<?php

/**
 * FanPress CM 4
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\view\helper;

/**
 * Button view helper object
 * 
 * @package fpcm\view\helper
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class button extends helper {

    use traits\iconHelper,
        traits\typeHelper;

    /*  @since 4.4.0 */
    const NAME_PREFIX = 'btn';

    /**
     * Is primary button
     * @var bool
     * @since 4.6-dev
     */
    protected $primary = false;

    /**
     * Return element string
     * @return string
     */
    protected function getString()
    {
        if ($this->primary) {
            $this->class = preg_replace('/(btn-)(.*)/i', '$1primary', $this->class);
        }
        
        return implode(' ', [
            "<button type=\"{$this->type}\" ",
            $this->getDataString(),
            $this->getAriaString(),
            $this->getReadonlyString(),
            ($this->readonly ? $this->getClassString() : $this->getNameIdString() . ' ' . $this->getClassString()),
            ($this->iconOnly ? "title=\"{$this->text}\">{$this->getIconString()}" : ">{$this->getIconString()} {$this->getDescriptionTextString()}"),
            "</button>"
        ]);
    }

    /**
     * Return class string
     * @return string
     */
    protected function getReadonlyString()
    {
        return $this->readonly ? "disabled" : '';
    }

    /**
     * Optional init function
     * @return void
     */
    protected function init()
    {
        $this->prefix = self::NAME_PREFIX;
        $this->class = 'btn btn-light fpcm ui-button';
        $this->type = 'button';
    }
    
    /**
     * Set button to primary
     * @param bool $primary
     * @return $this
     * @since 4.6-dev
     */
    public function setPrimary(bool $primary = true)
    {
        $this->primary = $primary;
        return $this;
    }

    final public function setOnClick(string $func, $args = null)
    {
        $this->data['fn'] = $func;
        $this->data['fn-arg'] = $args;
        return $this;
    }
}

?>