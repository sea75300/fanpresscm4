<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\traits\templates;

/**
 * -Template editor trait
 * 
 * @package fpcm\controller\traits\system.syscheck
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
trait edit {

    /**
     *
     * @var \fpcm\model\pubtemplates\template
     */
    protected $template;

    /**
     *
     * @var string
     */
    protected $prefix = 'TEMPLATE_ARTICLE_';

    /**
     * 
     * @return boolean
     */
    protected function getArticleTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\article($this->config->articles_template_active);
        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function getArticleSingleTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\article($this->config->article_template_active);
        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function getCommentTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\comment($this->config->comments_template_active);
        $this->prefix = 'TEMPLATE_COMMMENT_';
        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function getCommentFormTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\commentform();
        $this->prefix = 'TEMPLATE_COMMMENTFORM_';
        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function getLatestNewsTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\latestnews();
        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function getTweetTemplate()
    {
        $this->template = new \fpcm\model\pubtemplates\tweet();
        return true;
    }
}

?>