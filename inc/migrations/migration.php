<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\migrations;

/**
 * Migration base class
 * 
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2019, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm\migrations
 * @since 4.3
 */
abstract class migration {

    /**
     * Database object
     * @var \fpcm\classes\database
     */
    private $db;

    /**
     * System config object
     * @var \fpcm\model\system\config
     */
    private $config;

    /**
     * CLI execution flag
     * @var bool
     */
    private $isCli = null;

    /**
     * Result of pre-executed migration, false if one failed
     * @var bool
     */
    protected $requiredResult = true;

    /**
     * Constructor method
     * @return boolean
     */
    final public function __construct()
    {
        $this->init();
        return true;
    }

    /**
     * Config object getter
     * @return \fpcm\model\system\config
     */
    final protected function getConfig() : \fpcm\model\system\config
    {
        if ( !($this->config instanceof \fpcm\model\system\config) ) {
            $this->config = new \fpcm\model\system\config(false);
        }

        return $this->config;
    }
    
    /**
     * Config object getter
     * @return \fpcm\model\system\config
     */
    final protected function getDB() : \fpcm\classes\database
    {
        if ( !($this->db instanceof \fpcm\classes\database) ) {
            $this->db = new \fpcm\classes\database();
        }

        return $this->db;
    }
    
    /**
     * Config object getter
     * @return \fpcm\model\system\config
     */
    final protected function isCli() : bool
    {
        if ( $this->isCli === null ) {
            $this->isCli = \fpcm\classes\baseconfig::isCli();
        }

        return $this->isCli;
    }

    /**
     * Migration execution required due to system version
     * @return bool
     */
    final public function isRequired() : bool
    {
        return version_compare($this->getPreviewsVersion(), $this->getNewVersion(), '<');
    }

    /**
     * return preview version string
     * @return string
     * @since 4.5.1-b1
     */
    protected function getPreviewsVersion() : string
    {
        return $this->getConfig()->system_version;
    }

    /**
     * Execute migrations
     * @return bool
     */
    final public function process() : bool
    {
        $cn = get_called_class();
        $this->output('Processing migration '.$cn);
        
        $dbType = $this->getDB()->getDbtype();
        if (!in_array($this->getDB()->getDbtype(), $this->onDatabase())) {
            $this->output('Skip migration '.$cn.' on '.$dbType.'...');
            return true;
        }

        if (method_exists($this->getDB(), 'transaction')) {
            $this->getDB()->transaction();
        }
        
        if (!$this->alterTablesAfter()) {
            return false;
        }

        if (!$this->updatePermissionsAfter()) {
            return false;
        }

        if (!$this->updateSystemConfig()) {
            return false;
        }

        if (!$this->updateFileSystem()) {
            return false;
        }

        if (method_exists($this->getDB(), 'commit')) {
            $this->getDB()->commit();
        }

        $this->output('Processing of migration '.$cn.' successful.');
        return true;
    }

    /**
     * 
     * Output of migration messages
     * @param string $str
     * @param bool $error
     * @return void
     * @since 4.3
     */
    final protected function output(string $str, $error = false)
    {
        if ($error) trigger_error($str);
        else fpcmLogSystem($str);

        if (!$this->isCli) {
            return;
        }

        if (!class_exists('\fpcm\model\cli\io')) {
            print $str.PHP_EOL;
            return;
        }

        \fpcm\model\cli\io::output($str);
    }

    /**
     * Returns new version, e. g. from version.txt
     * @return string
     */
    protected function getNewVersion() : string
    {
        return \fpcm\classes\baseconfig::getVersionFromFile();
    }

    /**
     * Pre-Initializing
     * @return bool
     */
    protected function init() : bool
    {
        return true;
    }

    /**
     * Execute additional database table changes
     * @return bool
     */
    protected function alterTablesAfter() : bool
    {
        return true;
    }

    /**
     * Executes additional permission updates
     * @return bool
     */
    protected function updatePermissionsAfter() : bool
    {
        return true;
    }

    /**
     * Execute additional file system updates
     * @return bool
     */
    protected function updateFileSystem() : bool
    {
        return true;
    }

    /**
     * Execute additional system config updates
     * @return bool
     */
    protected function updateSystemConfig() : bool
    {
        return true;
    }

    /**
     * Returns a list of database driver names the migration should be executed to,
     * default is MySQL/ MariaDB and Postgres
     * @return array
     * @since 4.4.1
     */
    protected function onDatabase() : array
    {
        return [\fpcm\classes\database::DBTYPE_MYSQLMARIADB, \fpcm\classes\database::DBTYPE_POSTGRES];
    }

    /**
     * Returns migration class namespace
     * @param string $version
     * @return string
     * @static
     */
    public static function getNamespace(string $version) : string
    {
        return 'fpcm\\migrations\\v'.preg_replace('/([^0-9a-z])/i', '', $version);
    }

}
