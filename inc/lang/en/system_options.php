<?php

/**
 * System options language file
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
$lang = array(
    'SYSTEM_HL_OPTIONS_GENERAL' => 'General',
    'SYSTEM_HL_OPTIONS_EDITOR' => 'Editor and File Manager',
    'SYSTEM_HL_OPTIONS_ARTICLES' => 'Articles',
    'SYSTEM_HL_OPTIONS_COMMENTS' => 'Comments',
    'SYSTEM_HL_OPTIONS_SECURITY' => 'Security and Maintenance',
    'SYSTEM_HL_OPTIONS_TWITTER' => 'Twitter Connection',
    'SYSTEM_HL_OPTIONS_SYSCHECK' => 'System Check',
    'SYSTEM_OPTIONS_MAINTENANCE' => 'Maintenance mode enabled',
    'SYSTEM_OPTIONS_CRONJOBS' => 'Asynchronous cronjob execution disabled',
    'SYSTEM_OPTIONS_URL' => 'Base-URL for article links',
    'SYSTEM_OPTIONS_LANG' => 'Language',
    'SYSTEM_OPTIONS_DATETIMEMASK' => 'Date-Time mask',
    'SYSTEM_OPTIONS_DATETIMEMASK_HELP' => 'Syntax equivalent to PHP function date()',
    'SYSTEM_OPTIONS_TIMEZONE' => 'Timezone',
    'SYSTEM_OPTIONS_SESSIONLENGHT' => 'ACP session length',
    'SYSTEM_OPTIONS_SESSIONLENGHT_INTERVALS' => array(
        '30 minutes' => 1800,
        '1 hour' => 3600,
        '1,5 hours' => 5400,
        '2 hours' => 7200,
        '2,5 hours' => 9000,
        '3 hours' => 10800,
        '4 hours' => 14400,
        '5 hours' => 18000,
        '6 hours' => 21600
    ),
    'SYSTEM_OPTIONS_CACHETIMEOUT' => 'Interval for cache timeout',
    'SYSTEM_OPTIONS_CACHETIMEOUT_INTERVAL' => array(
        '30 minutes' => 1800,
        '1 hour' => 3600,
        '6 hours' => 21600,
        '12 hours' => 43200,
        '18 hours' => 64800,
        '1 day' => 86400,
        '2 days' => 172800,
        '3 days' => 259200,
        '4 days' => 345600,
        '5 days' => 432000,
        '6 days' => 518400,
        '1 week' => 604800
    ),
    'SYSTEM_OPTIONS_USEMODE' => 'Usemode',
    'SYSTEM_OPTIONS_USEMODE_IFRAME' => 'iframe',
    'SYSTEM_OPTIONS_USEMODE_PHPINCLUDE' => 'phpinclude',
    'SYSTEM_OPTIONS_INCLUDEJQUERY' => 'Load jQuery library in frontend',
    'SYSTEM_OPTIONS_INCLUDEJQUERY_YES' => 'Select &quot;yes&quot;, if jQuery is not loaded in your site yet.',
    'SYSTEM_OPTIONS_STYLESHEET' => 'CSS style file path',
    'SYSTEM_OPTIONS_NEWSSHOWLIMIT' => 'Articles per public page',
    'SYSTEM_OPTIONS_ACTIVENEWSTEMPLATE' => 'Article list template',
    'SYSTEM_OPTIONS_ACTIVENEWSTEMPLATESINGLE' => 'Single article template',
    'SYSTEM_OPTIONS_LATESTNEWSTEMPLATE' => '&quot;Lastest News&quot; Template',
    'SYSTEM_OPTIONS_NEWSSHOWSHARELINKS' => 'Show share buttons',
    'SYSTEM_OPTIONS_NEWSSHOWIMGTHUMBSIZE' => 'Thumbnail size',
    'SYSTEM_OPTIONS_NEWSSHOWMAXIMGSIZEPIXELS' => 'Pixel',
    'SYSTEM_OPTIONS_NEWSSHOWMAXIMGSIZEWIDTH' => 'Breite',
    'SYSTEM_OPTIONS_NEWSSHOWMAXIMGSIZEHEIGHT' => 'Höhe',
    'SYSTEM_OPTIONS_FILEMANAGER_LIMIT' => 'Number of images per page',
    'SYSTEM_OPTIONS_NEWS_EDITOR_SETTINGS' => 'Editor settings',
    'SYSTEM_OPTIONS_NEWS_EDITOR' => 'Select editor',
    'SYSTEM_OPTIONS_NEWS_EDITOR_STD' => 'TinyMCE',
    'SYSTEM_OPTIONS_NEWS_EDITOR_CLASSIC' => 'CodeMirror',
    'SYSTEM_OPTIONS_NEWS_EDITOR_CSS' => 'CSS classes in editor',
    'SYSTEM_OPTIONS_NEWS_EDITOR_FONTSIZE' => 'Default editor font size',
    'SYSTEM_OPTIONS_NEWS_EDITOR_IMGTOOLS' => 'Save image changes in TinyMCE in file system',
    'SYSTEM_OPTIONS_NEWS_NEWUPLOADER' => 'Use jQuery uploader',
    'SYSTEM_OPTIONS_NEWS_ARCHIVELIMIT' => 'Show articles in archiv from',
    'SYSTEM_OPTIONS_NEWS_ARCHIVELIMIT_EMPTY' => 'keep empty for no limit',
    'SYSTEM_OPTIONS_NEWS_URLREWRITING' => 'Enable URL rwriting for article links',
    'SYSTEM_OPTIONS_NEWS_REVISIONS_LIMIT' => 'Delete old revisions, if older then',
    'SYSTEM_OPTIONS_NEWS_REVISIONS_LIMIT_LIST' => array(
        'never' => 0,
        '1 week' => 604800,
        '2 weeks' => 1209600,
        '3 weeks' => 1814400,
        '4 weeks' => 2419200,
        '6 weeks' => 3628800,
        '8 weeks' => 4838400,
        '3 months' => 7257600,
        '6 months' => 14515200,
        '12 months' => 31449600,
        '18 months' => 43545600,
        '24 months' => 62899200
    ),
    'SYSTEM_OPTIONS_ARCHIVE_LINK' => 'Show archive link',
    'SYSTEM_OPTIONS_ACTIVECOMMENTTEMPLATE' => 'Comment template',
    'SYSTEM_OPTIONS_COMMENTFORMTEMPLATE' => 'Comment form template',
    'SYSTEM_OPTIONS_CAPTCHASETTING' => 'Captcha-Einstellungen',
    'SYSTEM_OPTIONS_ANTISPAMQUESTION' => 'Spam captcha question',
    'SYSTEM_OPTIONS_ANTISPAMANSWER' => 'Spam captcha answer',
    'SYSTEM_OPTIONS_ACPARTICLES_LIMIT' => 'Number of items per ACP page',
    'SYSTEM_OPTIONS_LOGIN_MAXATTEMPTS' => 'Login auto-lock',
    'SYSTEM_OPTIONS_FLOODPROTECTION' => 'Flood protection between two comments',
    'SYSTEM_OPTIONS_FLOODPROTECTION_INTERVALS' => array(
        'none' => 0,
        '10 seconds' => 10,
        '20 seconds' => 20,
        '30 seconds' => 30,
        '60 seconds' => 60,
        '90 seconds' => 90,
        '2 minutes' => 120,
        '5 minutes' => 300,
        '10 minutes' => 600,
        '15 minutes' => 900,
        '30 minutes' => 1800,
        '60 minutes' => 3600,
    ),
    'SYSTEM_OPTIONS_COMMENTEMAIL' => 'Email address required',
    'SYSTEM_OPTIONS_COMMENT_APPROVE' => 'Comment Approval required',
    'SYSTEM_OPTIONS_COMMENT_NOTIFY' => 'Send comment notification to',
    'SYSTEM_OPTIONS_COMMENT_NOTIFY_GLOBAL' => 'global email address',
    'SYSTEM_OPTIONS_COMMENT_NOTIFY_AUTHOR' => 'author email address',
    'SYSTEM_OPTIONS_COMMENT_NOTIFY_ALL' => 'global and author email address',
    'SYSTEM_OPTIONS_COMMENT_ENABLED_GLOBAL' => 'Comments are enabled',
    'SYSTEM_OPTIONS_COMMENT_MARKSPAM_PASTCHECK' => 'Automatic "Mark as Spam"',
    'SYSTEM_OPTIONS_REVISIONS_ENABLED' => 'Revisions enabled',
    'SYSTEM_OPTIONS_NEWS_SORTING' => 'Sort articles by',
    'SYSTEM_OPTIONS_NEWS_BYWRITTENTIME' => 'Creation date',
    'SYSTEM_OPTIONS_NEWS_BYEDITEDTIME' => 'Last change',
    'SYSTEM_OPTIONS_NEWS_BYINTERNALID' => 'Internal ID',
    'SYSTEM_OPTIONS_NEWS_BYAUTHOR' => 'Author',
    'SYSTEM_OPTIONS_NEWS_ORDERASC' => 'ascending',
    'SYSTEM_OPTIONS_NEWS_ORDERDESC' => 'descending',
    'SYSTEM_OPTIONS_NEWS_ENABLEFEED' => 'RSS feed is enabled',
    'SYSTEM_OPTIONS_EXTENDED_UPDATES' => 'Update-Settings',
    'SYSTEM_OPTIONS_EXTENDED_EMAILSUBMISSION' => 'E-Mail submission',
    'SYSTEM_OPTIONS_EXTENDED_EMAILUPDATES' => 'Email notification when updates are available',
    'SYSTEM_OPTIONS_EXTENDED_DEVUPDATES' => 'Include development releases in update check',
    'SYSTEM_OPTIONS_EXTENDED_UPDATESMANCHK' => 'Update check interval if unable to connect to external servers',
    'SYSTEM_OPTIONS_TWITTER_CONNECTION' => 'Twitter connection',
    'SYSTEM_OPTIONS_TWITTER_CONNECT' => 'Request API key and/or token',
    'SYSTEM_OPTIONS_TWITTER_DISCONNECT' => 'Disconnect',
    'SYSTEM_OPTIONS_TWITTER_EVENTS' => 'Create tweet on',
    'SYSTEM_OPTIONS_TWITTER_CONSUMER_KEY' => 'Consumer Key (API Key)',
    'SYSTEM_OPTIONS_TWITTER_CONSUMER_SECRET' => 'Consumer Secret (API Secret)',
    'SYSTEM_OPTIONS_TWITTER_USER_TOKEN' => 'Access Token',
    'SYSTEM_OPTIONS_TWITTER_USER_SECRET' => 'Access Token Secret',
    'SYSTEM_OPTIONS_TWITTER_ACTIVE' => 'Twitter connection is enabled! Connected with user "{{screenname}}".',
    'SYSTEM_OPTIONS_TWITTER_EVENTCREATE' => 'On create',
    'SYSTEM_OPTIONS_TWITTER_EVENTUPDATE' => 'On update',
    'SYSTEM_OPTIONS_EMAIL_ENABLED' => 'Submit e-mail via SMTP',
    'SYSTEM_OPTIONS_EMAIL_SERVER' => 'SMTP server address',
    'SYSTEM_OPTIONS_EMAIL_PORT' => 'SMTP server port',
    'SYSTEM_OPTIONS_EMAIL_USERNAME' => 'SMTP username',
    'SYSTEM_OPTIONS_EMAIL_PASSWORD' => 'SMTP password',
    'SYSTEM_OPTIONS_EMAIL_ENCRYPTED' => 'SMTP-encryption',
    'SYSTEM_OPTIONS_EMAIL_ACTIVE' => 'Check of credentials successfully. E-mails are delivered via SMTP.',
    'SYSTEM_OPTIONS_CRONINTERVALS' => array(
        'every request' => 0,
        '60 seconds' => 60,
        '90 seconds' => 90,
        'every 2 minutes' => 120,
        'every 5 minutes' => 300,
        'every 10 minutes' => 600,
        'every 15 minutes' => 900,
        'every 30 minutes' => 1800,
        'every 60 minutes' => 3600,
        'every 2 hours' => 7200,
        'every 3 hours' => 10800,
        'every 6 hours' => 21600,
        'every 12 hours' => 43200,
        'daily' => 86400,
        'every 2 days' => 172800,
        'weekly' => 604800,
        'every two weeks' => 1209600,
        'monthly' => 2419200
    ),
    'SYSTEM_OPTIONS_UPDATESMANUAL' => array(
        'daily' => 86400,
        'weekly' => 604800,
        'every two weeks' => 1209600,
        'monthlay' => 2419200
    )
);
