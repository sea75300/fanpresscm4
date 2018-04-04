<?php

/**
 * Module-Event: permissionsGetAll
 * 
 * Event wird ausgeführt, nachdem Berechtigungsinformationen aus Datenbank abgerufen wurden
 * Parameter: array Berechtigungsinformationen aus Datenbank
 * Rückgabe: array Berechtigungsinformationen
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\events\permission;

/**
 * Module-Event: permissionsGetAll
 * 
 * Event wird ausgeführt, nachdem Berechtigungsinformationen aus Datenbank abgerufen wurden
 * Parameter: array Berechtigungsinformationen aus Datenbank
 * Rückgabe: array Berechtigungsinformationen
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm/model/events
 */
final class permissionsGetAll extends \fpcm\events\abstracts\event {

    /**
     * wird ausgeführt, nachdem Berechtigungsinformationen aus Datenbank abgerufen wurden
     * @param array $data
     * @return array
     */
    public function run()
    {

        $eventClasses = $this->getEventClasses();

        if (!count($eventClasses))
            return $data;

        $mdata = $data;
        foreach ($eventClasses as $eventClass) {

            $classkey = $this->getModuleKeyByEvent($eventClass);
            $eventClass = \fpcm\model\abstracts\module::getModuleEventNamespace($classkey, 'permissionsGetAll');

            /**
             * @var \fpcm\events\event
             */
            $module = new $eventClass();

            if (!$this->is_a($module))
                continue;

            $mdata = $module->run($mdata);
        }

        if (!$mdata)
            return $data;

        return $mdata;
    }

}
