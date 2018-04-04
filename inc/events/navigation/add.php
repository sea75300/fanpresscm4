<?php

/**
 * Module-Event: navigationAdd
 * 
 * Event wird ausgeführt, wenn Navigation erzeugt wird
 * Parameter: array mit Standard-Navigationspunkten
 * Rückgabe: array mit Navigationsstruktur
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\events\navigation;

/**
 * Module-Event: navigationAdd
 * 
 * Event wird ausgeführt, wenn Navigation erzeugt wird
 * Parameter: array mit Standard-Navigationspunkten
 * Rückgabe: array mit Navigationsstruktur
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm/model/events
 */
final class navigationAdd extends \fpcm\events\abstracts\event {

    /**
     * wird ausgeführt, wenn Navigation erzeugt wird
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
            $eventClass = \fpcm\model\abstracts\module::getModuleEventNamespace($classkey, 'navigationAdd');

            /**
             * @var \fpcm\events\event
             */
            $module = new $eventClass();

            if (!$this->is_a($module))
                continue;

            $mdata = $module->run($mdata);

            /* @var $mdata \fpcm\model\theme\navigationItem */
            if (is_object($mdata) && is_a($mdata, '\\fpcm\\model\\theme\\navigationItem')) {
                $data[$mdata->getParent()][] = $mdata;
                $mdata = $data;
            }
        }

        if (!$mdata)
            return $data;

        return $mdata;
    }

}
