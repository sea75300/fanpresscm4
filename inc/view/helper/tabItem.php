<?php

/**
 * FanPress CM 4
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\view\helper;

/**
 * Logs tab item
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2017, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm\view\helper
 * @since FPCM 4
 */
class tabItem extends helper {

    /**
     * Description
     * @var string
     */
    protected $description = '';

    /**
     * Data
     * @var string
     */
    protected $data = [];

    /**
     * Tab-ID
     * @var string
     */
    protected $dataViewId = 'logs';

    /**
     * 
     * @return string
     */
    public function getDataViewId()
    {
        return $this->dataViewId;
    }

    /**
     * 
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * 
     * @param string $dataViewId
     * @return $this
     */
    public function setDataViewId($dataViewId)
    {
        $this->dataViewId = $dataViewId;
        return $this;
    }

    /**
     * 
     * @return string
     */
    protected function getString()
    {
        $html = [];
        $html[] = '<li';
        $html[] = 'id="fpcm-tabs-'.$this->id.'"';

        if ($this->dataViewId) {
            $html[] = 'data-dataview-list="'.$this->dataViewId.'"';
        }

        $html[] = '><a href="'.$this->url.'" '.$this->getDataString().'>'.$this->text.'</a>';
        $html[] = '</li>';

        return implode(' ', $html);
    }

}
