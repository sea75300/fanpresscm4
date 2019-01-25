<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\shares;

/**
 * Artikel Objekt
 * 
 * @package fpcm\model\shares
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
class shares extends \fpcm\model\abstracts\tablelist {

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->table = \fpcm\classes\database::tableShares;
        parent::__construct();
    }

    /**
     * Ftech share count by article id, item can be give via $item
     * @param int $articleId
     * @param string $item
     * @return array
     */
    public function getByArticleId(int $articleId, $item = null) : array
    {
        $search = ['article_id' => $articleId];
        if ($item) {
            $search['shareitem'] = $item;
        }

        $obj = (new \fpcm\model\dbal\selectParams())
                ->setTable($this->table)
                ->setParams(array_values($search))
                ->setWhere( implode(' = ? AND ', array_keys($search)).' = ? '.$this->dbcon->orderBy(['lastshare DESC']) )
                ->setFetchAll(TRUE);
        
        $result = $this->dbcon->selectFetch($obj);
        if (!$result) {
            return [];
        }

        $list = [];
        foreach ($result as $dataSet) {
            $obj = new share();
            $obj->createFromDbObject($dataSet);
            $list[$dataSet->shareitem] = $obj;
        }

        return $list;
    }

    /**
     * Check if a certain share item is registered
     * @param string $item
     * @return string
     */
    public static function getRegisteredShares($item) : string
    {
        $item = strtolower($item);
        
        $shares = \fpcm\classes\loader::getObject('\fpcm\events\events')->trigger('pub\registerShares', [
            'facebook', 'twitter', 'googleplus', 'tumblr', 'pinterest', 'reddit', 'whatsapp', 'email',
            'likebutton'
        ]);
        
        if (in_array($item, $shares)) {
            return $item;
        }
        
        return '';
    }
}
