<?php
$t_nav = $this->getVar('nav');
$va_breadcrumb = $t_nav->getDestinationAsBreadCrumbTrail();
$vs_window_title = trim(MetaTagManager::getWindowTitle());
if (!$vs_window_title && is_array($va_breadcrumb) && sizeof($va_breadcrumb)) {
    $vs_window_title = array_pop($va_breadcrumb);
}
if ($vs_window_title) {
    $vs_window_title = ' ' . $vs_window_title;
}
$vs_favicon = $this->request->getUrlPathForThemeFile('graphics/logos/favicon.ico');

$o_debugbar_renderer = Debug::$bar->getJavascriptRenderer();
$o_debugbar_renderer->setBaseUrl(__CA_URL_ROOT__.$o_debugbar_renderer->getBaseUrl());
?>
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
<title><?php print $this->appconfig->get("window_title") . $vs_window_title; ?></title>
<link rel="shortcut icon" href="<?php print $vs_favicon; ?>" />
<link rel="icon" href="<?php print $vs_favicon; ?>" />
<?php print MetaTagManager::getHTML(); ?>
<?php print AssetLoadManager::getLoadHTML($this->request); ?>
<script>
    (function () {
        'use strict';

        window.caBasePath = '<?php print $this->request->getBaseUrlPath(); ?>';
        caUI.initUtils({
            unsavedChangesWarningMessage: '<?php _p('You have made changes in this form that you have not yet saved. If you navigate away from this form you will lose your unsaved changes.'); ?>'
        });
    }());
</script>
<?php if ($vs_local_css_url_path = $this->request->getUrlPathForThemeFile("css/local.css")): ?>
    <link rel='stylesheet' href='{$vs_local_css_url_path}' type='text/css' media='screen' />
<?php endif; ?>
<?php if(Debug::isEnabled()): ?>
    <?php print $o_debugbar_renderer->renderHead(); ?>
<?php endif; ?>
