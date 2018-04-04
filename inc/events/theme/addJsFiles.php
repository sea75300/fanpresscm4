<?php

/**
 * Module-Event: themeAddJsFiles
 * 
 * Event wird ausgeführt, wenn in acpController-View die Liste mit Javascript-Dateien geladen wird
 * Parameter: array Liste mit Javascript-Dateien
 * Rückgabe: array Liste mit Javascript-Dateien
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\events\theme;

/**
 * Module-Event: themeAddJsFiles
 * 
 * Event wird ausgeführt, wenn in acpController-View die Liste mit Javascript-Dateien geladen wird
 * Parameter: array Liste mit Javascript-Dateien
 * Rückgabe: array Liste mit Javascript-Dateien
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm/model/events
 */
final class themeAddJsFiles extends \fpcm\events\abstracts\event {

    /**
     * wird ausgeführt, wenn in acpController-View die Liste mit Javascript-Dateien geladen wird
     * @param array $data
     * @return array
     */
    public function run()
    {

        $eventClasses = $this->getEventClasses();

        if (!count($eventClasses))
            return [];

        $mdata = [];
        foreach ($eventClasses as $eventClass) {

            $classkey = $this->getModuleKeyByEvent($eventClass);
            $eventClass = \fpcm\model\abstracts\module::getModuleEventNamespace($classkey, 'themeAddJsFiles');

            /**
             * @var \fpcm\events\event
             */
            $module = new $eventClass();

            if (!$this->is_a($module))
                continue;

            $mdata = $module->run($mdata);
        }

        return $mdata;
    }

}
