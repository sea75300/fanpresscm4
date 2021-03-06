<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\action\system;

/**
 * Backup manager controller
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class backups extends \fpcm\controller\abstracts\controller implements \fpcm\controller\interfaces\isAccessible {

    use \fpcm\controller\traits\common\dataView;
    
    protected $i = 0;

    protected $deletePrevent = 5;

    /**
     * 
     * @return bool
     */
    public function isAccessible(): bool
    {
        return $this->permissions->system->backups;
    }

    protected function getHelpLink()
    {
        return 'HL_BACKUPS';
    }

    public function request()
    {
        if (!$this->buttonClicked('delete')) {
            return true;
        }
            
        $deleteFile = $this->request->fromPOST('files', [
            \fpcm\model\http\request::FILTER_URLDECODE,
            \fpcm\model\http\request::FILTER_BASE64DECODE,
            \fpcm\model\http\request::FILTER_DECRYPT
        ]);

        $file = new \fpcm\model\files\dbbackup($deleteFile);
        if (!$file->exists()) {
            $this->view->addErrorMessage('GLOBAL_NOTFOUND_FILE');
            return true;
        }

        if (!$file->isValidDataFolder('', \fpcm\classes\dirs::DATA_DBDUMP) || !$file->delete()) {
            $this->view->addErrorMessage('DELETE_FAILED_FILES', [
                '{{filenames}}' => $deleteFile
            ]);

            return true;
        }

        $this->view->addNoticeMessage('DELETE_SUCCESS_FILES', [
            '{{filenames}}' => $deleteFile
        ]);

        return true;
    }

    /**
     * Controller-Processing
     */
    public function process()
    {
        $isPg = \fpcm\classes\loader::getObject('\fpcm\classes\database')->getDbtype() === \fpcm\classes\database::DBTYPE_POSTGRES;
        
        $this->view->addButton((new \fpcm\view\helper\deleteButton('delete'))->setClass('fpcm-ui-button-confirm')->setReadonly($isPg));
        $this->view->addJsFiles(['backups.js']);

        $this->view->assign('headline', 'HL_BACKUPS');
        if ($isPg) {
            $this->view->addErrorMessage('BACKUPS_NOTICE_POSTGRES');
            $this->items = [];
            $this->initDataView();
            $this->view->render();
            return true;
        }        
        
        
        $folderList = new \fpcm\model\files\backuplist();
        $this->items = $folderList->getFolderList();
        rsort($this->items);
        $this->initDataView();
        $this->view->setFormAction('system/backups');
        $this->view->addTopDescription(
            'BACKUPS_TOP_DESCRIPTION',
            ['path' => \fpcm\model\files\ops::removeBaseDir(\fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_DBDUMP), true)]
        );

        $this->view->render();
    }

    protected function getDataViewCols()
    {
        return [
            (new \fpcm\components\dataView\column('select', ''))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('name', 'FILE_LIST_FILENAME'))->setSize(7),
            (new \fpcm\components\dataView\column('size', 'FILE_LIST_FILESIZE'))->setSize(3),
        ];
    }

    protected function getDataViewName()
    {
        return 'backups';
    }

    /**
     * 
     * @param string $file
     * @return \fpcm\components\dataView\row
     */
    protected function initDataViewRow($file)
    {
        $basename = basename($file);
        $hash = md5($basename);
        
        $val = urlencode(base64_encode( $this->crypt->encrypt($basename)));

        $url = \fpcm\classes\tools::getFullControllerLink('system/backups', [
            'save' => $val
        ]);

        $this->i++;
        
        return new \fpcm\components\dataView\row([
            new \fpcm\components\dataView\rowCol('select', (new \fpcm\view\helper\radiobutton('files', 'files'.$hash))->setValue($val)->setReadonly($this->i > $this->deletePrevent ? false : true), '', \fpcm\components\dataView\rowCol::COLTYPE_ELEMENT),
            new \fpcm\components\dataView\rowCol('name', $basename),
            new \fpcm\components\dataView\rowCol('size', \fpcm\classes\tools::calcSize(filesize($file)) ),
        ]);
    }

    
}

?>
