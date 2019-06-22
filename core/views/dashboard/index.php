<?php /* @var $theView fpcm\view\viewVars */ ?>
    <div class="row no-gutters" id="fpcm-dashboard-containers">
        <div class="col-12">
            <div class="row no-gutters align-self-center fpcm-ui-inline-loader fpcm-ui-background-white-50p">
                <div class="col-12 fpcm-ui-center align-self-center">
                    <?php $theView->icon('spinner fa-inverse')->setSpinner('pulse')->setStack('circle')->setSize('2x'); ?>
                    <span class="fpcm-ui-padding-md-left"><?php $theView->write('DASHBOARD_LOADING'); ?></span>
                </div>
            </div>
        </div>
    </div>