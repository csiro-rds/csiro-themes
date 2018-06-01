<?php
global $g_ui_locale;

$vs_id_prefix = $this->getVar('placement_code') . $this->getVar('id_prefix');
$vs_error_source_code = $this->getVar('error_source_code');
$vs_render_mode = $this->getVar('render_mode');
$t_instance = $this->getVar('t_instance');
$t_element = $this->getVar('t_element');
$va_elements = $this->getVar('elements');
$va_element_ids = $this->getVar('element_ids');
$va_element_info = $this->getVar('element_info');
$va_root_element = current($va_element_info);
$vs_element_set_label = $this->getVar('element_set_label');
$va_attribute_list = $this->getVar('attribute_list');
$va_element_value_defaults = $this->getVar('element_value_defaults');
$va_failed_inserts = $this->getVar('failed_insert_attribute_list');
$va_failed_updates = $this->getVar('failed_update_attribute_list');
$va_settings = $this->getVar('settings');
$vb_batch = $this->getVar('batch');

$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($this->getVar('t_instance')->tableName(), $this->getVar('element_code')) == __CA_BUNDLE_ACCESS_READONLY__));
$va_element_settings = $t_element->getSettings();
$vb_is_read_only_for_existing_vals = false;

$va_data_types_no_value_return = array(
    __CA_ATTRIBUTE_VALUE_LCSH__,
    __CA_ATTRIBUTE_VALUE_PLACE__,
    __CA_ATTRIBUTE_VALUE_OCCURRENCE__,
    __CA_ATTRIBUTE_VALUE_TAXONOMY__,
    __CA_ATTRIBUTE_VALUE_INFORMATIONSERVICE__,
    __CA_ATTRIBUTE_VALUE_OBJECTREPRESENTATIONS__,
    __CA_ATTRIBUTE_VALUE_ENTITIES__
);

$va_readonly_previews = array();
if (($t_element->get('datatype') == __CA_ATTRIBUTE_VALUE_CONTAINER__) && isset($va_element_settings['readonlyTemplate']) && (strlen($va_element_settings['readonlyTemplate']) > 0)) {
    $vb_is_read_only_for_existing_vals = true;
    $va_display_vals = array_shift($t_instance->getAttributeDisplayValues($va_root_element['element_id'], $t_instance->getPrimaryKey()));
    if (is_array($va_display_vals)) {
        $vn_i = 0;
        foreach ($va_display_vals as $vn_attr_id => $va_display_val) {
            $vs_template = "<unit relativeTo='{$t_instance->tableName()}.{$t_element->get('element_code')}' start='{$vn_i}' length='1'>{$va_element_settings['readonlyTemplate']}</unit>";
            $va_readonly_previews[$vn_attr_id] = caProcessTemplateForIDs($vs_template, $t_instance->tableName(), array($t_instance->getPrimaryKey()));
            $vn_i++;
        }
    }
}

// generate list of initial form values; the bundle Javascript call will use the template to generate the initial form.
$va_initial_values = array();
$va_errors = array();
$vs_bundle_preview = '';

$va_template_tags = $va_element_ids;
$vs_display_template = trim(caGetOption('displayTemplate', $va_settings)) ?: caGetOption('displayTemplate', $va_element_settings, null);

$va_element_settings = $t_element->getSettings();
if ($t_instance->getAppConfig()->get('always_show_bundle_preview_for_attributes') || $vs_display_template) {
    $vs_bundle_preview = $vs_display_template ? $t_instance->getWithTemplate($vs_display_template) : $t_instance->getAttributesForDisplay($va_root_element['element_id'], null, array('showHierarchy' => true));
}

