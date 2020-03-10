<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\ajax\files;

/**
 * AJAX Controller to create new thumbnails
 * 
 * @package fpcm\controller\ajax\files
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class createThumbs extends \fpcm\controller\abstracts\ajaxControllerJSON implements \fpcm\controller\interfaces\isAccessible {

    /**
     *
     * @var array
     */
    private $files = [];

    /**
     *
     * @var array
     */
    private $success = [];

    /**
     *
     * @var array
     */
    private $failed = [];

    /**
     * 
     * @return bool
     */
    public function isAccessible(): bool
    {
        return $this->permissions->uploads->visible && $this->permissions->uploads->thumbs;
    }
    
    /**
     * Request-Handler
     * @return bool
     */
    public function request()
    {
        $this->response = new \fpcm\model\http\response;
        
        $this->files = $this->request->fromPOST('items', [
            \fpcm\model\http\request::FILTER_BASE64DECODE
        ]);

        if (!$this->files) {

            $this->response->setReturnData([new \fpcm\view\message(
                $this->language->translate('GLOBAL_NOTFOUND2', ''),
                \fpcm\view\message::TYPE_ERROR
            )])->fetch();

        }

        return true;
    }

    /**
     * Controller-Processing
     */
    public function process()
    {
        array_walk($this->files, [$this, 'createThumb']);

        $hasSuccess = count($this->success);
        $hasFailed = count($this->failed);
        if ($hasSuccess) {

            $this->returnData[] = new \fpcm\view\message(
                $this->language->translate('SUCCESS_FILES_NEWTHUMBS', [
                    '{{filenames}}' => implode(', ', $this->success)
                ]),
                \fpcm\view\message::TYPE_NOTICE
            );

        }

        if ($hasFailed) {
            
            $this->returnData[] = new \fpcm\view\message(
                    $this->language->translate('FAILED_FILES_NEWTHUMBS', [
                    '{{filenames}}' => implode(', ', $this->failed)
                ]),
                \fpcm\view\message::TYPE_ERROR
            );

        }

        if ($hasSuccess || $hasFailed) {
            $this->response->setReturnData($this->returnData)->fetch();
        }

        $this->response->setReturnData([new \fpcm\view\message(
            $this->language->translate('GLOBAL_NOTFOUND2', ''),
            \fpcm\view\message::TYPE_ERROR
        )])->fetch();

        $this->response->setReturnData($this->returnData)->fetch();
    }

    private function createThumb($fileName) : bool
    {
        if (!$fileName) {
            return false;
        }
        
        if ((new \fpcm\model\files\image($fileName, false))->createThumbnail()) {
            $this->success[] = $fileName;
            return true;
        }

        $this->failed[] = $fileName;
        return false;
    }

}

?>