<?php if ($mode == 2) : ?>
<div class="fpcm-inner-wrapper">
<?php endif; ?>
    
    <div class="fpcm-tabs-general">
        <ul>
            <li id="tabs-files-list-reload"><a href="#tabs-files-list"><?php $theView->write('FILE_LIST_AVAILABLE'); ?></a></li>                
            <?php if ($permUpload) : ?><li><a href="#tabs-files-upload"><?php $theView->write('FILE_LIST_UPLOADFORM'); ?></a></li><?php endif; ?>                
        </ul>

        <div id="tabs-files-list">
            <div id="tabs-files-list-content"><?php include $theView->getIncludePath('filemanager/listinner.php'); ?></div>
        </div>

        <?php if ($permUpload) : ?>
        <div id="tabs-files-upload">
            <?php if ($newUploader) : ?>
                <?php include $theView->getIncludePath('filemanager/forms/jqupload.php'); ?>
            <?php else : ?>
                <?php include $theView->getIncludePath('filemanager/forms/phpupload.php'); ?>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<?php include $theView->getIncludePath('filemanager/searchform.php'); ?>