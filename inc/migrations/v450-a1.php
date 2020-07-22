<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\migrations;

/**
 * Migration to v4.5.0-a1
 * 
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm\migrations
 * @since FPCM 4.5.0-a1
 * @see migration
 */
class v450a1 extends migration {

    /**
     * Update inedit data for articles
     * @return bool
     */
    protected function alterTablesAfter() : bool
    {
        if ($this->getConfig()->file_uploader_new === null) {
            return true;
        }
        
        fpcmLogSystem('Remove "file_uploader_new" config option...');
        if (!$this->getConfig()->remove('file_uploader_new')) {
            trigger_error('Failed to remove "file_uploader_new" option config config table!');
            return false;
        }
        
        fpcmLogSystem('Remove "file_uploader_new" config option from users...');
        $obj = new \fpcm\model\dbal\selectParams(\fpcm\classes\database::tableAuthors);
        $obj->setWhere("usrmeta NOT LIKE '' AND usrmeta LIKE '%file_uploader_new%'");
        $users = $this->getDB()->selectFetch($obj);
        if (!$users) {
            trigger_error('Failed to fetch user list from database!');
            return false;
        }

        if (!count($users)) {
            fpcmLogSystem('Config option not set by users, skip process...');
            return true;
        }
        
        foreach ($users as $user) {

            $uObj = new \fpcm\model\users\author();
            $uObj->createFromDbObject($user);

            fpcmLogSystem('Update user meta data for '.$uObj->getUsername().'...');
            $meta = $uObj->getUserMeta();
            unset($meta['file_uploader_new']);

            $uObj->setUserMeta($meta);
            if (!$uObj->update()) {
                trigger_error('Failed to update user meta data for '.$uObj->getUsername().'!');
                return false;
            }

        }
        
        return true;
    }
    
}