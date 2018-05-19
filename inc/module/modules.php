<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\module;

/**
 * Modules list
 * 
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm\module
 */
class modules extends \fpcm\model\abstracts\tablelist {

    /**
     *
     * @var array
     */
    private $enabledCache;

    /**
     *
     * @var array
     */
    private $keyCache;

    public function __construct()
    {
        $this->table = \fpcm\classes\database::tableModules;
        $this->dbcon = \fpcm\classes\loader::getObject('\fpcm\classes\database');
        
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return false;
        }

        $this->events = \fpcm\classes\loader::getObject('\fpcm\events\events');
        $this->config = \fpcm\classes\loader::getObject('\fpcm\model\system\config');
    }

    /**
     * 
     * @return array
     */
    public function getKeysFromDatabase()
    {
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return [];
        }
        
        if (is_array($this->keyCache)) {
            return $this->keyCache;
        }

        $this->keyCache = [];

        $result = $this->dbcon->fetch($this->dbcon->select($this->table, 'mkey'), true);
        if (!$result) {
            return $this->keyCache;
        }

        foreach ($result as $dataset) {
            $this->keyCache[] = $dataset->mkey;
        }

        return $this->keyCache;
    }

    /**
     * 
     * @return array
     */
    public function getFromDatabase()
    {
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return [];
        }

        $result = $this->dbcon->fetch($this->dbcon->select($this->table, '*'), true);
        if (!$result) {
            return [];
        }

        $modules = [];
        foreach ($result as $dataset) {
            $this->createResult($dataset, $modules);
        }

        return $modules;
    }

    /**
     * 
     * @return array
     */
    public function getInstalledDatabase()
    {
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return [];
        }

        $result = $this->dbcon->fetch($this->dbcon->select($this->table, '*', 'installed = 1'), true);
        if (!$result) {
            return [];
        }

        $modules = [];
        foreach ($result as $dataset) {
            $this->createResult($dataset, $modules);
        }

        return $modules;
    }

    /**
     * 
     * @return array
     */
    public function getInstalledUpdates()
    {
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return 0;
        }

        $installed = $this->getInstalledDatabase();
        if (!count($installed)) {
            return 0;
        }

        $list = [];
        foreach ($installed as $key => $module) {
            
            if (!$module->hasUpdates()) {
                continue;
            }
            
            $list[] = $key;
            
        }

        return $list;
    }

    /**
     * 
     * @return array
     */
    public function getFromRepository()
    {
        $repoData = (new \fpcm\model\updater\modules())->getData();
        
        if (!is_array($repoData) || !count($repoData)) {
            return [];
        }
        
        $modules = [];
        foreach ($repoData as $key => $value) {
            
            $module = new repoModule($key, false);
            $module->createFromRepoArray([
                'name' => $value['name'],
                'description' => isset($value['description']) ? $value['description'] : '',
                'version' => isset($value['version']) ? $value['version'] : '',
                'author' => isset($value['author']) ?$value['author'] : '',
                'link' => isset($value['link']) ?$value['link'] : '',
                'requirements' => isset($value['requirements']) ? $value['requirements'] : []
            ]);
            
            $modules[$key] = $module;
        }
        
        
        return $modules;
    }

    /**
     * 
     * @return boolean
     */
    public function updateFromFilesystem()
    {
        $folders = glob(\fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_MODULES, '*/*'), GLOB_ONLYDIR);
        if (!$folders) {
            return true;
        }

        $dbList = $this->getFromDatabase();
        foreach ($folders as $folder) {
            $key = module::getKeyFromPath($folder);
            $module = new module( $key, false );
            if (isset($dbList[$key])) {
                continue;
            }

            if (!$module->addModule()) {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @return array
     */
    public function getEnabledDatabase()
    {
        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return [];
        }

        if (is_array($this->enabledCache)) {
            return $this->enabledCache;
        }

        $this->enabledCache = [];

        $result = $this->dbcon->fetch($this->dbcon->select($this->table, 'mkey', 'installed = 1 AND active = 1'), true);
        if (!$result) {
            return $this->enabledCache;
        }

        foreach ($result as $dataset) {
            $this->enabledCache[] = $dataset->mkey;
        }

        return $this->enabledCache;
    }

    /**
     * 
     * @param object $dataset
     * @param array $modules
     * @return boolean
     */
    private function createResult($dataset, array &$modules)
    {
        $module = new module($dataset->mkey, false);
        $module->createFromDbObject($dataset);
        $modules[$dataset->mkey] = $module;
        return true;
    }

}
