<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\events\fileupload;

/**
 * Module-Event: phpBefore
 * 
 * Event wird ausgeführt, wenn Liste von Cronjob-Dateien für asynchrone Ausführung geladen wird
 * Parameter: array mit Dateiliste von Cronjob-Classen
 * Rückgabe: array mit Dateiliste von Cronjob-Classen
 * 
 * @author Stefan Seehafer aka imagine <fanpress@nobody-knows.org>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 * @package fpcm\events
 */
final class phpBefore extends \fpcm\events\abstracts\eventReturnArray {

}
