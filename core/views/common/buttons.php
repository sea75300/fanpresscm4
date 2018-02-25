<?php /* @var $theView fpcm\view\viewVars */ ?>
<?php if ($theView->buttons || $theView->pager) : ?>
<div class="fpcm-ui-background-white-50p" id="fpcm-ui-toolbar">
    <?php if ($theView->buttons) : ?>
    <div class="fpcm-ui-toolbar fpcm-ui-float-left">
        <?php foreach ($theView->buttons as $button) : ?><?php print $button; ?><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($theView->pager) : ?>
    <div class="fpcm-ui-toolbar fpcm-ui-float-right">
        <?php print $theView->pager; ?>
    </div>
    <?php endif; ?>
    <div class="fpcm-ui-clear"></div>
</div>
<?php endif; ?>