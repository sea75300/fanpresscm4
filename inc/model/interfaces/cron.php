<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\interfaces;

/**
 * Cronjob-Interface
 * 
 * @package fpcm\model\interfaces
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2020, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
interface cron {

    /**
     * Cronjob ausführen
     */
    public function run();

    /**
     * Zeitcheck ausführen
     */
    public function checkTime();

}
