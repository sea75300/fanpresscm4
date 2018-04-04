<?php

/**
 * System update finalizer object
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\updater;

/**
 * System Update Finalizer Objekt
 * 
 * @package fpcm\model\updater
 * @author Stefan Seehafer <sea75300@yahoo.de>
 */
final class finalizer extends \fpcm\model\abstracts\model {

    /**
     * Initialisiert System Update
     * @param int $init
     */
    public function __construct()
    {
        parent::__construct();

        $this->dbcon = new \fpcm\classes\database();
        $this->config = new \fpcm\model\system\config(false, false);
    }

    /**
     * Führt abschließende Update-Schritte aus
     * @return bool
     */
    public function runUpdate()
    {
        $res    = true &&
                $this->createTables() &&
                $this->alterTables() &&
                $this->removeSystemOptions() &&
                $this->addSystemOptions() &&
                $this->updateSystemOptions() &&
                $this->updatePermissions() &&
                $this->checkFilesystem() &&
                $this->updateVersion() &&
                $this->optimizeTables();

        return $res;
    }

    /**
     * aktualisiert Versionsinfos in Datenbank
     * @return bool
     */
    private function updateVersion()
    {
        $this->config->setNewConfig([
            'system_version' => \fpcm\classes\baseconfig::getVersionFromFile()
        ]);

        return $this->config->update();
    }

    /**
     * aktualisiert Berechtigungen
     * @return boolean
     */
    private function updatePermissions()
    {
        return true;
    }

    /**
     * neue System-Optionen bei Update erzeugen
     * @return bool
     */
    private function addSystemOptions()
    {
        return true;
    }

    /**
     * System-Optionen bei Update aktualisieren
     * @return bool
     */
    private function updateSystemOptions()
    {
        $newConfig = [];

        if (is_numeric($this->config->system_editor)) {

            $editors = [
                0 => '\fpcm\components\editor\tinymceEditor',
                1 => '\fpcm\components\editor\htmlEditor'
            ];

            $newConfig['system_editor'] = isset($editors[$this->config->system_editor]) ? $editors[$this->config->system_editor] : $editors[0];
        }

        if (!count($newConfig)) {
            return true;
        }

        $this->config->setNewConfig($newConfig);
        return $this->config->update();
    }

    /**
     * System-Optionen bei Update aktualisieren
     * @return bool
     */
    private function removeSystemOptions()
    {
        $res = true;
        if ($this->config->articles_trash) {
            $res = $res && $this->config->remove('articles_trash');
        }
        
        return $res;
    }

    /**
     * Änderungen an Tabellen-Struktur vornehmen
     * @return bool
     */
    private function alterTables()
    {
        $res = $this->dbcon->alter(\fpcm\classes\database::tableAuthors, 'DROP', 'salt', '', true);
        return $res;
    }

    /**
     * Neue Tabelle erzeugen
     * @return bool
     */
    private function createTables()
    {
        return true;
    }

    /**
     * Prüfung von Dateisystem-Strukturen
     * @return bool
     */
    private function checkFilesystem()
    {
        return true;
    }

    /**
     * Führt Optimierung der Datenbank-Tabellen durch
     * @since FPCM 3.3
     * @return boolean
     */
    private function optimizeTables()
    {
        $tables = [];
        $tables[] = \fpcm\classes\database::tableArticles;
        $tables[] = \fpcm\classes\database::tableAuthors;
        $tables[] = \fpcm\classes\database::tableCategories;
        $tables[] = \fpcm\classes\database::tableComments;
        $tables[] = \fpcm\classes\database::tableConfig;
        $tables[] = \fpcm\classes\database::tableCronjobs;
        $tables[] = \fpcm\classes\database::tableFiles;
        $tables[] = \fpcm\classes\database::tableIpAdresses;
        $tables[] = \fpcm\classes\database::tableModules;
        $tables[] = \fpcm\classes\database::tablePermissions;
        $tables[] = \fpcm\classes\database::tableRoll;
        $tables[] = \fpcm\classes\database::tableSessions;
        $tables[] = \fpcm\classes\database::tableSmileys;
        $tables[] = \fpcm\classes\database::tableTexts;
        $tables[] = \fpcm\classes\database::tableRevisions;

        $tables = $this->events->trigger('updaterAddOptimizeTables', $tables);
        foreach ($tables as $table) {
            $this->dbcon->optimize($table);
        }

        return true;
    }

    /**
     * Prüft System-Version auf bestimmten Wert
     * @param string $version
     * @param string $option
     * @return bool
     * @since FPCM 3.2
     */
    private function checkVersion($version, $option = '<')
    {
        return version_compare($this->config->system_version, $version, $option);
    }

}

?>