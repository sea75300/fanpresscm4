<?php /* @var $theView \fpcm\view\viewVars */ ?>

<fieldset>
    <legend><?php $theView->write('GLOBAL_EXTENDED'); ?></legend>

    <div class="row mx-3">
        
    <?php if (!$article->getArchived()) : ?>
        <?php $theView->checkbox('article[pinned]')->setText('EDITOR_PINNED')->setSelected($article->getPinned())->setClass('fpcm-ui-editor-metainfo-checkbox')->setData(['icon' => 'pinned'])->setSwitch(true); ?>
        <?php $theView->checkbox('article[draft]')->setText('EDITOR_DRAFT')->setSelected($article->getDraft())->setClass('fpcm-ui-editor-metainfo-checkbox')->setData(['icon' => 'draft'])->setSwitch(true); ?>
    <?php endif; ?>

        <?php $theView->checkbox('article[comments]')->setText('EDITOR_COMMENTS')->setSelected($article->getComments())->setClass('fpcm-ui-editor-metainfo-checkbox')->setData(['icon' => 'comments'])->setSwitch(true); ?>
        <?php if (!$approvalRequired) : ?><?php $theView->checkbox('article[approval]')->setText('EDITOR_STATUS_APPROVAL')->setSelected($article->getApproval())->setClass('fpcm-ui-editor-metainfo-checkbox')->setData(['icon' => 'approval'])->setSwitch(true); ?><?php endif; ?>
        <?php if ($editorMode && $theView->permissions->article->archive) : ?><?php $theView->checkbox('article[archived]')->setText('EDITOR_ARCHIVE')->setSelected($article->getArchived())->setClass('fpcm-ui-editor-metainfo-checkbox')->setData(['icon' => 'archived'])->setSwitch(true); ?><?php endif; ?>

    </div>
    
    <?php if ($changeAuthor) : ?>
    <div class="mx-3">
        
        <div class="row">
            
            <div class="col-12 col-md-8">
        <?php $theView->select('article[author]')
                ->setOptions($changeuserList)
                ->setSelected($article->getCreateuser())
                ->setFirstOption(fpcm\view\helper\select::FIRST_OPTION_DISABLED)
                ->setText('EDITOR_CHANGEAUTHOR')
                ->prependLabel(); ?>
                
            </div>
            
        </div>
        
    </div>
    <?php endif; ?>
</fieldset>

<?php if (!$editorMode || $article->getPostponed()) : ?>
<fieldset>
    <legend><?php $theView->write('EDITOR_POSTPONETO'); ?></legend>

    <div class="row my-2">
        <div class="col">
            <?php $theView->checkbox('article[postponed]')
                    ->setText('EDITOR_POSTPONETO')
                    ->setSelected($article->getPostponed())
                    ->setClass('fpcm-ui-editor-metainfo-checkbox')
                    ->setData(['icon' => 'postponed'])
                    ->setSwitch(true); ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="row">
                <?php $theView->dateTimeInput('article[postponedate]')
                    ->setText('EDITOR_POSTPONED_DATE')
                    ->setPlaceholder((string) $theView->dateText($postponedTimer, 'Y-m-d'))
                    ->setValue($theView->dateText($postponedTimer, 'Y-m-d'))
                    ->setIcon('calendar-plus')
                    ->setSize('lg')
                    ->setData(['mindate' => '0d', 'maxdate' => '+2m']); ?>
            </div>
        </div>
        
        <div class="col-12 col-md-4 col-lg-2">
            <div class="row">
                
                <div class="col align-self-center">
                    <?php $theView->textInput('article[postponehour]')->setText('')->setClass('fpcm-ui-spinner-hour')->setValue($theView->dateText($postponedTimer, 'H')); ?>
                </div>

                <div class="col align-self-center">            
                    <?php $theView->textInput('article[postponeminute]')->setText('')->setClass('fpcm-ui-spinner-minutes')->setValue($theView->dateText($postponedTimer, 'i')); ?>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<?php endif; ?>

<?php if ($showTwitter && !empty($twitterReplacements)) : ?>
<fieldset>
    <legend><?php $theView->write('EDITOR_TWEET_ENABLED'); ?></legend>
    <div class="row my-2">
        <div class="col">
            <?php $theView->checkbox('article[tweet]')
                    ->setText('EDITOR_TWEET_ENABLED')
                    ->setSelected($article->tweetCreationEnabled())
                    ->setSwitch(true); ?>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-12 col-md-8">
            <div class="row">
                <?php $theView->textInput('article[tweettxt]')
                        ->setPlaceholder($twitterTplPlaceholder)
                        ->setText('EDITOR_TWEET_TEXT')
                        ->setValue('')
                        ->setSize(280)
                        ->setIcon('twitter', 'fab')
                        ->setSize('lg'); ?>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="row">
                
                <div class="col align-self-center">
                    <?php $theView->select('twitterReplacements')->setOptions($twitterReplacements)->setFirstOption(\fpcm\view\helper\select::FIRST_OPTION_DISABLED); ?>
                </div>

            </div>
        </div>
    </div>    
</fieldset>
<?php endif; ?>

<fieldset>
    <legend><?php $theView->write('TEMPLATE_ARTICLE_SOURCES'); ?></legend>
    <div class="row mb-3">
        <div class="col-12 col-md-8">
            <div class="row">
            <?php $theView->textInput('article[sources]')
                    ->setPlaceholder('http://')
                    ->setText('TEMPLATE_ARTICLE_SOURCES')
                    ->setValue($article->getSources())
                    ->setIcon('external-link-alt')
                    ->setSize('lg'); ?>
                
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php $theView->write('TEMPLATE_ARTICLE_ARTICLEIMAGE'); ?></legend>
    <div class="row mb-3">
        <div class="col-12 col-md-8">
            <div class="row">
            <?php $theView->textInput('article[imagepath]')
                ->setType('url')
                ->setPlaceholder('http://')
                ->setText('TEMPLATE_ARTICLE_ARTICLEIMAGE')
                ->setValue($article->getImagepath())
                ->setMaxlenght(512)
                ->setIcon('image')
                ->setSize('lg'); ?>
            </div>
        </div>        
        <div class="col-12 col-md-1">
            <div class="row">
            <?php $theView->button('insertarticleimg', 'insertarticleimg')->setText('HL_FILES_MNG')->setIcon('image')->setIconOnly(true); ?>
            </div>
        </div>        
    </div>
</fieldset>

<?php if ($showShares && count($shares)) : ?>
<fieldset>
    <legend><?php $theView->write('EDITOR_SHARES'); ?></legend>
    <?php foreach ($shares as $share) : ?>
    <div class="row g-0">
        <div class="col-2 col-lg-1"><?php print $share->getIcon(); ?></div>
        <div class="col-6 col-lg-2 align-self-center"><?php print $share->getDescription(); ?>:</div>
        <div class="col-4 col-lg-1 align-self-center fpcm-ui-center"><?php print $share->getSharecount(); ?></div>
        <div class="col-12 col-lg-auto align-self-center"><?php $theView->icon('clock', 'far')->setText('EDITOR_SHARES_LAST'); ?> <?php $theView->dateText($share->getLastshare()); ?></div>
    </div>
    <?php endforeach; ?>
</fieldset>
<?php endif; ?>