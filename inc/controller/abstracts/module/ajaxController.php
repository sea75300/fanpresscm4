<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\abstracts\module;

/**
 * Module AJAX controller base
 * 
 * @package fpcm\module
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @abstract
 * @since 4.1
 */
class ajaxController extends \fpcm\controller\abstracts\ajaxController
implements \fpcm\controller\interfaces\isAccessible {

    use \fpcm\module\tools;
    
    /**
     * Konstruktor
     * @return void
     */
    final public function __construct()
    {
        parent::__construct();
        $this->initConstruct();
    }

    /**
     * Must return true, if controller is accessible
     * @return bool
     * @since 4.4
     */
    public function isAccessible(): bool
    {
        return true;
    }

    /**
     * Initialises view object
     * @return bool
     */
    final protected function initView()
    {
        return parent::initView();
    }

}