if (sizeof($va_attribute_list)) {
    $va_item_ids = array();
    foreach ($va_attribute_list as $o_attr) {
        $va_initial_values[$o_attr->getAttributeID()] = array();
        foreach ($o_attr->getValues() as $o_value) {
            $vn_attr_id = $o_attr->getAttributeID();
            $vn_element_id = $o_value->getElementID();

            $vs_display_val = ($va_failed_updates[$vn_attr_id] && !in_array($o_value->getDatatype(), $va_data_types_no_value_return)) ?
                $va_failed_updates[$vn_attr_id][$vn_element_id] :
                $o_value->getDisplayValue(array('request' => $this->request, 'includeID' => true));

            $va_initial_values[$vn_attr_id][$vn_element_id] = $vs_display_val;

            // autocompleter-based mode for list attributes
            if (isset($va_element_info[$vn_element_id]) && isset($va_element_info[$vn_element_id]['settings']['render']) && ($va_element_info[$vn_element_id]['settings']['render'] == 'lookup')) {
                $vs_tag = $vn_element_id . '_label';
                $va_template_tags[] = $vs_tag;
                $va_initial_values[$vn_attr_id][$vs_tag] = '';
                $va_item_ids[] = (int)$vs_display_val;
            }
        }
        $va_initial_values[$o_attr->getAttributeID()]['locale_id'] = $o_attr->getLocaleID();

        // set errors for attribute
        $va_action_errors = $this->request->getActionErrors($vs_error_source_code, $o_attr->getAttributeID());
        if (is_array($va_action_errors)) {
            foreach ($va_action_errors as $o_error) {
                $va_errors[$o_attr->getAttributeID()][] = array(
                    'errorDescription' => $o_error->getErrorDescription(),
                    'errorCode' => $o_error->getErrorNumber()
                );
            }
        }
    }

    if (sizeof($va_item_ids)) {
        $t_list_item = new ca_list_items();
        $va_labels = $t_list_item->getPreferredDisplayLabelsForIDs($va_item_ids);
        foreach ($va_initial_values as $vn_attr_id => $va_values) {
            foreach ($va_values as $vn_element_id => $vs_value) {
                $va_initial_values[$vn_attr_id][$vn_element_id . '_label'] = $va_labels[$va_initial_values[$vn_attr_id][$vn_element_id]];
            }
        }
    }
} else {
    // set labels for replacement in blank lookups
    if (is_array($va_element_ids)) {
        foreach ($va_element_ids as $vn_element_id) {
            $va_template_tags[] = $vn_element_id . '_label';
        }
    }
}

$vs_add_label = $va_settings['add_label'][$g_ui_locale] ?: _t("Add %1", $vs_element_set_label);
$va_template_list = caGetAvailablePrintTemplates('bundles', array('table' => $t_instance->tableName(), 'elementCode' => $t_element->get('element_code'), 'forHTMLSelect' => true));
$vs_presets = $vb_batch ? null : $t_element->getPresetsAsHTMLFormElement(array('width' => '100px')); // TODO not hardcoded width here
$vb_show_add_remove = ($vs_render_mode !== 'checklist') && !$vb_read_only;
$vs_print_base_url = caNavUrl($this->request, '*', '*', 'PrintBundle', array('element_code' => $t_element->get('element_code'), $t_instance->primaryKey() => $t_instance->getPrimaryKey()));
$vb_can_make_pdf = caGetOption('canMakePDFForValue', $va_element_info[$t_element->getPrimaryKey()]['settings'], false);

