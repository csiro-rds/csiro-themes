<?php
require_once(__CA_LIB_DIR__.'/ca/DashboardManager.php');
AssetLoadManager::register('draggableUI');
AssetLoadManager::register('dashboard');
$o_dashboard_manager = DashboardManager::load($this->request);
?>
<div id="page-dashboard">
    <div class="dashboard-controls">
        <div class="clearfix">
            <div class="pull-left">
                <a href="<?php print caNavUrl($this->request, '', 'Dashboard', 'clear'); ?>" id="dashboard-clear" class="btn btn-default btn-sm">
                    <span class="glyphicon glyphicon-remove-circle"></span>
                    <?php print _t('Clear dashboard'); ?>
                </a>
            </div>
            <div class="btn-group btn-group-sm pull-right">
                <a href="#" onclick="window.caDashboard.editDashboard(1);" class="btn btn-default btn-sm" id="dashboard-edit">
                    <span class="glyphicon glyphicon-edit"></span>
                    <?php print _t('Edit dashboard'); ?>
                </a>
                <a href="#" onclick="jQuery('#dashboardWidgetPanel').modal('show'); return false;" class='btn btn-default btn-sm' id="dashboard-add">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php print _t('Add widget'); ?>
                </a>
                <a href="#" onclick="window.caDashboard.editDashboard(0);" class='btn btn-success btn-sm' id="dashboard-done">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?php print _t('Done'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="dashboard">
        <div class="well well-sm" id="dashboard-welcome">
            <?php print _t('This is your CollectiveAccess dashboard.  Click the "Edit Dashboard" button above to add widgets to your dashboard that will allow you to monitor system activity.  You\'ll see your dashboard whenever you login or  click the CollectiveAccess logo above.'); ?>
        </div>
        <div class="well well-sm" id="dashboard-help">
            <?php print _t('Use the button above to add a widget to your dashboard.  You can drag and drop the widgets in the left or right columns in the order you would like them to appear.  To customize the information in each widget, click the <i>"i"</i> button in the upper right corner of the widget.  To remove the widget from your dashboard click the "X" button in the upper right corner of the widget.  Click the "Clear dashboard" button above to remove all widgets from your dashboard.  When you are finished editing your dashboard, click the "Done" button above.'); ?>
        </div>
        <div class="row">
            <?php foreach (array(1, 2) as $pn_column): ?>
                <?php
                $va_widget_list = $o_dashboard_manager->getWidgetsForColumn($pn_column);
                ?>
                <div class="col-md-6 col-sm-6 dashboard-column" id="dashboard_column_<?php print $pn_column; ?>">
                    <div class="well well-sm dashboard-landing" id="dashboardWidget_placeholder_<?php print $pn_column; ?>">
                        <?php print _t("To place a dashboard widget in this column drag it here"); ?>
                    </div>
                    <?php foreach ($va_widget_list as $vn_i => $va_widget_info): ?>
                        <div class="panel panel-default portlet" id="dashboardWidget_<?php print $pn_column; ?>_<?php print $vn_i; ?>">
                            <div class="panel-heading">
                                <div class="pull-right">
                                    <?php if ($o_dashboard_manager->widgetHasSettings($va_widget_info['widget'])): ?>
                                        <a href="#" onclick="jQuery('#content_<?php print $va_widget_info['widget_id']; ?>').load('<?php print caNavUrl($this->request, '', 'Dashboard', 'getSettingsForm'); ?>', { widget_id: '<?php print $va_widget_info['widget_id'] ?>' }); return false;" class="dashboard-widget-settings">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php print caNavUrl($this->request, '', 'Dashboard', 'removeWidget', $va_widget_info); ?>" class="dashboard-widget-remove">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>
                                <?php echo WidgetManager::getWidgetTitle($va_widget_info['widget']); ?>
                            </div>
                            <div class="panel-body" id="content_<?php print $va_widget_info['widget_id']; ?>">
                                <?php print $o_dashboard_manager->renderWidget($va_widget_info['widget'], $va_widget_info['widget_id'], $va_widget_info['settings']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="dashboardWidgetPanel" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <h3 class="modal-title"><?php print _t('Add a Widget'); ?></h3>
                </div>
                <div id="dashboardWidgetPanelContent" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Cancel'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        'use strict';

        $(function () {
            // create javascript dashboard UI object - handles logic for the Javascript elements of the dashboard
            window.caDashboard = caUI.initDashboard({
                reorderURL: '<?php print caNavUrl($this->request, '', 'Dashboard', 'moveWidgets'); ?>',
                dashboardClass: 'dashboard',
                columnClass: 'dashboard-column',
                landingClass: 'dashboard-landing',
                widgetClass: 'portlet',
                widgetRemoveClass: 'dashboard-widget-remove',
                widgetSettingsClass: 'dashboard-widget-settings',
                addID: 'dashboard-add',
                editID: 'dashboard-edit',
                doneEditingID: 'dashboard-done',
                clearID: 'dashboard-clear',
                welcomeMessageID: 'dashboard-welcome',
                editMessageID: 'dashboard-help'
            });

            // load list of widgets
            $("#dashboardWidgetPanelContent").load('<?php print caNavUrl($this->request, '', 'Dashboard', 'getAvailableWidgetList'); ?>');
        });
    }(jQuery));
</script>
