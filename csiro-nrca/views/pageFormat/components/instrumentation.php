<?php if (Debug::isEnabled()): ?>
    <?php print Debug::$bar->getJavascriptRenderer()->render(); ?>
<?php endif; ?>
<?php if (Db::$monitor): ?>
    <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/system/monitor_html.php')); ?>
<?php endif; ?>
