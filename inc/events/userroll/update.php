<?php
    /**
     * Module-Event: userrollUpdate
     * 
     * Event wird ausgeführt, wenn Daten einer Benutzerrolle aktualisiert werden
     * Parameter: array Daten der Benutzerrolle
     * Rückgabe: array Daten der Benutzerrolle
     * 
     * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
     * @copyright (c) 2011-2018, Stefan Seehafer
     * @license http://www.gnu.org/licenses/gpl.txt GPLv3
     */
    namespace fpcm\events\userroll;

    /**
     * Module-Event: userrollUpdate
     * 
     * Event wird ausgeführt, wenn Daten einer Benutzerrolle aktualisiert werden
     * Parameter: array Daten der Benutzerrolle
     * Rückgabe: array Daten der Benutzerrolle
     * 
     * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
     * @copyright (c) 2011-2018, Stefan Seehafer
     * @license http://www.gnu.org/licenses/gpl.txt GPLv3
     * @package fpcm/model/events
     */
    final class userrollUpdate extends \fpcm\model\abstracts\event {

        /**
         * wird ausgeführt, wenn Daten einer Benutzerrolle aktualisiert werden
         * @param array $data
         * @return array
         */
        public function run($data = null) {
            
            $eventClasses = $this->getEventClasses();
            
            if (!count($eventClasses)) return $data;
            
            $mdata = $data;
            foreach ($eventClasses as $eventClass) {
                
                $classkey = $this->getModuleKeyByEvent($eventClass);                
                $eventClass = \fpcm\model\abstracts\module::getModuleEventNamespace($classkey, 'userrollUpdate');
                
                /**
                 * @var \fpcm\model\abstracts\event
                 */
                $module = new $eventClass();

                if (!$this->is_a($module)) continue;
                
                $mdata = $module->run($mdata);
            }
            
            if (!$mdata) return $data;
            
            return $mdata;
            
        }
    }