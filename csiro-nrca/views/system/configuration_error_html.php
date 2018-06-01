<?php
/* TODO Allow this theme file to be used */
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
                        <h1 class="modal-title"><?php print _t('Configuration error'); ?></h1>
                    </div>
                    <p class="panel-body text-center">
                        <img src="<?php print $this->request->getUrlPathForThemeFile('/graphics/logos/' . $this->request->config->get('login_logo')); ?>">
                        <p><?php print $this->request->config->get("app_display_name"); ?></p>
                        <p>
                        An error in your system configuration has been detected.
                        </p>
                        <p>
                        General installation instructions can be found
                        <a href='http://wiki.collectiveaccess.org/index.php?title=Installation_(Providence)' target='_blank'>here</a>.
                        </p>
                        <p>
                        For more specific hints on the existing issues please have a look at the messages below.
                        </p>
                        <ul class="list-unstyled">
                            <?php foreach($opa_error_messages as $vs_message): ?>
                                <li class="alert alert-danger">
                                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                                    <?php print $vs_message; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
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
