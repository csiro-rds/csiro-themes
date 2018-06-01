<header class="panel panel-default">
    <div class="panel-body">
        <div class="text-center">
            <a href="<?php print $this->request->getBaseUrlPath(); ?>/">
                <img src="<?php print $this->request->getUrlPathForThemeFile('graphics/logos/' . $this->request->config->get('header_img')); ?>" border="0" alt="<?php print _t("CollectiveAccess"); ?>" />
            </a>
        </div>
        <div class="small text-center">
            <strong class="text-secondary">National Research Collections Australia</strong>
        </div>
        <?php if ($this->request->isLoggedIn()): ?>
            <div class="small text-center">
                <?php print _t('Welcome, %1', $this->request->user->getName()); ?>
            </div>
            <div class="small text-center">
                <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/viewSettings.php')); ?>
                &bull;
                <a href="<?php print caNavUrl($this->request, 'system', 'Preferences', 'EditUIPrefs'); ?>">
                    <span class="glyphicon glyphicon-cog"></span>
                    <?php print _t('Preferences'); ?>
                </a>
                &bull;
                <a href="<?php print caNavUrl($this->request, 'system', 'auth', 'logout'); ?>">
                    <span class="glyphicon glyphicon-log-out"></span>
                    <?php print _t('Logout'); ?>
                </a>
            </div>
        <?php else: ?>
            <div class="small text-center">
                <a href="<?php print caNavUrl($this->request, 'system', 'auth', 'login'); ?>">
                    <span class="glyphicon glyphicon-log-in"></span>
                    <?php print _t('Login'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>
