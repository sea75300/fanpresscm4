<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\abstracts;

use fpcm\classes\loader;

/**
 * Dashboard container model base
 * 
 * @package fpcm\model\abstracts
 * @abstract
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
abstract class dashcontainer extends model implements \fpcm\model\interfaces\dashcontainer {

    /**
     * Default container cache module
     */
    const CACHE_M0DULE_DASHBOARD = 'dashboard';

    /**
     * Container height big
     */
    const DASHBOARD_HEIGHT_VERYBIG = 'very-big';

    /**
     * Container height big
     */
    const DASHBOARD_HEIGHT_BIG = 'big';

    /**
     * Container height medium
     */
    const DASHBOARD_HEIGHT_MEDIUM = 'middle';

    /**
     * Container height medium
     */
    const DASHBOARD_HEIGHT_SMALL_MEDIUM = 'small-medium';

    /**
     * Container height SMALL
     */
    const DASHBOARD_HEIGHT_SMALL = 'small';

    /**
     * Container max position
     */
    const DASHBOARD_POS_MAX = '{{max}}';

    /**
     * Container-Name
     * @var string
     */
    protected $name = '';

    /**
     * ggf. nötige Container-Berechtigungen
     * @var array
     */
    protected $checkPermissions = [];

    /**
     * Container-Position
     * @var int 
     */
    protected $position = 0;

    /**
     * Berechtigungen
     * @var \fpcm\model\permissions\permissions
     * @since 4.4
     */
    protected $permissions;

    /**
     * Konstruktor
     */
    final public function __construct()
    {
        parent::__construct();
        if ($this instanceof \fpcm\model\interfaces\isAccessible) {
            $this->permissions = \fpcm\classes\loader::getObject('\fpcm\model\permissions\permissions');
        }

        $this->initObjects();
    }

    /**
     * Cache-Name zurückgeben
     * @param string $addName
     * @return string
     */
    public function getCacheName($addName = '')
    {
        $this->cacheName = self::CACHE_M0DULE_DASHBOARD . '/' . $this->getName() . $addName;
        return $this->cacheName;
    }

    /**
     * Container-Breite-CSS-Klasse (big/small) zurückgeben
     * @return string
     */
    public function getWidth()
    {
        return 4;
    }

    /**
     * Container-Höhe-Klasse (big/middle/small) zurückgeben
     * @return string
     */
    public function getHeight()
    {
        return self::DASHBOARD_HEIGHT_SMALL;
    }

    /**
     * Returns stored container position
     * @return int|bool
     * @since 4.1
     */
    final public function getStoredPosition()
    {
        $conf = loader::getObject('\fpcm\model\system\session')->getCurrentUser()->getUserMeta('dashboardpos');
        return is_array($conf) && isset($conf[$this->getName()]) ? $conf[$this->getName()] : false;
    }

    /**
     * Container-Berechtigungen, die geprüft werden müssen, zurückgeben
     * @return string
     */
    public function getPermissions()
    {
        return [];
    }

    /**
     * Return JavaScript view vars
     * @return array
     * @since 3.1.3
     */
    public function getJavascriptVars()
    {
        return [];
    }

    /**
     * Return JavaScript language vars
     * @return array
     * @since 3.1.3
     */
    public function getJavascriptLangVars()
    {
        return [];
    }

    /**
     * Return JavaScript files
     * @return array
     * @since 3.1.3
     */
    public function getJavascriptFiles()
    {
        return [];
    }

    /**
     * Returns view vars
     * @return array
     * @since 3.1.3
     */
    public function getControllerViewVars()
    {
        return [];
    }

    /**
     * Gibt Liste mit zu Variablen zurück, welche an Dashboard-Controller-View übergeben werden sollen
     * @return string
     * @since 3.1.3
     */
    public function getView()
    {
        return '';
    }

    /**
     * Initialize objects
     * @return bool
     */
    protected function initObjects()
    {
        return true;
    }

    /**
     * Container-Name zurückgeben
     * @return string
     */
    abstract public function getName();

    /**
     * container-Objekt via print/echo ausgeben
     * @return string
     */
    final public function __toString()
    {
        $html = [];
        $html[] = '<div id="fpcm-dashboard-container-' . $this->getName() . '" class="fpcm-dashboard-container fpcm-dashboard-container-' . $this->getName() . ' col-sm-12 col-md-' . $this->getWidth() . ' fpcm-dashboard-container-width-' . $this->getWidth() . ' fpcm-dashboard-container-height-' . $this->getHeight() . ' fpcm-ui-padding-none-lr" data-container="' . $this->getName() . '">';
        $html[] = ' <div class="fpcm-dashboard-container-inner fpcm ui-background-white-50p ui-blurring ui-border-radius-all">';
        $html[] = '     <h3 class="fpcm-dashboard-container-headline p-2" title="' . strip_tags($this->language->translate($this->getHeadline())) . '">';
        $html[] = '         <span class="fpcm ui-inline-block ui-align-ellipsis">' . $this->language->translate($this->getHeadline()) . '</span> ';
        $html[] = '         ' . (new \fpcm\view\helper\icon('arrows-alt'))->setClass('fpcm-dashboard-container-move fpcm-ui-float-right');
        $html[] = '     </h3>';
        $html[] = '     <div class="fpcm-dashboard-container-content">' . $this->getContent() . '</div>';
        $html[] = ' </div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

}

?>