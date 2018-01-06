<?php
    /**
     * FanPress CM 3.x
     * @license http://www.gnu.org/licenses/gpl.txt GPLv3
     */

    namespace fpcm\classes;

    /**
     * Directory base config
     * 
     * @package fpcm\classes\baseconfig
     * @author Stefan Seehafer <sea75300@yahoo.de>
     * @copyright (c) 2011-2018, Stefan Seehafer
     */     
    final class dirs {

        const DATA_CONFIG   = 'config';
        const DATA_CACHE    = 'cache';
        const DATA_FMTMP    = 'filemanager';
        const DATA_LOGS     = 'logs';
        const DATA_STYLES   = 'styles';
        const DATA_SHARE    = 'share';
        const DATA_TEMP     = 'temp';
        const DATA_UPLOADS  = 'uploads';
        const DATA_DBDUMP   = 'dbdump';
        const DATA_DBSTRUCT = 'dbstruct';
        const DATA_DRAFTS   = 'drafts';
        const DATA_PROFILES = 'profiles';
        const DATA_MODULES  = 'modules';

        const CORE_THEME    = 'theme';
        const CORE_JS       = 'js';

        /**
         * Initialisiert Basis-Ordner
         * @return boolean
         */
        public static function initDirs() {
            $GLOBALS['fpcm']['dir']['base'] = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR;                        
            $GLOBALS['fpcm']['dir']['core'] = $GLOBALS['fpcm']['dir']['base'].'core'.DIRECTORY_SEPARATOR;
            $GLOBALS['fpcm']['dir']['data'] = $GLOBALS['fpcm']['dir']['base'].'data'.DIRECTORY_SEPARATOR;
            $GLOBALS['fpcm']['dir']['inc']  = $GLOBALS['fpcm']['dir']['base'].'inc'.DIRECTORY_SEPARATOR;
            $GLOBALS['fpcm']['dir']['lib']  = $GLOBALS['fpcm']['dir']['base'].'lib'.DIRECTORY_SEPARATOR;

            return true;
        }

        /**
         * Initialisiert Basis-URLs
         * @return boolean
         */
        public static function initUrls() {

            if (php_sapi_name() === 'cli') {
                $GLOBALS['fpcm']['urls']['base'] = '';
                return false;
            }

            $GLOBALS['fpcm']['urls']['base'] = (baseconfig::canHttps() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $GLOBALS['fpcm']['urls']['data'] = $GLOBALS['fpcm']['urls']['base'].'/data/';
            $GLOBALS['fpcm']['urls']['core'] = $GLOBALS['fpcm']['urls']['base'].'/core/';
            $GLOBALS['fpcm']['urls']['lib']  = $GLOBALS['fpcm']['urls']['base'].'/lib/';
            
            return true;
        }

        /**
         * Kompletten Ordner-Pfad ausgehend von Basis-Ordner ermitteln
         * @param string $type
         * @param string $path
         * @param boolean $base
         * @return string
         */
        public static function getFullDirPath($type, $path = '', $base = false)
        {
            $path = $GLOBALS['fpcm']['dir']['base'].$type.(trim($path ? DIRECTORY_SEPARATOR.$path : ''));
            if (!file_exists($path)) {
                trigger_error('Invalid path, path does not exists in "'.$path.'"!');
                return false;
            }

            return ($base ? basename($path) : $path);
        }

        /**
         * Kompletten Ordner-Pfad ausgehend von data-Ordner ermitteln
         * @param string $type
         * @param string $path
         * @param boolean $base
         * @return string
         */
        public static function getDataDirPath($type, $path = '', $base = false)
        {
            $path = $GLOBALS['fpcm']['dir']['data'].$type.DIRECTORY_SEPARATOR.$path;
            if (!file_exists($path)) {
                trigger_error('Invalid data path, path does not exists in "'.$path.'"!');
                return false;
            }
            
            return ($base ? basename($path) : $path);
        }

        /**
         * Kompletten Ordner-Pfad ausgehend von inc-Ordner ermitteln
         * @param string $path
         * @return string
         */
        public static function getIncDirPath($path = '')
        {
            $path = $GLOBALS['fpcm']['dir']['inc'].$path;
            if (!file_exists($path)) {
                trigger_error('Invalid include path, path does not exists in "'.$path.'"!');
                return false;
            }
            
            return $path;
        }

        /**
         * Komplette URL ausgehend von data-Ordner ermitteln
         * @param string $type
         * @param string $path
         * @return string
         */
        public static function getDataUrl($type, $path)
        {
            return $GLOBALS['fpcm']['dir']['data'].'/data/'.$type.'/'.$path;
        }

        /**
         * Komplette URL ausgehend von core-Ordner ermitteln
         * @param string $type
         * @param string $path
         * @return string
         */
        public static function getCoreUrl($type, $path)
        {
            return $GLOBALS['fpcm']['urls']['core'].'/core/'.$type.'/'.$path;
        }

        /**
         * Komplette URL ausgehend von lib-Ordner ermitteln
         * @param string $path
         * @return string
         */
        public static function getLibUrl($path)
        {
            return $GLOBALS['fpcm']['urls']['lib'].$path;
        }

    }
?>