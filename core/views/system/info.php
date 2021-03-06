<?php /* @var $theView fpcm\view\viewVars */ ?>
<div class="fpcm-content-wrapper fpcm-ui-padding-lg-top">
    <div class="fpcm-ui-tabs-general">
        <ul>
            <li><a href="#tabs-help-general"><?php $theView->write('HL_HELP_SUPPORT'); ?></a></li>
        </ul>
        <div>
            <div class="row">
                <div class="col-12">
                    <h3 class="fpcm-ui-margin-lg-top fpcm-ui-margin-lg-bottom"><?php $theView->write('SYSTEM_HL_OPTIONS_GENERAL'); ?></h3>
                    <?php print $content; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3 class="fpcm-ui-margin-lg-bottom"><?php $theView->write('VERSION'); ?></h3>
                    <p><?php print $theView->version; ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 fpcm-ui-padding-lg-bottom">
                    <h3 class="fpcm-ui-margin-lg-bottom"><?php $theView->write('HL_HELP_LICENCE'); ?></h3>
                    <?php print nl2br($theView->escapeVal($licence)); ?>
                </div>
            </div>
        </div>
    </div>
</div>