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
                <?php print caFormTag($this->request, 'DoReset', 'reset'); ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h1 class="modal-title"><?php print _t('Reset password'); ?></h1>
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
                            <?php if ($this->getVar('renderForm')): ?>
                                <div class="form-group text-left">
                                    <label for="password"><?php print _t("Password"); ?></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" />
                                </div>
                                <div class="form-group text-left">
                                    <label for="password2"><?php print _t("Retype password"); ?></label>
                                    <input type="password" class="form-control" id="password2" name="password2" placeholder="Enter your password again for confirmation" />
                                </div>
                                <input type="hidden" name="token" value="<?php print $this->getVar('token'); ?>"/>
                                <input type="hidden" name="username" value="<?php print $this->getVar('username'); ?>"/>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                                    <?php print _t("Invalid user or token"); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="panel-footer text-center">
                            <div class="btn-group">
                                <a href="<?php print caNavLink($this->request, 'system/auth', 'login', ''); ?>" class="btn btn-default">
                                    <span class="glyphicon glyphicon-remove"></span>
                                    <?php print _t("Cancel"); ?>
                                </a>
                                <?php if ($this->getVar('renderForm')): ?>
                                    <button class="btn btn-primary">
                                        <span class="glyphicon glyphicon-save"></span>
                                        <?php print _t("Submit"); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
