<div class="fpcm-ui-dialog-layer fpcm-ui-hidden" id="fpcm-dialog-comments-search">
    <table class="fpcm-ui-table fpcm-ui-comments-search">
        <tr>
            <td colspan="2"><?php \fpcm\view\helper::textInput('text', 'fpcm-comments-search-input', '', false, 255, $theView->translate('ARTICLE_SEARCH_TEXT'), 'fpcm-full-width'); ?></td>
            <td><?php \fpcm\view\helper::select('searchtype', $searchTypes, 1, true, false, false, 'fpcm-comments-search-input fpcm-ui-input-select-commentsearch'); ?></td>
        </tr>
        <tr>
            <td><?php \fpcm\view\helper::select('spam', $searchSpam, null, false, false, false, 'fpcm-comments-search-input fpcm-ui-input-select-commentsearch'); ?></td>
            <td><?php \fpcm\view\helper::select('approved', $searchApproval, null, false, false, false, 'fpcm-comments-search-input fpcm-ui-input-select-commentsearch'); ?></td>
            <td><?php \fpcm\view\helper::select('private', $searchPrivate, null, false, false, false, 'fpcm-comments-search-input fpcm-ui-input-select-commentsearch'); ?></td>
        </tr>
        <tr>    
            <td><?php \fpcm\view\helper::textInput('datefrom', 'fpcm-comments-search-input fpcm-full-width-date', '', false, 10, $theView->translate('ARTICLE_SEARCH_DATE_FROM'), 'fpcm-full-width'); ?></td>
            <td><?php \fpcm\view\helper::textInput('dateto', 'fpcm-comments-search-input fpcm-full-width-date', '', false, 10, $theView->translate('ARTICLE_SEARCH_DATE_TO'), 'fpcm-full-width'); ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php \fpcm\view\helper::textInput('articleId', 'fpcm-comments-search-input', '', false, 20, $theView->translate('COMMMENT_SEARCH_ARTICLE'), 'fpcm-full-width'); ?></td>
            <td></td>
            <td><?php \fpcm\view\helper::select('combination', $searchCombination, null, false, false, false, 'fpcm-comments-search-input fpcm-ui-input-select-commentsearch'); ?></td>
        </tr>
    </table>
</div>