$vn_add_remove_width = $vb_show_add_remove ? 1 : 0;
$vn_field_width = 12 - $vn_add_remove_width;
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-attributes">
    <?php if ($vb_batch): ?>
        <?php print caBatchEditorAttributeModeControl($vs_id_prefix); ?>
    <?php endif; ?>

    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $va_settings); ?>

    <?php if (caGetOption('canMakePDF', $va_element_info[$t_element->getPrimaryKey()]['settings'], false) && sizeof($va_template_list) > 0): ?>
        <div>
            <?php if (sizeof($va_template_list) > 1): ?>
                <?php print caHTMLSelect('template', $va_template_list, array('class' => 'dontTriggerUnsavedChangeWarning', 'id' => "{$vs_id_prefix}PrintTemplate")); ?>
            <?php else: ?>
                <?php print caHTMLHiddenInput('template', array('value' => array_pop($va_template_list), 'id' => "{$vs_id_prefix}PrintTemplate")); ?>
            <?php endif; ?>
            <button type="button" class="print" onclick="<?php print $vs_id_prefix; ?>Print(); return false;">
                <span class="glyphicon glyphicon-print"></span>
            </button>
        </div>
    <?php endif; ?>

    <textarea class="item-template hidden">
        <div id="{fieldNamePrefix}Item_{n}" class="repeating-item">
            <?php /* TODO Is this `hidden` ever overridden? */ ?>
            <div class="alert-danger hidden">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                {error}
            </div>
            <?php if ($vs_presets || isset($va_elements['_locale_id']) || ($vb_can_make_pdf && sizeof($va_template_list) > 0)): ?>
                <div>
                    <?php print $vs_presets; ?>
                    <?php if (isset($va_elements['_locale_id'])): ?>
                        <?php if ($va_elements['_locale_id']['hidden']): ?>
                            <?php print $va_elements['_locale_id']['element']; ?>
                        <?php else: ?>
                            <div class="formLabel">
                                <label><?php print _t('Locale'); ?></label>
                                <?php $va_elements['_locale_id']['element']; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($vb_can_make_pdf && sizeof($va_template_list) > 0): ?>
                        <div class="print" id="<?php print $vs_id_prefix; ?>_print_control_{n}">
                            <?php if (sizeof($va_template_list) > 1): ?>
                                <?php print caHTMLSelect('template', $va_template_list, array('class' => 'dontTriggerUnsavedChangeWarning', 'id' => "{$vs_id_prefix}PrintTemplate{n}")); ?>
                            <?php else: ?>
                                <?php print caHTMLHiddenInput('template', array('value' => array_pop($va_template_list), 'id' => "{$vs_id_prefix}PrintTemplate{n}")); ?>
                            <?php endif; ?>
                            <button type="button" onclick="{$vs_id_prefix}Print({n}); return false;" class="print">
                                <span class="glyphicon glyphicon-print"></span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php foreach ($va_elements as $vn_container_id => $va_element_list): ?>
                <?php if ($vn_container_id !== '_locale_id'): ?>
                    <div class="elements-container <?php print ($vb_show_add_remove ? 'removable' : ''); ?>">
                        <?php foreach ($va_element_list as $vs_element): ?>
                            <div class="form-group">
                                <?php // any <textarea> tags in the template needs to be renamed to 'textentry' for the template to work ?>
                                <?php print str_replace('textarea', 'textentry', $vs_element); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($vb_show_add_remove): ?>
                        <button type="button" class="remove" title="<?php print _t('Remove this item'); ?>">
                            <?php print $this->getVar('remove_label'); ?>
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </textarea>

    <div class="bundleContainer">
        <div class="item-list">
            <?php if ($vb_is_read_only_for_existing_vals): ?>
                <div class="hidden">
                    <?php foreach ($va_readonly_previews as $vn_attr_id => $vs_readonly_preview): ?>
                        <div id="caReadonlyContainer<?php print $vs_id_prefix; ?>_<?php print $vn_attr_id; ?>" data-id-prefix="<?php print $vs_id_prefix; ?>">
                            <button type="button" class="readonly-edit">
                                <span class="glyphicon glyphicon-edit"></span>
                                <?php print _t('Edit'); ?>
                            </button>
                            <div class="readonly-preview">
                                <?php print $vs_readonly_preview; ?>
                            </div>
                            <?php print caHTMLHiddenInput($vs_id_prefix.'_dont_save_'.$vn_attr_id, array('class' => 'dont-save', 'value' => 1)); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($vb_show_add_remove): ?>
            <button type="button" class="add top-right" title="<?php print _t('Add another item'); ?>">
                <?php print $this->getVar('add_label') ?: _t('Add item'); ?>
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function () {
            $('.readonly-edit').click(function () {
                var $container = $(this).parent();
                $('#<?php print $vs_id_prefix; ?>Item_' + $container.data('id-prefix')).show();
                $container.hide();
                $container.children('input[type=hidden].dont-save').val('0');
            });

            <?php if (!$vb_batch): ?>
                <?php print $t_element->getPresetsJavascript($vs_id_prefix); ?>
            <?php endif; ?>

            <?php if ($vs_render_mode === 'checklist'): ?>
                caUI.initChecklistBundle('#<?php print $vs_id_prefix; ?>', {
                    fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                    templateValues: <?php print json_encode($va_template_tags); ?>,
                    initialValues: <?php print json_encode($va_initial_values); ?>,
                    initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                    errors: <?php print json_encode($va_errors); ?>,
                    itemID: '<?php print $vs_id_prefix; ?>Item_',
                    templateClassName: 'item-template',
                    initialValueTemplateClassName: 'item-template',
                    itemListClassName: 'item-list',
                    minRepeats: <?php print ($this->getVar('min_num_repeats') ?: 0); ?>,
                    maxRepeats: <?php print ($this->getVar('max_num_repeats') ?: 65535); ?>,
                    defaultValues: <?php print json_encode($va_element_value_defaults); ?>,
                    bundlePreview: <?php print caEscapeForBundlePreview($vs_bundle_preview); ?>,
                    readonly: <?php print json_encode($vb_read_only); ?>,
                    defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
                });
            <?php else: ?>
                caUI.initBundle('#<?php print $vs_id_prefix; ?>', {
                    fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                    templateValues: <?php print json_encode($va_template_tags); ?>,
                    initialValues: <?php print json_encode($va_initial_values); ?>,
                    initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                    forceNewValues: <?php print json_encode($va_failed_inserts); ?>,
                    errors: <?php print json_encode($va_errors); ?>,
                    itemID: '<?php print $vs_id_prefix; ?>Item_',
                    templateClassName: 'item-template',
                    initialValueTemplateClassName: 'item-template',
                    itemListClassName: 'item-list',
                    addButtonClassName: 'add',
                    deleteButtonClassName: 'remove',
                    minRepeats: <?php print ($this->getVar('min_num_repeats') ?: 0); ?>,
                    maxRepeats: <?php print ($this->getVar('max_num_repeats') ?: 65535); ?>,
                    showEmptyFormsOnLoad: <?php print intval($this->getVar('min_num_to_display')); ?>,
                    hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_download_control_', '<?php print $vs_id_prefix; ?>_print_control_'],
                    showOnNewIDList: ['<?php print $vs_id_prefix; ?>_upload_control_'],
                    defaultValues: <?php print json_encode($va_element_value_defaults); ?>,
                    bundlePreview: <?php print caEscapeForBundlePreview($vs_bundle_preview); ?>,
                    readonly: <?php print json_encode($vb_read_only); ?>,
                    defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>,
                    onInitializeItem: function (attribute_id, values, element, isNew) {
                        if (isNew) {
                            return false;
                        }
                        <?php if ($vb_is_read_only_for_existing_vals): ?>
                            var bundleFormElement = $("#" + element.container.replace('#', '') + 'Item_' + attribute_id);
                            bundleFormElement.hide();
                            bundleFormElement.after($('#caReadonlyContainer<?php print $vs_id_prefix?>_' + attribute_id));
                        <?php endif; ?>
                    },
                    listItemClassName: 'repeating-item',
                    oddColor: '<?php print caGetOption('colorOddItem', $va_settings, 'FFFFFF'); ?>',
                    evenColor: '<?php print caGetOption('colorEvenItem', $va_settings, 'FFFFFF'); ?>'
                });
            <?php endif; ?>

            function <?php print $vs_id_prefix; ?>Print (attribute_id) {
                attribute_id = attribute_id || '';
                var template = $('#<?php print $vs_id_prefix; ?>PrintTemplate' + attribute_id).val();
                window.location = '<?php print $vs_print_base_url; ?>/template/' + template + '/attribute_id/' + attribute_id;
            }
        });
    }(jQuery));
</script>
