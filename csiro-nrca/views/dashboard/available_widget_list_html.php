<?php
$o_widget_manager = $this->getVar('widget_manager');
$va_widget_list = $o_widget_manager->getWidgetNames();
?>
<?php print caFormTag($this->request, 'addWidget', 'caWidgetManagerForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
    <input type="hidden" name="widget" value="" id="caWidgetManagerFormWidgetValue" />
    <?php foreach($va_widget_list as $vs_widget_name): ?>
        <?php if (WidgetManager::checkWidgetStatus($vs_widget_name)["available"]): ?>
            <a href="#" onclick="jQuery('#caWidgetManagerFormWidgetValue').val('<?php print $vs_widget_name; ?>'); jQuery('#caWidgetManagerForm').submit();">
                <span class="glyphicon glyphicon-plus"></span>
                <?php print $o_widget_manager->getWidgetTitle($vs_widget_name); ?>
            </a>
            <p><?php print $o_widget_manager->getWidgetDescription($vs_widget_name); ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
</form>