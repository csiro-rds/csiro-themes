<?php
$t_screen = $this->getVar('t_screen');
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');

$va_to_display_items = $t_screen->getPlacementsInScreen(array('noCache' => true));
$va_available_display_items = $t_screen->getAvailableBundles();
foreach ($va_available_display_items as $vs_bundle => $va_item) {
    // strip lists of valid settings - we don't need to send them to the client and they can be fairly large
    unset($va_available_display_items[$vs_bundle]['settings']);
}
?>
<div class="component component-bundle component-bundle-editor-ui-bundle-placements" id="<?php print $vs_id_prefix; ?>">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $this->getVar('settings') ?: array()); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <span class="glyphicon glyphicon-info-sign"></span>
                <?php print _t("Drag your selection from column to column to edit the contents of the screen."); ?>
            </div>
        </div>
    </div>
    <div class="row bundle-list-container">
        <div class="col-md-6">
            <label><?php print _t("Available editor elements"); ?></label>
            <div id="bundleDisplayEditorAvailableList"></div>
        </div>
        <div class="col-md-6">
            <label><?php print _t("Elements to display"); ?></label>
            <div id="bundleDisplayEditorToDisplayList"></div>
        </div>
        <input type="hidden" id="<?php print $vs_id_prefix; ?>displayBundleList" name="<?php print $vs_id_prefix; ?>displayBundleList" value=""/>
    </div>

    <script>
        (function ($) {
            $(function () {
                caUI.bundlelisteditor({
                    availableListID: 'bundleDisplayEditorAvailableList',
                    toDisplayListID: 'bundleDisplayEditorToDisplayList',
                    displayItemClass: 'display-item',
                    displayListClass: 'display-list',
                    availableDisplayList: <?php print json_encode($va_available_display_items); ?>,
                    initialDisplayList: <?php print json_encode($va_to_display_items); ?>,
                    initialDisplayListOrder: <?php print json_encode(array_keys($va_to_display_items)); ?>,
                    displayBundleListID: '<?php print $vs_id_prefix; ?>displayBundleList',
                    settingsIcon: '<span class="glyphicon glyphicon-cog"></span>'
                });
            });
        }(jQuery));
    </script>
</div>
