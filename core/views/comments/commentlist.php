<form method="post" action="<?php print $theView->self; ?>?module=comments/list">
<div class="fpcm-content-wrapper">
    <div class="fpcm-tabs-general">
        <ul>
            <li><a href="#tabs-comments-active"><?php $theView->write('HL_COMMENTS_MNG'); ?></a></li>
        </ul>            

        <div id="tabs-comments-active">
            <?php include $theView->getIncludePath('comments/commentlist_inner.php'); ?>
        </div>
    </div>
</div>

    <?php $theView->pageTokenField('pgtkn'); ?>
</form>

<?php include $theView->getIncludePath('comments/searchform.php'); ?>