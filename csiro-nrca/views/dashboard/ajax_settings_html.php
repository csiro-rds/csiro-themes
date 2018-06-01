<?php
$vs_widget_id = $this->getVar('widget_id');
$vs_form = $this->getVar('form');
?>
<div class="widget" id="caWidgetSettingForm_<?php print $vs_widget_id; ?>">
    <h3><?php print _t('Widget Settings'); ?></h3>
    <form id="caWidgetSettings_<?php print $vs_widget_id; ?>" action="#" method="get">
        <?php if ($vs_form): ?>
            <?php print $vs_form; ?>
        <?php else: ?>
            <?php print _t('No settings available'); ?>
        <?php endif; ?>
        <?php if ($vs_form): ?>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default" onclick="jQuery('#caWidgetSettingForm_<?php print $vs_widget_id; ?>').load('<?php print caNavUrl($this->request, '', 'Dashboard', 'getWidget', array()); ?>', jQuery('#caWidgetSettings_<?php print $vs_widget_id; ?>').serializeArray());">
                    <span class="glyphicon glyphicon-remove"></span>
                    <?php print _t('Cancel'); ?>
                </button>
                <button type="button" class="btn btn-success" onclick="jQuery('#caWidgetSettingForm_<?php print $vs_widget_id; ?>').load('<?php print caNavUrl($this->request, '', 'Dashboard', 'saveSettings', array()); ?>', jQuery('#caWidgetSettings_<?php print $vs_widget_id; ?>').serializeArray());">
                    <span class="glyphicon glyphicon-check"></span>
                    <?php print _t('Save'); ?>
                </button>
            </div>
        <?php endif; ?>
        <?php print caHTMLHiddenInput('widget_id', array('value' => $vs_widget_id)); ?>
    </form>
</div>

<?php print TooltipManager::getLoadHTML(); ?>
