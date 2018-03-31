<?php

/**
 * AJAX system updates controller
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\ajax\packagemgr;

/**
 * AJAX-Controller Paketmanager - System-Updater
 * 
 * @package fpcm\controller\ajax\packagemgr\sysupdater
 * @author Stefan Seehafer <sea75300@yahoo.de>
 */
class sysupdater extends \fpcm\controller\abstracts\ajaxController {

    /**
     * Auszuführender Schritt
     * @var int
     */
    protected $step;

    /**
     * allow_url_fopen = 1
     * @var bool
     */
    protected $canConnect;

    /**
     * Update-Package-Object
     * @var \fpcm\model\packages\update
     */
    protected $pkg;
    
    /**
     *
     * @var bool
     */
    protected $res = false;

    /**
     *
     * @var array
     */
    protected $pkgdata = [];

    /**
     * Version data file
     * @var \fpcm\model\files\tempfile
     */
    protected $versionDataFile = false;

    /**
     * 
     * @return array
     */
    protected function getPermissions()
    {
        return ['system' => 'update'];
    }

    /**
     * Request-Handler
     * @return boolean
     */
    public function request()
    {
        $this->step = 'exec'.ucfirst($this->getRequestVar('step'));
        return true;
    }

    /**
     * Controller-Processing
     */
    public function process()
    {
        if (!method_exists($this, $this->step)) {
            trigger_error('Update step '.$this->step.' not defined!');
            $this->returnData = [
                'code' => $this->res,
                'pkgdata' => $this->pkgdata
            ];

            $this->getSimpleResponse();
        }

        call_user_func([$this, $this->step]);
        $this->returnData = [
            'code' => $this->res,
            'pkgdata' => $this->pkgdata
        ];

        usleep(500000);
        $this->getSimpleResponse();
    }

    private function execMaintenanceOn()
    {
        $this->res = $this->config->setMaintenanceMode(true) && \fpcm\classes\baseconfig::enableAsyncCronjobs(false);
    }

    private function execMaintenanceOff()
    {
        $this->res = $this->config->setMaintenanceMode(false) && \fpcm\classes\baseconfig::enableAsyncCronjobs(true);
    }

    private function execCheckFiles()
    {
        $this->init();

        $success = $this->pkg->checkFiles();
        if ($success === \fpcm\model\packages\package::FILESCHECK_ERROR) {
            $this->addErrorMessage('UPDATE_WRITEERROR');
        }

        $this->res = $success === true ? true : false;

        if (!$this->res) {
            return false;
        }

        fpcmLogSystem('Local file system check was successful');
    }

    private function execDownload()
    {
        $this->init();
        
        if (!$this->pkg->isTrustedPath()) {
            $this->addErrorMessage('PACKAGES_FAILED_DOWNLOAD_UNTRUSTED', [
                '{{var}}' => $this->pkg->getRemotePath()
            ]);
            $this->res = false;
            return false;
        }
        
        $this->res = $this->pkg->download();
        if ($this->res === true) {
            fpcmLogSystem('Download of package '.$this->pkg->getRemotePath().' was successful.');
            return true;
        }

        $this->addErrorMessage('PACKAGES_FAILED_ERROR'.$this->res);
        \fpcm\classes\baseconfig::enableAsyncCronjobs(true);
        $this->res = false;
    }
    
    private function execCheckPkg()
    {
        $this->init();

        $this->res = $this->pkg->checkPackage();
        if ($this->res === true) {
            fpcmLogSystem('Package integity check for '.basename($this->pkg->getLocalPath()).' was successful.');
            return true;
        }

        \fpcm\classes\baseconfig::enableAsyncCronjobs(true);
        $this->res = false;
    }

    private function execExtract()
    {
        $this->init();

        $this->res = $this->pkg->extract();
        if ($this->res === true) {
            fpcmLogSystem('Package extraction for '.basename($this->pkg->getLocalPath()).' was successful.');
            return true;
        }

        $this->addErrorMessage('PACKAGES_FAILED_ERROR'.$this->res);
        \fpcm\classes\baseconfig::enableAsyncCronjobs(true);
        $this->res = false;
    }

    private function execUpdateFs()
    {
        $this->res = true;
        return;

        $this->res = $this->pkg->copy();

        $dest = \fpcm\model\files\ops::removeBaseDir(\fpcm\classes\dirs::getFullDirPath(''));
        $from = \fpcm\model\files\ops::removeBaseDir($this->pkg->getExtractPath());

        if ($this->res === true) {
            $this->syslog('Moved update package content successfully from ' . $from . ' to ' . $dest);
            return true;
        }

        $this->syslog('Error while moving update package content from ' . $from . ' to ' . $dest);
        $this->syslog(implode(PHP_EOL, $this->pkg->getCopyErrorPaths()));
    }

    private function execUpdateDb()
    {
        $finalizer = new \fpcm\model\updater\finalizer();
        $this->res = $finalizer->runUpdate();

        if ($this->res === true) {
            fpcmLogSystem('Run final update steps successfully!');
            return true;
        }

        fpcmLogSystem('Databse update failed. See error and database log for further information.');
    }
    
    private function execUpdateLog()
    {
        $this->res = true;
        return;
    }

    private function execCleanup()
    {
        $this->init();
        $this->res = $this->pkg->cleanup();
        \fpcm\classes\loader::getObject('\fpcm\classes\cache')->cleanup();
    }

    private function execGetVersion()
    {
        $this->pkgdata['version'] = $this->config->system_version;
        $this->res = true;
    }

    private function init()
    {
        $this->pkg = new \fpcm\model\packages\update(basename((new \fpcm\model\updater\system())->url));
    }

    /**
     * 
     * @param string $var
     */
    private function addErrorMessage($var, $params = [])
    {
        $this->pkgdata['errorMsg'] = $this->lang->translate($var, $params);
    }
}

?>