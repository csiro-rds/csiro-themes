<?php
AppController::getInstance()->removeAllPlugins();
$va_notification_types = array(
    array(
        'class' => 'alert-danger',
        'glyph' => 'glyphicon-exclamation-sign'
    ),
    array(
        'class' => 'alert-warning',
        'glyph' => 'glyphicon-warning-sign'
    ),
    array(
        'class' => 'alert-info',
        'glyph' => 'glyphicon-info-sign'
    )
);
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
                        <h1 class="modal-title"><?php print _t('Logged out'); ?></h1>
                    </div>
                    <div class="panel-body text-center">
                        <img src="<?php print $this->request->getUrlPathForThemeFile('/graphics/logos/' . $this->request->config->get('login_logo')); ?>">
                        <p><?php print $this->request->config->get("app_display_name"); ?></p>
                        <?php if ($this->getVar('notifications')): ?>
                            <ul class="list-unstyled">
                                <?php foreach($this->getVar('notifications') as $va_notification): ?>
                                    <li class="alert <?php print $va_notification_types[$va_notification['type']]['class']; ?>">
                                        <span class="glyphicon <?php print $va_notification_types[$va_notification['type']]['glyph']; ?>"></span>
                                        <?php print $va_notification['message']; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div class="panel-footer text-center">
                        <a href="<?php print caNavUrl($this->request, 'system/auth', 'login', ''); ?>" class="btn btn-primary">
                            <span class="glyphicon glyphicon-log-in"></span>
                            <?php print _t("Login again"); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
