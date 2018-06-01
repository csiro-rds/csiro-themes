<?php
AssetLoadManager::register('setEditorUI');

$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_items = $this->getVar('items');
$t_set = $this->getVar('t_set');
$vn_set_id = $t_set->getPrimaryKey();
$t_row = $this->getVar('t_row');
$vs_type_singular = $this->getVar('type_singular');
$vs_type_plural = $this->getVar('type_plural');
$va_lookup_urls = $this->getVar('lookup_urls');
$va_settings = $this->getVar('settings');
$vn_table_num = $t_set->get('table_num');
?>

<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle-set-items">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.'setItemEditor', $va_settings); ?>
    <?php if (!$vn_table_num): ?>
		<div id="<?php print $vs_id_prefix; ?>setNoItemsWarning">
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign"></span>
                <?php print _t('You must save this set before you can add items to it.'); ?>
            </div>
		</div>
    <?php else: ?>
        <div class="bundleSubLabel clearfix">
	        <?php if (is_array($va_items) && sizeof($va_items)): ?>
		        <?php print caGetPrintFormatsListAsHTMLForSetItemBundles($vs_id_prefix, $this->request, $t_set, $t_set->getItemRowIDs()); ?>
	        <?php endif; ?>
        </div>
        <div class="text-nowrap">
            <label><?php print _t('Sort by'); ?></label>
            <a href="#" onclick="setEditorOps.sort('name'); return false;"><?php print _t('name'); ?></a>&nbsp;&nbsp;
            <a href="#" onclick="setEditorOps.sort('idno'); return false;"><?php print _t('identifier'); ?></a>
        </div>
        <div id="<?php print $vs_id_prefix; ?>setItems" class="setItems">
            <div class="setEditorAddItemForm" id="<?php print $vs_id_prefix; ?>addItemForm">
                <label for="<?php print $vs_id_prefix; ?>setItemAutocompleter"><?php print _t('Add %1', $vs_type_singular); ?></label>
                <input type="text" size="70" name="setItemAutocompleter" id="<?php print $vs_id_prefix; ?>setItemAutocompleter" class="lookupBg"/>
            </div>

            <ul id="<?php print $vs_id_prefix; ?>setItemList" class="setItemList">
            </ul>

            <input type="hidden" id="<?php print $vs_id_prefix; ?>setRowIDList" name="<?php print $vs_id_prefix; ?>setRowIDList" value=""/>
        </div>
    <?php endif; ?>
</div>

<?php if ($vn_table_num): ?>
    <script>
        var setEditorOps = null;

        (function ($) {
            'use strict';

            $(function() {
                setEditorOps = caUI.seteditor({
                    setID: <?php print (int)$vn_set_id; ?>,
                    table_num: <?php print (int)$t_set->get('table_num'); ?>,
                    fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
                    initialValues: <?php print json_encode($va_items); ?>,
                    initialValueOrder: <?php print json_encode(array_keys($va_items)); ?>,
                    setItemAutocompleteID: '<?php print $vs_id_prefix; ?>setItemAutocompleter',
                    rowIDListID: '<?php print $vs_id_prefix; ?>setRowIDList',
                    displayTemplate: <?php print (isset($va_settings['displayTemplate']) ? json_encode($va_settings['displayTemplate']) : 'null'); ?>,
                    editSetItemButton: '<span class="glyphicon glyphicon-edit"></span>',
                    deleteSetItemButton: '<span class="glyphicon glyphicon-remove"></span>',
                    lookupURL: '<?php print $va_lookup_urls['search']; ?>',
                    itemInfoURL: '<?php print caNavUrl($this->request, 'manage/sets', 'SetEditor', 'GetItemInfo'); ?>',
                    editSetItemsURL: '<?php print caNavUrl($this->request, 'manage/set_items', 'SetItemEditor', 'Edit', array('set_id' => $vn_set_id)); ?>',
                    editSetItemToolTip: '<?php print _t("Edit set item metadata"); ?>'
                });
            });
        }(jQuery));
    </script>
<?php endif; ?>
