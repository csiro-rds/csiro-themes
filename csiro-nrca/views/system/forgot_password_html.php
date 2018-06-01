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
                <?php print caFormTag($this->request, 'RequestPassword', 'forgot'); ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h1 class="modal-title"><?php print _t('Forgot password'); ?></h1>
                        </div>
                        <div class="panel-body text-center">
                            <img src="<?php print $this->request->getUrlPathForThemeFile('/graphics/logos/' . $this->request->config->get('login_logo')); ?>">
                            <p><?php print $this->request->config->get("app_display_name"); ?></p>
                            <p><?php print _t("Enter your CollectiveAccess user name below to request a new password. We will send you an email with further instructions."); ?></p>
                            <div class="form-group text-left">
                                <label for="username"><?php print _t("User Name"); ?></label>
                                <input id="username" class="form-control" name="username" placeholder="Enter your username" />
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <div class="btn-group">
                                <button class="btn btn-primary">
                                    <span class="glyphicon glyphicon-send"></span>
                                    <?php print _t("Submit"); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
