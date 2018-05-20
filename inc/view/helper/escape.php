<?php

/**
 * FanPress CM 4
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\view\helper;

/**
 * Value escape view helper
 * 
 * @package fpcm\view\helper
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
final class escape {

    use traits\escapeHelper;

    /**
     * Element value
     * @var string
     */
    protected $value = '';

    /**
     * Element value
     * @var string
     */
    protected $mode = 0;

    /**
     * Element already returned
     * @var string
     */
    protected $returned = false;

    /**
     * Konstruktor
     * @param string $name
     * @param string $id
     */
    final public function __construct($value, $mode = null)
    {
        $this->value = $value;
        $this->mode = $mode;
    }

    /**
     * 
     * @return string
     */
    final public function __toString()
    {
        $this->returned = true;
        return $this->escapeVal($this->value, $this->mode);
    }

    /**
     * 
     * @return void
     */
    final public function __destruct()
    {
        if ($this->returned) {
            return;
        }

        print $this->escapeVal($this->value, $this->mode);
    }

}

?>