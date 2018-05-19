<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\classes;

/**
 * Loader
 * 
 * @package fpcm\classes\loader
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
final class loader {

    /**
     * Globaler Generator für Objekte
     * @param string $class
     * @param mixed $params
     * @return object
     */
    public static function getObject($class, $params = null, $cache = true)
    {
        if (!class_exists($class)) {
            trigger_error('Undefined class ' . $class);
            return false;
        }

        $class = ltrim($class, '\\');
        if (isset($GLOBALS['fpcm']['objects'][$class]) && is_object($GLOBALS['fpcm']['objects'][$class]) && $cache) {
            return $GLOBALS['fpcm']['objects'][$class];
        }

        $GLOBALS['fpcm']['objects'][$class] = $params ? new $class($params) : new $class();
        return $GLOBALS['fpcm']['objects'][$class];
    }

    /**
     * 
     * @param string $libPath
     * @param boolean $exists
     * @return string
     */
    public static function libGetFilePath($libPath, $exists = true)
    {
        $path = dirs::getFullDirPath('lib', $libPath);
        if ($exists && !file_exists($path)) {
            trigger_error('Lib path ' . $path . ' does not exists!');
        }

        if (file_exists($path . DIRECTORY_SEPARATOR . 'autoload.php')) {
            return $path . DIRECTORY_SEPARATOR . 'autoload.php';
        }

        return $path;
    }

    /**
     * 
     * @param string $libPath
     * @return string
     */
    public static function libGetFileUrl($libPath)
    {
        return dirs::getLibUrl($libPath);
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @param bool $force
     * @return boolean
     */
    public static function stackPush($name, $value, $force = false)
    {
        if (isset($GLOBALS['fpcm']['stack'][$name]) && !$force) {
            return false;
        }

        $GLOBALS['fpcm']['stack'][$name] = $value;
        return true;
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public static function stackPull($name)
    {
        return isset($GLOBALS['fpcm']['stack'][$name]) ? $GLOBALS['fpcm']['stack'][$name] : null;
    }

}
