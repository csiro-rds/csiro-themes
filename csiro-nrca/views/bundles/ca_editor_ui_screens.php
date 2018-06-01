<?php
AssetLoadManager::register('sortableUI');

$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_initial_values = $this->getVar('screens');
?>
<div id="<?php print $vs_id_prefix; ?>">
    <textarea class="item-template hidden">
        <tr id="<?php print $vs_id_prefix; ?>Item_{n}" class="repeating-item">
            <td>
                <div id="{fieldNamePrefix}_edit_name_{n}" class="form-group hidden">
                    <?php print caHTMLTextInput('{fieldNamePrefix}_name_{n}', array('id' => '{fieldNamePrefix}_name_{n}', 'value' => '{name}'), array('class' => 'form-control')); ?>
                </div>
                <span id="{fieldNamePrefix}_screen_name_{n}">{name}</span>
            </td>
            <td>
                {numPlacements}
            </td>
            <td>
                {isDefault}
            </td>
            <td>
                <span id="{fieldNamePrefix}_screen_info_{n}">
                    {typeRestrictionsForDisplay}
                </span>
            </td>
            <td class="text-right">
                <a href="<?php print urldecode(caNavUrl($this->request, 'administrate/setup/interface_screen_editor', 'InterfaceScreenEditor', 'Edit', array('screen_id' => '{screen_id}'))); ?>" id="{fieldNamePrefix}_edit_{n}" title="<?php print _t('Edit this item'); ?>">
                    <?php print $this->getVar('edit_label'); ?>
                    <span class="glyphicon glyphicon-cog"></span>
                </a>
                <button type="button" class="remove" title="<?php print _t('Remove this item'); ?>">
                    <?php print $this->getVar('remove_label'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            </td>
        </tr>
        <?php print TooltipManager::getLoadHTML('bundle_ca_editor_ui_screens'); ?>
    </textarea>

    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, array()); ?>

    <div class="bundleContainer">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Screen</th>
                <th># Placements</th>
                <th>Is Default</th>
                <th>Type Restrictions</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="item-list">
            </tbody>
        </table>
        <button type="button" class="add top-right" title="<?php print _t('Add a screen'); ?>">
            <?php print $this->getVar('add_label') ?: _t('Add screen'); ?>
            <span class="glyphicon glyphicon-plus"></span>
        </button>
    </div>

    <input type="hidden" id="<?php print $vs_id_prefix; ?>_ScreenBundleList" name="<?php print $vs_id_prefix; ?>_ScreenBundleList" value="" />
</div>

<script>
    caUI.initBundle('#<?php print $vs_id_prefix; ?>', {
        fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
        templateValues: ['name', 'locale_id', 'rank', 'screen_id', 'numPlacements', 'typeRestrictionsForDisplay', 'isDefault'],
        initialValues: <?php print json_encode($va_initial_values); ?>,
        initialValueOrder: <?php print json_encode(is_array($va_initial_values) ? array_keys($va_initial_values) : null); ?>,
        itemID: '<?php print $vs_id_prefix; ?>Item_',
        templateClassName: 'item-template',
        initialValueTemplateClassName: 'item-template',
        itemListClassName: 'item-list',
        itemClassName: 'repeating-item',
        addButtonClassName: 'add',
        deleteButtonClassName: 'remove',
        showOnNewIDList: ['<?php print $vs_id_prefix; ?>_edit_name_'],
        hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_screen_info_', '<?php print $vs_id_prefix; ?>_screen_name_', '<?php print $vs_id_prefix; ?>_edit_'],
        showEmptyFormsOnLoad: 1,
        isSortable: true,
        listSortOrderID: '<?php print $vs_id_prefix; ?>_ScreenBundleList',
        defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
    });
</script>
