<div class="fpcm-tabs-general" id="fpcm-editor-tabs">
    <ul>
        <?php if ($isRevision) : ?>
        <li><a href="#tabs-article"><?php $theView->write('EDITOR_STATUS_REVISION'); ?></a></li>
        <?php else : ?>
        <li id="fpcm-editor-tabs-editorregister"><a href="#tabs-article"><?php $theView->write('ARTICLES_EDITOR'); ?></a></li>
        <?php endif; ?>
        <?php if ($showComments && !$isRevision) : ?>
        <li><a href="<?php print $theView->basePath.\fpcm\classes\tools::getControllerLink('ajax/editor/editorlist', ['id' => $article->getId(), 'view' => 'comments']); ?>">
            <?php $theView->write('HL_ARTICLE_EDIT_COMMENTS', [ 'count' => $commentCount ]); ?>
        </a></li>
        <?php endif; ?>
        <?php if ($showRevisions) : ?>
        <li><a href="<?php print $theView->basePath.\fpcm\classes\tools::getControllerLink('ajax/editor/editorlist', ['id' => $article->getId(), 'view' => 'revisions']); ?>">
            <?php $theView->write('HL_ARTICLE_EDIT_REVISIONS', [ 'count' => $revisionCount ]); ?>
        </a></li>
        <?php endif; ?>
    </ul>            

    <div id="tabs-article">
        <div class="fpcm-ui-dialog-layer fpcm-ui-hidden fpcm-editor-dialog" id="fpcm-dialog-editor-html-filemanager"></div>            

        <?php include $editorFile; ?>
        <?php include $theView->getIncludePath('articles/buttons.php'); ?>
    </div>

    <?php $theView->pageTokenField('pgtkn'); ?>
</div>