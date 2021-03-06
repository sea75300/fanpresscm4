<?php

namespace nkorg\yatdl;

/**
 * YaML Table Definition Language Parser Libary\n
 * Item Abstract
 * 
 * @package nkorg\yatdl
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2016-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @version YaTDL4.0
 */
abstract class item {

    /**
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     * @param array $data
     */
    final public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Magic getter
     * @param string $name
     * @ignore
     */
    final public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }
    
    /**
     * Magic setter
     * @param string $name
     * @param mixed $value
     * @return bool
     * @ignore
     */
    final public function __set(string $name, $value)
    {
        $this->data[$name] = $value;
        return true;
    }

    /**
     * Checks if Property exists
     * @param string $name
     * @return bool
     */
    final public function hasProperty(string $name) : bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Dump item data
     * @return mixes
     */
    final public function dump()
    {
        return var_export($this->data, true);
    }
}
