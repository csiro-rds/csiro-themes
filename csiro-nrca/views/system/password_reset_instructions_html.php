<?php
AppController::getInstance()->removeAllPlugins();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php print $this->request->config->get("app_display_name"); ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="<?php print $this->request->getUrlPathForThemeFile('css/base.css'); ?>" rel="stylesheet" type="text/css" />
    <?php print AssetLoadManager::getLoadHTML($this->request); ?>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1 class="modal-title"><?php print _t('Password reset initiated'); ?></h1>
                    </div>
                    <div class="panel-body text-center">
                        <img src="<?php print $this->request->getUrlPathForThemeFile('/graphics/logos/' . $this->request->config->get('login_logo')); ?>">
                        <p><?php print $this->request->config->get("app_display_name"); ?></p>
                        <p class="bg bg-success">
                        <?php print _t("Thank you for your request. We will send you an email with further instructions. If you don't receive the message after submitting the form, please wait a couple of minutes and also make sure to check your spam and junk folders. If you don't receive an email within 15 minutes after submitting the form, you may have misspelled your user name. Either resubmit the previous form or contact your CollectiveAccess administrator."); ?>
                        </p>
                    </div>
                    <div class="panel-footer text-center">
                        <a href="<?php print caNavUrl($this->request, 'system/auth', 'login', ''); ?>" class="btn btn-primary">
                            <span class="glyphicon glyphicon-log-in"></span>
                            <?php print _t("Login"); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
