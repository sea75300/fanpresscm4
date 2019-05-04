<?php /* @var $theView \fpcm\view\viewVars */ ?>
<div class="fpcm-content-wrapper">
    <div class="fpcm-ui-tabs-general">
        <ul>
            <li><a href="#tabs-user"><?php $theView->write('USERS_EDIT'); ?></a></li>
            <li><a href="#tabs-user-meta"><?php $theView->write('USERS_META_OPTIONS'); ?></a></li>
        </ul>            

        <div id="tabs-user">                
           <?php include $theView->getIncludePath('users/usereditor.php'); ?>
        </div>

        <div id="tabs-user-meta">                
           <?php include $theView->getIncludePath('users/editormeta.php'); ?>
        </div>            
    </div>
</div>