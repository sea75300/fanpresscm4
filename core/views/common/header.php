<?php /* @var $theView fpcm\view\viewVars */ ?>
<!DOCTYPE HTML>
<HTML lang="<?php print $theView->langCode; ?>">
    <head>
        <title><?php $theView->write('HEADLINE'); ?></title>
        <meta http-equiv="content-type" content= "text/html; charset=utf-8">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php print $theView->themePath; ?>favicon.png" type="image/png" /> 
        <?php include_once 'includefiles.php'; ?>
        <?php include_once 'vars.php'; ?>

    </head>    

    <body class="fpcm-body <?php print $theView->bodyClass; ?>" id="fpcm-body">

        <div id="fpcm-messages"></div>

        <?php if ($theView->formActionTarget) : ?><form method="post" action="<?php print $theView->formActionTarget; ?>" enctype="multipart/form-data" id="fpcm-ui-form"><?php endif; ?>

        <header>
            <div class="row no-gutters fpcm-ui-background-white-50p">
                <div class="col-12 col-md-6 fpcm-ui-ellipsis">
                    <h1 class="fpcm-ui-padding-lg-lr"><?php $theView->icon('chevron-right '); ?> <span>FanPress CM</span> <span>News System</span></h1>
                </div>
                <div class="col-12 col-md-6 align-self-center">
                    <?php include_once $theView->getIncludePath('common/menutop.php'); ?>
                </div>                
            </div>
        </header>
            
        <nav>
            <div class="row no-gutters align-self-center">
                <div class="col-12">
                    <?php include_once $theView->getIncludePath('common/navigation.php'); ?>
                </div>
            </div>
        </nav>

        <div class="row">
            <div class="col-sm-12">
                <?php include_once $theView->getIncludePath('common/buttons.php'); ?>