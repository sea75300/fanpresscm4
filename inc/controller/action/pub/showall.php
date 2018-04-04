<?php

/**
 * Public article list controller
 * @article Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\action\pub;

class showall extends showcommon {

    /**
     * Konstruktor
     * @param bool $apiMode API-Modus
     */
    public function __construct($apiMode = false)
    {
        $this->apiMode = $apiMode;
        parent::__construct();

        $this->view->showHeaderFooter($this->apiMode ? \fpcm\view\view::INCLUDE_HEADER_NONE : \fpcm\view\view::INCLUDE_HEADER_SIMPLE);
    }

    /**
     * @see \fpcm\controller\abstracts\controller::getViewPath
     * @return string
     */
    protected function getViewPath()
    {
        return 'public/showall';
    }

    /**
     * Request-Handler
     * @return boolean
     */
    public function request()
    {
        $this->limit = defined('FPCM_PUB_LIMIT_LISTALL') ? FPCM_PUB_LIMIT_LISTALL : $this->config->articles_limit;
        $this->cacheName = \fpcm\model\articles\article::CACHE_ARTICLE_MODULE . '/articlelist' . $this->page . $this->category;

        return parent::request();
    }

    /**
     * Controller-Processing
     * @return boolean
     */
    public function process()
    {
        parent::process();

        $parsed = [];

        if ($this->cache->isExpired($this->cacheName) || $this->session->exists()) {

            $conditions = new \fpcm\model\articles\search();
            $conditions->limit = [$this->limit, $this->listShowLimit];
            $conditions->archived = 0;
            $conditions->postponed = 0;
            $conditions->orderby = ['pinned DESC, ' . $this->config->articles_sort . ' ' . $this->config->articles_sort_order];

            if ($this->category !== 0) {
                $conditions->category = $this->category;
            }

            $articles = $this->articleList->getArticlesByCondition($conditions);
            $this->users = $this->userList->getUsersForArticles(array_keys($articles));

            foreach ($articles as $article) {
                $parsed[] = $this->assignData($article);
            }

            $countConditions = new \fpcm\model\articles\search();
            $countConditions->active = 1;
            if ($this->category !== 0) {
                $countConditions->category = $this->category;
            }

            $parsed[] = $this->createPagination($this->articleList->countArticlesByCondition($countConditions));
            $parsed = $this->events->trigger('publicShowAll', $parsed);

            if (!$this->session->exists()) {
                $this->cache->write($cacheName, $parsed, $this->config->system_cache_timeout);
            }
        } else {
            $parsed = $this->cache->read($this->cacheName);
        }

        $content = implode(PHP_EOL, $parsed);
        if (!$this->isUtf8) {
            $content = utf8_decode($content);
        }

        $this->view->assign('content', $content);
        $this->view->assign('systemMode', $this->config->system_mode);
        $this->view->render();
    }

    /**
     * Seitennavigation erzeugen
     * @param int $count
     * @param string $action
     * @return string
     */
    protected function createPagination($count, $action = 'fpcm/list')
    {

        $res = parent::createPagination($count, $action);
        if ($this->config->articles_archive_show) {
            $res = str_replace('</ul>', '<li><a href="?module=fpcm/archive" class="fpcm-pub-pagination-archive">' . $this->lang->translate('ARTICLES_PUBLIC_ARCHIVE') . '</a></li>' . PHP_EOL . '</ul>' . PHP_EOL, $res);
        }

        $res = $this->events->trigger('publicPageinationShowAll', $res);

        return $res ? $res : '';
    }

}

?>