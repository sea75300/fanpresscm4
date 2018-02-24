<?php

/**
 * Article list controller base
 * @article Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\controller\action\articles;

abstract class articlelistbase extends \fpcm\controller\abstracts\controller {

    use \fpcm\controller\traits\articles\lists;


    /**
     * Liste mit erlaubten Artikel-Aktionen
     * @var array
     */
    protected $articleActions = [];

    /**
     *
     * @var bool
     */
    protected $deleteActions = false;

    /**
     *
     * @var bool
     */
    protected $canEdit = true;

    /**
     *
     * @var string
     */
    protected $listAction = '';

    /**
     *
     * @var string
     */
    protected $page = '';

    /**
     *
     * @var \fpcm\model\articles\search
     */
    protected $conditionItems;

    protected function getViewPath()
    {
        return 'articles/listouter';
    }

    protected function getHelpLink()
    {
        return 'hl_article_edit';
    }

    /**
     * Konstruktor
     */
    public function __construct()
    {
        parent::__construct();

        $this->initActionObjects();
        $this->initArticleActions();
        $this->initEditPermisions();
    }

    /**
     * Request-Handler
     * @return boolean
     */
    public function request()
    {   
        if (($this->buttonClicked('doAction') || $this->buttonClicked('clearTrash')) && !$this->checkPageToken()) {
            $this->view->addErrorMessage('CSRF_INVALID');
            return true;
        }

        if ($this->buttonClicked('doAction') && !is_null($this->getRequestVar('actions'))) {

            $actionData = $this->getRequestVar('actions');

            if ($actionData['action'] === 'trash') {

                if (!$this->doTrash()) {
                    $this->view->addErrorMessage('DELETE_FAILED_TRASH');
                } else {
                    $this->view->addNoticeMessage('DELETE_SUCCESS_TRASH');
                }

                return true;
            }


            if ((!isset($actionData['ids']) && $actionData['action'] != 'trash') || !$actionData['action']) {
                $this->view->addErrorMessage('SELECT_ITEMS_MSG');
                return true;
            }

            $ids = array_map('intval', $actionData['ids']);

            $action = in_array($actionData['action'], array_values($this->articleActions)) ? $actionData['action'] : false;

            if ($action === false) {
                $this->view->addErrorMessage('SELECT_ITEMS_MSG');
                return true;
            }

            if (!call_user_func([$this, 'do' . ucfirst($action)], $ids)) {
                $msg = ($action == 'delete') ? 'DELETE_FAILED_ARTICLE' : 'SAVE_FAILED_ARTICLE';
                $this->view->addErrorMessage($msg);
                return true;
            }

            $msg = ($action == 'delete') ? 'DELETE_SUCCESS_ARTICLE' : 'SAVE_SUCCESS_ARTICLE' . strtoupper($action);
            $this->view->addNoticeMessage($msg);
        }

        $this->getListAction();
        $this->getLimitsByPage();
        $this->getConditionItem();
        $this->getArticleCount();
        $this->getArticleItems();
        
        //$this->initPagination();
        return true;
    }

    /**
     * Controller-Processing
     * @return boolean
     */
    public function process()
    {
        //$this->initPagination();
        $this->initActionVars();

        $this->view->assign('users', array_flip($this->users));
        $this->view->assign('commentEnabledGlobal', $this->config->system_comments_enabled);
        $this->view->assign('showDraftStatus', $this->showDraftStatus);
        $this->view->assign('articleActions', $this->articleActions);
        $this->view->assign('deletePermissions', $this->deleteActions);

        $this->initSearchForm($this->users);
        $this->initMassEditForm($this->users);

        $this->view->addJsFiles(['articlelist.js']);

        if ($this->listAction !== 'articles/trash') {

            if ($this->permissions->check(['article' => 'add'])) {
                $this->view->addButton((new \fpcm\view\helper\linkButton('addArticle'))->setUrl(\fpcm\classes\tools::getFullControllerLink('articles/add'))->setText('HL_ARTICLE_ADD')->setIcon('pencil'));
            }

            if ($this->canEdit) {
                $this->view->addButton((new \fpcm\view\helper\button('massEdit', 'massEdit'))->setText('GLOBAL_EDIT')->setIcon('pencil-square-o'));
            }

            $this->view->addButton((new \fpcm\view\helper\button('opensearch', 'opensearch'))->setText('ARTICLES_SEARCH')->setIcon('search'));
        }

        $this->view->addButtons([
            (new \fpcm\view\helper\select('actions'))->setOptions($this->articleActions),
            (new \fpcm\view\helper\submitButton('doAction'))->setText('GLOBAL_OK')->setClass('fpcm-loader')->setIcon('check')
        ]);

        $minMax = $this->articleList->getMinMaxDate(1);
        $this->view->addJsVars([
            'articleSearchMode'   => $this->getSearchMode(),
            'articleSearchMinDate' => date('Y-m-d', $minMax['minDate'])
        ]);

        $formActionParams = [];
        if ($this->page) {
            $formActionParams['page'] = $this->page;
        }
        
        $this->view->setFormAction($this->listAction, $formActionParams);
        
        $this->translateCategories();

        $this->initDataView();
        $this->view->addDataView($this->dataView);

        return true;
    }

    /**
     * Artikel löschen
     * @param array $ids
     * @return boolean
     */
    protected function doDelete(array $ids)
    {
        if (!$this->deleteActions) {
            return false;
        }

        return $this->articleList->deleteArticles($ids);
    }

    /**
     * Papierkorb leeren
     * @return boolean
     */
    protected function doTrash()
    {
        if (!$this->deleteActions) {
            return false;
        }

        return $this->articleList->emptyTrash();
    }

    /**
     * Artikel aus Papierkorb wiederherstellen
     * @param array $ids
     * @return boolean
     */
    protected function doRestore(array $ids)
    {
        if (!$this->deleteActions) {
            return false;
        }
        
        return $this->articleList->restoreArticles($ids);
    }
    
    protected function getLimitsByPage()
    {
        $this->page          = $this->getRequestVar('page', [\fpcm\classes\http::FPCM_REQFILTER_CASTINT]);
        $this->listShowStart = \fpcm\classes\tools::getPageOffset($this->page, $this->listShowLimit);
    }

    /**
     * Seitenvaigation erzeugen
     */
    protected function initPagination()
    {
        $this->view->assign('backBtn', false);
        $this->view->assign('nextBtn', false);
        $this->view->assign('listActionLimit', '');

        $pagerData = \fpcm\classes\tools::calcPagination(
            $this->listShowLimit,
            $this->page,
            $this->articleCount,
            count($this->articleItems)
        );

        $this->view->assign('showPager', true);
        foreach ($pagerData as $key => $value) {
            $this->view->assign($key, $value);
        }

        if ($this->listAction) {
            $this->view->setFormAction($this->listAction, ['page' => $pagerData['pageCurrent']]);
        }

        $this->view->addJsVars(['currentModule' => $this->getRequestVar('module')]);
    }

    protected function initArticleActions()
    {
        if (!$this->permissions) {
            return false;
        }

        $this->canEdit = $this->permissions->check(['article' => ['edit', 'editall', 'approve', 'archive']]);

        $this->view->assign('canEdit', $this->canEdit);
        $this->deleteActions = $this->permissions->check(['article' => 'delete']);

        $tweet = new \fpcm\model\system\twitter();

        if ($tweet->checkRequirements() && $tweet->checkConnection()) {
            $this->articleActions[$this->lang->translate('ARTICLE_LIST_NEWTWEET')] = 'newtweet';
        }

        if ($this->deleteActions) {
            $this->articleActions[$this->lang->translate('GLOBAL_DELETE')] = 'delete';
        }

        $this->articleActions[$this->lang->translate('ARTICLES_CACHE_CLEAR')] = 'articlecache';

        $crypt = \fpcm\classes\loader::getObject('\fpcm\classes\crypt');
        $this->view->addJsVars(['artCacheMod' => urlencode($crypt->encrypt(\fpcm\model\articles\article::CACHE_ARTICLE_MODULE))]);
    }

    /**
     * Initialisiert Suchformular
     * @param array $users
     */
    private function initSearchForm($users)
    {
        $users = [$this->lang->translate('ARTICLE_SEARCH_USER') => -1] + $users;
        $this->view->assign('searchUsers', $users);

        $categories = [$this->lang->translate('ARTICLE_SEARCH_CATEGORY') => -1] + $this->categories;
        $this->view->assign('searchCategories', $categories);

        $this->view->assign('searchTypes', [
            $this->lang->translate('ARTICLE_SEARCH_TYPE_ALL') => -1,
            $this->lang->translate('ARTICLE_SEARCH_TYPE_TITLE') => 0,
            $this->lang->translate('ARTICLE_SEARCH_TYPE_TEXT') => 1
        ]);

        $this->view->assign('searchPinned', [
            $this->lang->translate('ARTICLE_SEARCH_PINNED') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('searchPostponed', [
            $this->lang->translate('ARTICLE_SEARCH_POSTPONED') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('searchComments', [
            $this->lang->translate('ARTICLE_SEARCH_COMMENTS') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('searchApproval', [
            $this->lang->translate('ARTICLE_SEARCH_APPROVAL') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('searchDraft', [
            $this->lang->translate('ARTICLE_SEARCH_DRAFT') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('searchCombination', [
            $this->lang->translate('ARTICLE_SEARCH_LOGICAND') => 0,
            $this->lang->translate('ARTICLE_SEARCH_LOGICOR') => 1
        ]);

        $this->view->addJsLangVars(['SEARCH_WAITMSG', 'ARTICLES_SEARCH', 'ARTICLE_SEARCH_START']);
        $this->view->addJsVars(['articlesLastSearch' => 0]);
    }

    /**
     * Initialisiert Massenbearbeitung
     * @param array $users
     */
    private function initMassEditForm($users)
    {
        $this->view->assign('massEditUsers', [$this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1] + $users);
        $this->view->assign('massEditCategories', $this->categories);

        $this->view->assign('massEditPinned', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('massEditPostponed', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('massEditComments', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('massEditApproved', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('massEditDraft', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->assign('massEditArchived', [
            $this->lang->translate('GLOBAL_NOCHANGE_APPLY') => -1,
            $this->lang->translate('GLOBAL_YES') => 1,
            $this->lang->translate('GLOBAL_NO') => 0
        ]);

        $this->view->addJsLangVars(['SAVE_FAILED_ARTICLES']);
        $this->view->addJsVars(['masseditPageToken' => \fpcm\classes\security::createPageToken('articles/massedit')]);
    }

    abstract protected function getArticleCount();

    abstract protected function getArticleItems();

    abstract protected function getConditionItem();

    abstract protected function getListAction();

    abstract protected function getSearchMode();

}

?>
