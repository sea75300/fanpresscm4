<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\ajax\system;

/**
 * AJAX import controller
 * 
 * @package fpcm\controller\ajax\system\refresh
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class import extends \fpcm\controller\abstracts\ajaxController implements \fpcm\controller\interfaces\isAccessible {

    /**
     * 
     * @var \fpcm\model\articles\article //\fpcm\model\abstracts\dataset
     */
    private $instance;

    /**
     * 
     * @var \fpcm\model\files\fileOption
     */
    private $opt;

    private $item;

    private $current;
    
    private $next;
    
    private $file;

    private $delim;

    private $enclosure;

    private $skipfirst;

    private $fields;

    private $unique;
    
    private $responseData = [];

    public function isAccessible(): bool
    {
        return $this->permissions->system->options;
    }
    
    public function request()
    {
        $this->current = $this->request->fromPOST('current', [
            \fpcm\model\http\request::FILTER_CASTINT
        ]);

        $this->next = (bool) $this->request->fromPOST('next', [
            \fpcm\model\http\request::FILTER_CASTINT
        ]);

        $this->unique = $this->request->fromPOST('unique');
        if (!trim($this->unique)) {
            $this->response->setReturnData(new \fpcm\view\message(
                'Fehler beim initialisieren.',
                \fpcm\view\message::TYPE_ERROR,
                \fpcm\view\message::ICON_ERROR,
                '',
                true
            ))->fetch();
        }

        $this->opt = new \fpcm\model\files\fileOption('csv/'.$this->unique);
        $csvParams = $this->opt->read() !== null ? (array) $this->opt->read() : $this->request->fromPOST('csv');
        $this->opt->write($csvParams);

        $this->item = filter_var($csvParams['item'] ?? null, FILTER_SANITIZE_STRING);

        $this->file = basename(filter_var($csvParams['file'] ?? null, FILTER_SANITIZE_STRING), '.csv');

        $this->delim = substr(filter_var($csvParams['delim'] ?? null, FILTER_SANITIZE_STRING), 0, 1);
        if (!in_array($this->delim, [';', ','])) {
            $this->delim = ';';
        }
        
        $this->enclosure = substr(filter_var($csvParams['enclo'] ?? null, FILTER_SANITIZE_STRING), 0, 1);
        if (!in_array($this->enclosure, ['"', '\''])) {
            $this->enclosure = '"';
        }
        
        $this->skipfirst = $csvParams['skipfirst'] ?? false;
        
        $this->fields = $csvParams['fields'] ?? [];
        if (!count($this->fields)) {
            $this->response->setReturnData(new \fpcm\view\message(
                'Keine Felder zugewiesen.',
                \fpcm\view\message::TYPE_ERROR,
                \fpcm\view\message::ICON_ERROR,
                '',
                true
            ))->fetch();
        }

        $this->initImportItem();
        return true;
    }
    
    /**
     * Controller-Processing
     */
    public function process()
    {
        $this->responseData = [
            'fs' => 0,
        ];
        
        $csv = new \fpcm\model\files\csvFile($this->file, $this->delim, $this->enclosure);        
        
        if ( !$csv->exists() || !$csv->isValidDataFolder('', \fpcm\classes\dirs::DATA_TEMP ) ) {
            $this->response->setReturnData(new \fpcm\view\message(
                'Die CSV-Datei wurde nicht gefunden!',
                \fpcm\view\message::TYPE_ERROR,
                \fpcm\view\message::ICON_ERROR,
                '',
                true
            ))->fetch();
        }

        if ( \fpcm\model\files\csvFile::isValidType($csv->getExtension(), $csv->getMimeType())  ) {
            $this->response->setReturnData(new \fpcm\view\message(
                'Übermittelte Datei ist ungültig!',
                \fpcm\view\message::TYPE_ERROR,
                \fpcm\view\message::ICON_ERROR,
                '',
                true
            ))->fetch();            
        }

        $i = 0;
        
        $progressObj = new \fpcm\model\system\progress(function (&$data, &$current, $next, &$stop) use (&$csv, &$i) {

            if ($current >= $data['fs']) {
                $stop = true;
                return false;
            }

            usleep(10000);

            $i++;
            
            $line = $csv->getContent();
            
            if ($this->skipfirst && $i < 2) {
                $current = $csv->tell();
                usleep(2000);

                return !$csv->isEoF() ? true : false;
            }
            
            if ($csv->assignCsvFields($this->fields, $line) === false) {

                $current = $csv->tell();

                $this->response->setReturnData(new \fpcm\view\message(
                    'Übermittelte Datei ist ungültig!',
                    \fpcm\view\message::TYPE_ERROR,
                    \fpcm\view\message::ICON_ERROR,
                    '',
                    true
                ))->fetch();

            }
            
            $this->instance->assignCsvRow($line);
            
            $current = $csv->tell();
            usleep(2000);

            return !$csv->isEoF() ? true : false;
        }, $this->unique);

        $progressObj->setNext($this->next)->setData($this->responseData);

        if (!$csv->hasResource()) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        $this->responseData['fs'] = $csv->getFilesize();
        $progressObj->setNext($this->next)->setData($this->responseData);

        if (!$progressObj->getNext()) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        if ($csv->seek($this->current) === -1) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        $progressObj->setCurrent($this->current)->setNext(!$csv->isEoF());
        $progressObj->process();
        
        if (!$progressObj->getStop()) {
            $progressObj->setNext(!$csv->isEoF());
        }
        else {
            $csv->delete();
            $this->opt->remove();
        }

        $csv = null;
        $this->opt = null;

        $this->response->setReturnData($progressObj)->fetch();

    }

    /**
     * 
     * @return bool
     */
    private function initImportItem() : bool
    {
        if ($this->instance instanceof \fpcm\model\interfaces\isCsvImportable) {
            return true;
        }

        $class = 'fpcm\\model\\'. str_replace('__', '\\', $this->item);
        if (!is_subclass_of($class, '\fpcm\model\interfaces\isCsvImportable')) {
            $this->response->setReturnData(new \fpcm\view\message('Ungültiger Typ: ' . $this->item, \fpcm\view\message::TYPE_ERROR ))->fetch();
        }
        
        $this->instance = new $class;
        return true;
    }


}

?>