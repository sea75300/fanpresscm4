<div class="fpcm-content-wrapper">
    <form method="post" action="<?php print $theView->self; ?>?module=system/crons">
        <div class="fpcm-tabs-general">
            <ul>
                <li><a href="#tabs-options-general"><?php $theView->write('SYSTEM_HL_OPTIONS_GENERAL'); ?></a></li>
            </ul>

            <div id="tabs-options-general">

                <table class="fpcm-ui-table fpcm-ui-logs fpcm-ui-logs-cronjobs">
                    <tr>
                        <th></th>
                        <th class="fpcm-cronjob-name"><?php $theView->write('CRONJOB_LIST_NAME'); ?></th>
                        <th class="fpcm-ui-center"><?php $theView->write('CRONJOB_LIST_INTERVAL'); ?></th>
                        <th class="fpcm-ui-center"><?php $theView->write('CRONJOB_LIST_LASTEXEC'); ?></th>
                        <th class="fpcm-ui-center"><?php $theView->write('CRONJOB_LIST_NEXTEXEC'); ?></th>
                    </tr>
                    <tr class="fpcm-td-spacer"><td></td></tr>   
                    <?php foreach ($cronjobList as $cronjob) : ?>
                    <tr <?php if ($currentTime > ($cronjob->getNextExecTime() - 60)) : ?>class="fpcm-ui-important-text"<?php endif; ?>>
                        <td class="fpcm-ui-center"><?php $theView->button($cronjob->getCronName(), $cronjob->getCronName())->setText('CRONJOB_LIST_EXECDEMAND')->setClass('fpcm-cronjoblist-exec')->setIcon('play-circle')->setIconOnly(true); ?></td>
                        <td class="fpcm-cronjob-name"><?php $theView->write('CRONJOB_'.strtoupper($cronjob->getCronName())); ?></td>
                        <td class="fpcm-cronjob-interval fpcm-ui-center"><?php fpcm\view\helper::select('intervals_'.$cronjob->getCronName(), $theView->translate('SYSTEM_OPTIONS_CRONINTERVALS'), $cronjob->getIntervalTime(), false, false, false, 'fpcm-cronjoblist-intervals'); ?></td>
                        <td class="fpcm-ui-center"><?php $theView->dateText($cronjob->getLastExecTime()); ?></td>
                        <td class="fpcm-ui-center"><?php $theView->dateText($cronjob->getNextExecTime()); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

            </div>
            
        </div>
        
    </form> 
</div>