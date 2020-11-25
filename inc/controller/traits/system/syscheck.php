<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\traits\system;

/**
 * System check trait
 * 
 * @package fpcm\controller\traits\system\syscheck
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
trait syscheck {

    /**
     * System-Check ausführen
     * @return array
     */
    protected function getCheckOptionsSystem()
    {
        $checkOptions = [];

        $loadedExtensions = array_map('strtolower', get_loaded_extensions());

        if (!\fpcm\classes\baseconfig::installerEnabled() && \fpcm\classes\baseconfig::dbConfigExists()) {
            $updater = new \fpcm\model\updater\system();
            $updater->updateAvailable();

            $remoteVersion = $updater->version;

            $result = version_compare($this->config->system_version, $remoteVersion, '>=');
            $option = new \fpcm\model\system\syscheckOption(
                $this->config->system_version,
                'https://nobody-knows.org/download/fanpress-cm/',
                $result
            );
            
            if (!$result) {
                
                if (\fpcm\classes\baseconfig::isCli()) {
                    $option->setNotice('You may run       : php '.\fpcm\classes\dirs::getFullDirPath('fpcmcli.php').' pkg --upgrade system');
                }
                elseif ($this->permissions->system->update) {
                    $button = new \fpcm\view\helper\linkButton('startUpdate');
                    $button->setReturned(true)->setIcon('sync')->setUrl(\fpcm\classes\tools::getFullControllerLink('package/sysupdate'))->setText('PACKAGES_UPDATE');
                    $option->setActionButton($button);
                }
                
            }
            
            $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_FPCMVERSION', [
                'value' => $remoteVersion ? $remoteVersion : $this->language->translate('GLOBAL_NOTFOUND')])
            ] = $option;       
            
        }
        
        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_PHPVERSION', [
            'value' => FPCM_PHP_REQUIRED])
        ] = new \fpcm\model\system\syscheckOption(
            phpversion(),
            'http://php.net/',
            version_compare(phpversion(), FPCM_PHP_REQUIRED, '>=')
        );

        $recomVal   = 64;
        $curVal     = \fpcm\classes\baseconfig::memoryLimit();
        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_PHPMEMLIMIT', [
            'value' => $recomVal . ' MiB'])
        ] = new \fpcm\model\system\syscheckOption(
            $curVal . ' MiB',
            'http://php.net/manual/info.configuration.php',
            ($curVal >= $recomVal ? true : false),
            true
        );

        $recomVal   = 10;
        $curVal     = ini_get('max_execution_time');
        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_PHPMAXEXECTIME', [
            'value' => $recomVal . 'sec'])
        ] = new \fpcm\model\system\syscheckOption(
            $curVal . 'sec',
            'http://php.net/manual/info.configuration.php',
            ($curVal >= $recomVal ? true : false),
            true
        );

        $dbDrivers      = \PDO::getAvailableDrivers();
        $resultMySql    = in_array(\fpcm\classes\database::DBTYPE_MYSQLMARIADB, $dbDrivers);
        $resultPgSql    = in_array(\fpcm\classes\database::DBTYPE_POSTGRES, $dbDrivers);
        $sqlhelp        = 'http://php.net/manual/pdo.getavailabledrivers.php';

        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_DBDRV_MYSQL', [
            'value' => 'true'])
        ] = new \fpcm\model\system\syscheckOption(
            $resultMySql ? 'true' : 'false',
            $sqlhelp,
            $resultMySql,
            (!$resultMySql && $resultPgSql ? 1 : 0)
        );

        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_DBDRV_PGSQL', [
            'value' => 'true'])
        ] = new \fpcm\model\system\syscheckOption(
            $resultPgSql ? 'true' : 'false',
            $sqlhelp,
            $resultPgSql,
            ($resultMySql ? 1 : 0)
        );

        $db = \fpcm\classes\loader::getObject('\fpcm\classes\database');
        if (\fpcm\classes\baseconfig::dbConfigExists() && is_object($db)) {

            $recommend = implode('/', array_intersect($dbDrivers, array_keys(\fpcm\classes\database::$supportedDBMS)));

            $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_DBDRV_ACTIVE', [
                'value' => $recommend])
            ] = new \fpcm\model\system\syscheckOption(
                $db->getDbtype(),
                'http://php.net/manual/pdo.getavailabledrivers.php',
                true
            );

            $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_DBVERSION', [
                'value' => $db->getRecommendVersion()])
            ] = new \fpcm\model\system\syscheckOption(
                $db->getDbVersion(),
                'http://php.net/manual/pdo.getattribute.php',
                $db->checkDbVersion()
            );
        }

        $current = in_array('pdo', $loadedExtensions) && in_array('pdo_mysql', $loadedExtensions);
        $checkOptions['PHP Data Objects (PDO)'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/en/class.pdo.php',
            (true && $current)
        );

        $current = (CRYPT_SHA256 == 1 ? true : false);
        $current = $current && in_array(\fpcm\classes\security::defaultHashAlgo, hash_algos());
        $checkOptions['SHA256 Hash Algorithm'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/function.hash-algos.php',
            (true && $current)
        );

        $current = in_array('gd', $loadedExtensions);
        $checkOptions['GD Lib'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/book.image.php',
            (true && $current)
        );

        $current = in_array('json', $loadedExtensions);
        $checkOptions['JSON'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/book.json.php',
            (true && $current)
        );

        $current = in_array('xml', $loadedExtensions) && in_array('simplexml', $loadedExtensions) && class_exists('DOMDocument');
        $checkOptions['XML, SimpleXML, DOMDocument'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/class.simplexmlelement.php',
            (true && $current)
        );

        $current = in_array('zip', $loadedExtensions);
        $checkOptions['ZipArchive'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/class.ziparchive.php',
            (true && $current)
        );

        $current = in_array('openssl', $loadedExtensions) && defined('OPENSSL_ALGO_SHA512');
        $checkOptions['OpenSSL'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/book.openssl.php',
            (true && $current)
        );

        $current = in_array('curl', $loadedExtensions);
        $checkOptions['cURL (' . $this->language->translate('GLOBAL_OPTIONAL') . ')'] = new \fpcm\model\system\syscheckOption(
            'true',
            'http://php.net/manual/book.curl.php',
            (true && $current),
            true
        );

        $externalCon = \fpcm\classes\baseconfig::canConnect();
        $checkOptions['allow_url_fopen = 1 (' . $this->language->translate('GLOBAL_OPTIONAL') . ')'] = new \fpcm\model\system\syscheckOption(
            $externalCon ? 'true' : 'false',
            'http://php.net/manual/filesystem.configuration.php#ini.allow-url-fopen',
            (true && $externalCon),
            true
        );

        $https = \fpcm\classes\baseconfig::canHttps();
        $checkOptions[$this->language->translate('SYSTEM_OPTIONS_SYSCHECK_HTTPS') . ' (' . $this->language->translate('GLOBAL_OPTIONAL') . ')']
            = new \fpcm\model\system\syscheckOption(
                $https ? 'true' : 'false',
                'http://php.net/manual/reserved.variables.server.php',
                (true && $https),
                true
            );

        $checkFolders = $this->getCheckFolders();
        foreach ($checkFolders as $description => $folderPath) {
            $current = is_writable($folderPath);
            $pathOutput = \fpcm\model\files\ops::removeBaseDir($folderPath, true);
            $checkOptions['<i>' . $description . '</i> ' . $pathOutput] = new \fpcm\model\system\syscheckOption(
                $current ? 'true' : 'false',
                '',
                (true && $current),
                false,
                true
            );

        }

        return $checkOptions;
    }

    /**
     * Check folders
     * @return array
     */
    public function getCheckFolders()
    {
        $checkFolders = array(
            $this->language->translate('SYSCHECK_FOLDER_DATA') => \fpcm\classes\dirs::getDataDirPath(''),
            $this->language->translate('SYSCHECK_FOLDER_CACHE') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_CACHE),
            $this->language->translate('SYSCHECK_FOLDER_CONFIG') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_CONFIG),
            $this->language->translate('SYSCHECK_FOLDER_FILEMANAGER') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_FMTMP),
            $this->language->translate('SYSCHECK_FOLDER_LOGS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_LOGS),
            $this->language->translate('SYSCHECK_FOLDER_MODULES') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_MODULES),
            $this->language->translate('SYSCHECK_FOLDER_OPTIONS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_OPTIONS),
            $this->language->translate('SYSCHECK_FOLDER_SHARE') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_SHARE),
            $this->language->translate('SYSCHECK_FOLDER_SMILEYS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_SMILEYS),
            $this->language->translate('SYSCHECK_FOLDER_STYLES') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_STYLES),
            $this->language->translate('SYSCHECK_FOLDER_TEMP') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_TEMP),
            $this->language->translate('SYSCHECK_FOLDER_UPLOADS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_UPLOADS),
            $this->language->translate('SYSCHECK_FOLDER_DBDUMPS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_DBDUMP),
            $this->language->translate('SYSCHECK_FOLDER_DRAFTS') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_DRAFTS),
            $this->language->translate('SYSCHECK_FOLDER_PROFILES') => \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_PROFILES),
        );

        natsort($checkFolders);

        return $checkFolders;
    }

}

?>