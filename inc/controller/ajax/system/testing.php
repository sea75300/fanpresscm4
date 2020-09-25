<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\ajax\system;

/**
 * AJAX testing controller
 * 
 * @package fpcm\controller\ajax\system\refresh
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2019, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @ignore
 */
class testing extends \fpcm\controller\abstracts\ajaxController implements \fpcm\controller\interfaces\isAccessible {

    public function isAccessible(): bool
    {
        return true;
    }

    /**
     * @see \fpcm\controller\abstracts\controller::hasAccess()
     * @return bool
     */
    public function hasAccess()
    {
        return defined('FPCM_DEBUG') && FPCM_DEBUG;
    }
    
    /**
     * Controller-Processing
     */
    public function process()
    {
        $current = $this->request->fromPOST('current', [
            \fpcm\model\http\request::FILTER_CASTINT
        ]);

        $next = (bool) $this->request->fromPOST('next', [
            \fpcm\model\http\request::FILTER_CASTINT
        ]);

        $fpath = \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_OPTIONS, 'import.csv');
        $handle = fopen($fpath, 'r');

        $progressObj = new \fpcm\model\system\progress(function (&$data, &$current, $next) use (&$handle) {

            $line = fgetcsv($handle);
            if (is_array($line) && count($line)) {
                $data['lines'][]  = $line;
            }

            $current = ftell($handle);
            usleep(2500);

            return !feof($handle) ? true : false;
        });

        $progressObj->setNext($next)->setData([
            'fs' => filesize($fpath),
            'lines' => []
        ]);

        if (!is_resource($handle)) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        if (!$progressObj->getNext()) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        if (fseek($handle, $current) === -1) {
            $this->response->setReturnData($progressObj)->fetch();
        }

        $progressObj->setCurrent($current)->setNext(!feof($handle));
        
        $progressObj->process();
        $progressObj->setNext(!feof($handle));

        fclose($handle);

        $this->response->setReturnData($progressObj)->fetch();

    }


}

?>