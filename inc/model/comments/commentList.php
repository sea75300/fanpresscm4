<?php

/**
 * FanPress CM 4
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\comments;

/**
 * Kommentar-Listen-Objekt
 * 
 * @package fpcm\model\comments
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class commentList extends \fpcm\model\abstracts\tablelist {

    use permissions;

    /**
     * articlelist Objekt
     * @var \fpcm\model\articles\articlelist
     * @since FPCM 3.3
     */
    protected $articleList;

    /**
     * Liste mit IDs von Artikeln, die vom aktuelle Benutzer verschrieben wurden
     * @var array
     * @since FPCM 3.3
     */
    protected $ownArticleIds = false;

    /**
     * Permission Object
     * @var \fpcm\model\system\permissions
     * @since FPCM 3.3
     */
    protected $permissions = false;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->table = \fpcm\classes\database::tableComments;

        if (is_object(\fpcm\classes\loader::getObject('\fpcm\model\system\session')) && \fpcm\classes\loader::getObject('\fpcm\model\system\session')->exists()) {
            $this->permissions = \fpcm\classes\loader::getObject('\fpcm\model\system\permissions');
        }

        parent::__construct();
    }

    /**
     * Liefert ein array aller Kommentare
     * @return array
     */
    public function getCommentsAll()
    {
        $list = $this->dbcon->fetch($this->dbcon->select($this->table, '*', '1=1' . $this->dbcon->orderBy(array('createtime DESC'))), true);
        return $this->createCommentResult($list);
    }

    /**
     * Liefert ein array der Kommentare, welcher mit der Bedingung übereinstimmen
     * @param int $articleId Artikel-ID
     * @param bool $private private Kommentare ja/nein
     * @param bool $hideUnapproved genehmigte Kommentare ja/nein
     * @param bool $spam als Spam markierte Kommentare ja/nein
     * @return array
     */
    public function getCommentsByCondition($articleId, $private = 0, $hideUnapproved = 1, $spam = 0)
    {
        $conditions = new search();
        $conditions->articleid = $articleId;
        $conditions->private = $private;
        $conditions->spam = $spam;
        $conditions->approved = $hideUnapproved;
        $conditions->searchtype = 0;
        $conditions->deleted = 0;

        return $this->getCommentsBySearchCondition($conditions);
    }

    /**
     * Liefert ein array der Kommentare, welcher mit der Bedingung übereinstimmen
     * @param search $conditions
     * @return array
     */
    public function getCommentsBySearchCondition(search $conditions)
    {
        $where = ['1=1'];
        $valueParams = [];

        if ($conditions->text && $conditions->searchtype == 0) {
            $where[] = "name " . $this->dbcon->dbLike() . " ?";
            $valueParams[] = "%{$conditions->text}%";
        }

        if ($conditions->text && $conditions->searchtype == 0) {
            $where[] = "email " . $this->dbcon->dbLike() . " ?";
            $valueParams[] = "%{$conditions->text}%";
        }

        if ($conditions->text && $conditions->searchtype == 0) {
            $where[] = "website " . $this->dbcon->dbLike() . " ?";
            $valueParams[] = "%{$conditions->text}%";
        }

        if ($conditions->text) {
            $where[] = "text " . $this->dbcon->dbLike() . " ?";
            $valueParams[] = "%{$conditions->text}%";
        }

        if ($conditions->searchtype == 0) {
            $where = ['(' . implode(' OR ', $where) . ')'];
        }

        if ($conditions->datefrom) {
            $where[] = "createtime >= ?";
            $valueParams[] = $conditions->datefrom;
        }

        if ($conditions->dateto) {
            $where[] = "createtime <= ?";
            $valueParams[] = $conditions->dateto;
        }

        if ($conditions->spam && $conditions->spam > -1) {
            $where[] = "spammer = ?";
            $valueParams[] = $conditions->spam;
        }

        if ($conditions->private && $conditions->private > -1) {
            $where[] = "private = ?";
            $valueParams[] = $conditions->private;
        }

        if ($conditions->approved && $conditions->approved > -1) {
            $where[] = "approved = ?";
            $valueParams[] = $conditions->approved;
        }

        if ($conditions->deleted !== null) {
            $where[] = "deleted = ?";
            $valueParams[] = $conditions->deleted;
        }

        if ($conditions->articleid) {
            $where[] = "articleid = ?";
            $valueParams[] = $conditions->articleid;
        }

        if ($conditions->ipaddress) {
            $where[] = "ipaddress = ?";
            $valueParams[] = $conditions->ipaddress;
        }

        $combination = $conditions->combination ? $conditions->combination : 'AND';

        $eventData = $this->events->trigger('comments\getByCondition', array(
            'conditions' => $conditions,
            'where' => $where,
            'values' => $valueParams
        ));

        $where = $eventData['where'];
        $where = implode(" {$combination} ", $where);
        $valueParams = $eventData['values'];

        $where2 = [];
        $where2[] = $this->dbcon->orderBy( ($conditions->orderby ? $conditions->orderby : ['createtime ASC']) );

        $limit = $conditions->limit;
        if ($limit) {
            $where2[] = $this->dbcon->limitQuery($limit[0], $limit[1]);
        }

        $where .= ' ' . implode(' ', $where2);

        $list = $this->dbcon->fetch( $this->dbcon->select($this->table, '*', $where, $valueParams), true );

        return $this->createCommentResult($list);
    }

    /**
     * Löscht Kommentare
     * @param array $ids
     * @return bool
     */
    public function deleteComments(array $ids)
    {
        $this->cache->cleanup();
        return $this->dbcon->update($this->table, ['deleted'], [1, implode(', ', array_map('intval', $ids))], 'id IN (?)');
    }

    /**
     * Löscht Kommentare für einen Artikel mit übergebener/n ID(s)
     * @param int|array $article_ids
     * @return bool
     */
    public function deleteCommentsByArticle($article_ids)
    {
        if (!is_array($article_ids)) {
            $article_ids = array($article_ids);
        }

        $this->cache->cleanup();
        return $this->dbcon->update($this->table, ['deleted'], [1, implode(', ', array_map('intval', $article_ids))], 'articleid IN (?)');
    }

    /**
     * Zählt Kommentare für alle Artikel
     * @param array $articleIds
     * @param bool $private
     * @param bool $approved
     * @param bool $spam
     * @param bool $getCached
     * @return bool
     */
    public function countComments(array $articleIds = [], $private = null, $approved = null, $spam = null, $getCached = true)
    {
        $cacheName = \fpcm\model\articles\article::CACHE_ARTICLE_MODULE . '/' . __FUNCTION__ . \fpcm\classes\tools::getHash(json_encode(func_get_args()));

        if (!$this->cache->isExpired($cacheName) && $getCached) {
            return $this->cache->read($cacheName);
        }

        $where = count($articleIds) ? "articleid IN (" . implode(',', $articleIds) . ")" : '1=1';
        $where .= is_null($private) ? '' : ' AND private = ' . $private;
        $where .= is_null($approved) ? '' : ' AND approved = ' . $approved;
        $where .= is_null($spam) ? '' : ' AND spammer = ' . $spam;
        $where .= ' AND deleted = 0';

        $articleCounts = $this->dbcon->fetch($this->dbcon->select($this->table, 'articleid, count(id) AS count', "{$where} GROUP BY articleid"), true);
        if (!count($articleCounts)) {
            return [0];
        }

        $res = [];
        foreach ($articleCounts as $articleCount) {
            $res[$articleCount->articleid] = $articleCount->count;
        }

        $this->cache->write($cacheName, $res, $this->config->system_cache_timeout);

        return $res;
    }

    /**
     * Zählt Kommentare für alle Artikel, die Privat oder nicht genehmigt sind
     * @param array $articleIds
     * @return array
     */
    public function countUnapprovedPrivateComments(array $articleIds = [])
    {
        $cacheName = \fpcm\model\articles\article::CACHE_ARTICLE_MODULE . '/' . __FUNCTION__ . \fpcm\classes\tools::getHash(implode(':', $articleIds));
        if (!$this->cache->isExpired($cacheName)) {
            return $this->cache->read($cacheName);
        }

        $where = count($articleIds) ? "articleid IN (" . implode(',', $articleIds) . ")" : '1=1';
        $articleCounts = $this->dbcon->fetch($this->dbcon->select($this->table, 'articleid, count(id) AS count', "$where AND (private = 1 OR approved = 0) AND deleted = 0 GROUP BY articleid"), true);

        if (!count($articleCounts)) {
            return [0];
        }

        $res = [];
        foreach ($articleCounts as $articleCount) {
            $res[$articleCount->articleid] = $articleCount->count;
        }

        $this->cache->write($cacheName, $res, $this->config->system_cache_timeout);

        return $res;
    }

    /**
     * Zählt Kommentare anhand von Bedingung
     * @param search $conditions
     * @return int
     */
    public function countCommentsByCondition(search $conditions)
    {
        $where = ['id > 0'];

        if ($conditions->private) {
            $where[] = 'private = 1';
        }

        if ($conditions->unapproved) {
            $where[] = 'approved = 0';
        }

        if ($conditions->spam) {
            $where[] = 'spammer = 1';
        }

        $where[] = ($conditions->deleted ? 'deleted = 1' : 'deleted = 0');

        $combination = $conditions->combination ? $conditions->combination : 'AND';
        
        return $this->dbcon->count(
            $this->table,
            '*',
            $this->events->trigger('comments\getByConditionCount', implode(" {$combination} ", $where))
        );
    }

    /**
     * Gibt Zeit zurück, wenn von der aktuellen IP der letzte Kommentar geschrieben wurde
     * @return int
     */
    public function getLastCommentTimeByIP()
    {
        $res = $this->dbcon->fetch($this->dbcon->select($this->table, 'createtime', 'deleted = 0 AND ipaddress ' . $this->dbcon->dbLike() . ' ?' . $this->dbcon->orderBy(array('createtime ASC')) . $this->dbcon->limitQuery(0, 1), array(\fpcm\classes\http::getIp())));
        return isset($res->createtime) ? $res->createtime : 0;
    }

    /**
     * Prüft ob für die in Daten eines neuen Kommentars bereits Kommentare als Spam markiert wurden
     * @param \fpcm\model\comments\comment $comment
     * @return boolean true, wenn Anzahl größer als in $this->config->comments_markspam_commentcount definiert
     */
    public function spamExistsbyCommentData(comment $comment)
    {
        $count = $this->dbcon->count($this->table, 'id', implode(' OR ', [            
            'name ' . $this->dbcon->dbLike() . ' ?',
            'email ' . $this->dbcon->dbLike() . ' ?',
            'website ' . $this->dbcon->dbLike() . ' ?',
            'ipaddress ' . $this->dbcon->dbLike() . ' ?'
        ]) . ' AND spammer = 1 AND deleted = 0',
        [            
            $comment->getName(), '%' .
            $comment->getEmail() . '%',
            '%' . $comment->getWebsite() . '%',
            $comment->getIpaddress()
        ]);

        return $count >= $this->config->comments_markspam_commentcount ? true : false;
    }

    /**
     * Massenbearbeitung
     * @param array $commentIds
     * @param array $fields
     * @since FPCM 3.6
     */
    public function editCommentsByMass(array $commentIds, array $fields)
    {
        if (!count($commentIds)) {
            return false;
        }

        $result = $this->events->trigger('comments\massEditBefore', [
            'fields' => $fields,
            'commentIds' => $commentIds
        ]);

        foreach ($result as $key => $val) {
            ${$key} = $val;
        }

        if (isset($fields['spammer']) && $fields['spammer'] === -1) {
            unset($fields['spammer']);
        }

        if (isset($fields['approved']) && $fields['approved'] === -1) {
            unset($fields['approved']);
        }

        if (isset($fields['private']) && $fields['private'] === -1) {
            unset($fields['private']);
        }

        if (isset($fields['articleid']) && $fields['articleid'] < 1) {
            unset($fields['articleid']);
        }

        if (!count($fields)) {
            return false;
        }

        $where = 'id IN (' . implode(',', $commentIds) . ')';
        $result = $this->dbcon->update($this->table, array_keys($fields), array_values($fields), $where);

        $this->cache->cleanup();
        return $result;
    }

    /**
     * Erzeugt Listen-Result-Array
     * @param array $list
     * @return array
     */
    private function createCommentResult($list)
    {
        $res = [];

        foreach ($list as $listItem) {
            $object = new comment();
            if (!$object->createFromDbObject($listItem)) {
                continue;
            }
            $this->checkEditPermissions($object);
            $res[$object->getId()] = $object;
        }

        return $res;
    }

}
