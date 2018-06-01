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
                <?php print caFormTag($this->request, 'DoLogin', 'login'); ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h1 class="modal-title"><?php print _t('Login'); ?></h1>
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
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="form-group text-left">
                                        <label for="username"><?php print _t("User Name"); ?></label>
                                        <input id="username" class="form-control" name="username" placeholder="Enter your username" />
                                    </div>
                                </div>
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="form-group text-left">
                                        <label for="password"><?php print _t("Password"); ?></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <div class="btn-group">
                                <?php if (AuthenticationManager::supports(__CA_AUTH_ADAPTER_FEATURE_RESET_PASSWORDS__)): ?>
                                    <a href="<?php print caNavUrl($this->request, 'system/auth', 'forgot', ''); ?>" class="btn btn-default">
                                        <span class="glyphicon glyphicon-user"></span>
                                        <?php print _t("Forgot your password?"); ?>
                                    </a>
                                <?php elseif (AuthenticationManager::getAccountManagementLink()): ?>
                                    <a href="<?php print AuthenticationManager::getAccountManagementLink(); ?>" class="btn btn-default" target="_blank">
                                        <span class="glyphicon glyphicon-user"></span>
                                        <?php print _t("Manage your account"); ?>
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-primary">
                                    <span class="glyphicon glyphicon-log-in"></span>
                                    <?php print _t("Login"); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input name="redirect" type="hidden" value="<?php echo $this->getVar('redirect'); ?>" />
                    <input type="hidden" name="_screen_width" value="" />
                    <input type="hidden" name="_screen_height" value="" />
                    <input type="hidden" name="_has_pdf_plugin" value="" />
                </form>
            </div>
        </div>
    </div>
    <script>
        (function ($) {
            'use strict';

            $(function() {
                var pdfInfo = caUI.utils.getAcrobatInfo();
                $('input[name=_screen_width]').val(screen.width);
                $('input[name=_screen_height]').val(screen.height);
                $('input[name=_has_pdf_plugin]').val((pdfInfo && pdfInfo['acrobat'] && (pdfInfo['acrobat'] === 'installed')) ? 1 : 0);
            });
        }(jQuery));
    </script>
</body>
</html>
