<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\events\abstracts;

/**
 * Event model base
 * 
 * @package fpcm\events\abstracts
 * @abstract
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
abstract class event implements \fpcm\model\interfaces\event {

    /**
     * Array returntype für Module-Event
     * @since FPCM 3.4
     */
    const FPCM_MODULE_EVENT_RETURNTYPE_ARRAY = 'array';

    /**
     * Object returntype für Module-Event
     * @since FPCM 3.4
     */
    const FPCM_MODULE_EVENT_RETURNTYPE_OBJ = 'object';

    /**
     * Object returntype für Module-Event
     * @since FPCM 4
     */
    const FPCM_MODULE_EVENT_RETURNTYPE_SCALAR = 'scalar';

    /**
     * Object returntype für Module-Event
     * @since FPCM 4
     */
    const FPCM_MODULE_EVENT_RETURNTYPE_VOID = null;

    /**
     * Base instaces a module event has to implement
     */
    const EVENT_BASE_INSTANCE = '\\fpcm\\events\\abstracts\\moduleEvent';

    /**
     * Event-Daten
     * @var array
     */
    protected $data;

    /**
     * Events mit aktuellem Event
     * @var array
     */
    protected $modules;

    /**
     * Liste mit aktiven Modulen
     * @var array
     */
    protected $activeModules = [];

    /**
     * Event-Cache
     * @var \fpcm\classes\cache
     */
    protected $cache;

    /**
     * Berechtigungen
     * @var \fpcm\model\system\permissions
     */
    protected $permissions;

    /**
     * Konstruktor
     * @param mixed $dataParams
     * @return boolean
     */
    public function __construct($dataParams = null)
    {
        $this->data = $dataParams;
        $this->cache = \fpcm\classes\loader::getObject('\fpcm\classes\cache');

        if (\fpcm\classes\baseconfig::installerEnabled()) {
            return false;
        }

        if (!$this->cache->isExpired('modules/activeeventscache')) {
            $this->activeModules = $this->cache->read('modules/activeeventscache');
            return;
        }

        $this->activeModules = \fpcm\classes\loader::getObject('\fpcm\modules\modules')->getEnabledDatabase();
        $this->cache->write('modules/activeeventscache', $this->activeModules, FPCM_CACHE_DEFAULT_TIMEOUT);
    }

    /**
     * Magic get
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : false;
    }

    /**
     * Magic set
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Magische Methode für nicht vorhandene Methoden
     * @param string $name
     * @param mixed $arguments
     * @return boolean
     */
    public function __call($name, $arguments)
    {
        print "Function '{$name}' not found in " . get_class($this) . '<br>';
        return false;
    }

    /**
     * Magische Methode für nicht vorhandene, statische Methoden
     * @param string $name
     * @param mixed $arguments
     * @return boolean
     */
    public static function __callStatic($name, $arguments)
    {
        print "Static function '{$name}' not found in " . get_class($this) . '<br>';
        return false;
    }

    /**
     * Gibt Module-Key anhand des Event-Datei-Pfades zurück
     * @param string $path
     * @return string
     */
    public function getModuleKeyByEvent($path)
    {
        return \fpcm\model\abstracts\module::getModuleKeyByFolder($path);
    }

    /**
     * Prüft ob spezielle Berechtigungen für Event nötig sind
     * @return boolean
     */
    public function checkPermissions()
    {
        $checkPermission = $this->getEventPermissons();
        if (!$this->permissions || !count($checkPermission)) {
            return true;
        }

        return $this->permissions->check($checkPermission);
    }

    /**
     * Defines type of returned data
     * @return string|bool
     */
    protected function getReturnType()
    {
        return self::FPCM_MODULE_EVENT_RETURNTYPE_SCALAR;
    }

    /**
     * Defines type of returned data
     * @return mixed|bool
     */
    protected function getEventPermissons()
    {
        return [];
    }

    /**
     * Checks if module event class has implemented \fpcm\events\abstracts\moduleEvent
     * @param mixed $object
     * @return boolean
     */
    protected function is_a($object)
    {
        if (is_a($object, self::EVENT_BASE_INSTANCE)) {
            return true;
        }

        trigger_error('Event object of class ' . get_class($object) . ' must be an instance of ' . self::EVENT_BASE_INSTANCE . '!');
        return false;
    }

    /**
     * Liefert Array mit Event-Klassen in installierten Modulen zurück
     * @return array
     * @since FPCM 3.3
     */
    protected function getEventClasses()
    {
        if (!count($this->activeModules)) {
            return [];
        }

        $classes = [];

        $eventBaseClass = DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $this->getEventClassBase() . '.php';
        foreach ($this->activeModules as $module) {

            $path = \fpcm\classes\dirs::getDataDirPath(\fpcm\classes\dirs::DATA_MODULES, $module, $eventBaseClass);
            if (!file_exists($path)) {
                continue;
            }

            $classes[] = $path;
        }

        return $classes;
    }

    /**
     * Liefert
     * @return string
     */
    protected function getEventClassBase()
    {
        return str_replace('fpcm\\events\\', '', get_class($this));
    }

    /**
     * Executed a certain events
     * @param array $data
     * @return array
     */
    public function run()
    {
        $eventClasses = $this->getEventClasses();

        if (!count($eventClasses)) {
            return $this->data;
        }

        foreach ($eventClasses as $eventClass) {

            $classkey = $this->getModuleKeyByEvent($eventClass);
            $eventClass = \fpcm\model\abstracts\module::getModuleEventNamespace($classkey, $this->getEventClassBase());

            /**
             * @var \fpcm\events\event
             */
            $module = new $eventClass($this->data);
            if (!$this->is_a($module)) {
                continue;
            }

            $eventResult = $module->run();
        }

        $returnDataType = $this->getReturnType();
        if ($returnDataType === self::FPCM_MODULE_EVENT_RETURNTYPE_VOID && $eventResult !== null) {
            trigger_error('Invalid data type. Returned data type must be null');
            return null;
        } elseif ($returnDataType === self::FPCM_MODULE_EVENT_RETURNTYPE_ARRAY && !is_array($eventResult)) {
            trigger_error('Invalid data type. Returned data type must be an array');
            return $this->data;
        } elseif ($returnDataType === self::FPCM_MODULE_EVENT_RETURNTYPE_OBJ && !is_object($eventResult)) {
            trigger_error('Invalid data type. Returned data type must be an object');
            return $this->data;
        } elseif ($returnDataType === self::FPCM_MODULE_EVENT_RETURNTYPE_SCALAR && is_scalar($eventResult) ) {
            trigger_error('Invalid data type. Returned data type must be instance of ' . $returnDataType);
            return $this->data;
        } elseif ($returnDataType !== false && !is_array($eventResult) && !is_a($eventResult, $returnDataType)) {
            trigger_error('Invalid data type. Returned data type must be instance of ' . $returnDataType);
            return $this->data;
        }

        return $eventResult;
    }

}
