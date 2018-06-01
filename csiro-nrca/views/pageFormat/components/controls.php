<?php
$t_nav = $this->getVar('nav');
$va_breadcrumb = $t_nav->getDestinationAsBreadCrumbTrail();
$vs_widgets = $t_nav->getHTMLWidgets();
$vb_show_breadcrumbs = $this->request->user->getPreference('ui_show_breadcrumbs');
$vs_trail = $vb_show_breadcrumbs ? trim(join(' <span class="glyphicon glyphicon-menu-right"></span> ', array_filter($va_breadcrumb))) : null;
?>
<?php if (($vb_show_breadcrumbs && $vs_trail) || $vs_widgets): ?>
    <aside class="panel panel-default">
        <?php if ($vb_show_breadcrumbs): ?>
            <div class="panel-heading">
                <?php echo $vs_trail ?: $vs_window_title ?: 'Home'; ?>
            </div>
        <?php endif; ?>
        <?php if ($vs_widgets): ?>
            <div class="panel-body">
                <div id="widgets">
                    <?php print $vs_widgets; ?>
                </div>
            </div>
        <?php endif; ?>
    </aside>
<?php endif; ?>
