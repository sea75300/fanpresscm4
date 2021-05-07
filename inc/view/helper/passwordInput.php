<?php

/**
 * FanPress CM 4
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\view\helper;

/**
 * Text input view helper object
 * 
 * @package fpcm\view\helper
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
final class passwordInput extends input {

    /**
     * Optional init function
     * @return void
     */
    protected function init()
    {
        parent::init();
        $this->type = 'password';
        $this->class .= ' fpcm-ui-input-password';
    }

    protected function appendItems(string &$str): bool
    {
        $str .= ' ' . (string) (new icon('eye fa-inverse'))
                ->setClass('fpcm ui-put-pass-toggle')
                ->setStack('circle');
        
        return true;
    }

}

?>