<?php
$vs_id_prefix = $this->getVar('placement_code') . $this->getVar('id_prefix');
$va_labels = $this->getVar('labels');
$t_label = $this->getVar('t_label');
$t_subject = $this->getVar('t_subject');
$va_initial_values = $this->getVar('label_initial_values');
$va_settings = $this->getVar('settings');
$va_force_new_labels = $this->getVar('new_labels') ?: array();
$vb_batch = $this->getVar('batch');

$vs_table_name = $t_subject->tableName();
$vs_bundle_preview_name = $this->getVar('bundlePreviewName') ?: 'name';
$vs_bundle_preview = (isset($va_settings['displayTemplate']) ? $t_subject->getWithTemplate($va_settings['displayTemplate']) : null) ?: current($va_initial_values)[$vs_bundle_preview_name];
$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($vs_table_name, 'nonpreferred_labels') == __CA_BUNDLE_ACCESS_READONLY__));
$vs_label_list = $this->request->config->get($vs_table_name . '_nonpreferred_label_type_list');
$vs_name_element_template = $t_label->htmlFormElement('name', "^ELEMENT", array_merge($va_settings, array('request' => $this->request, 'name' => "{fieldNamePrefix}name_{n}", 'id' => "{fieldNamePrefix}name_{n}", "value" => "{{name}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)));
$vs_locale_element_template = $t_label->htmlFormElement('locale_id', "^ELEMENT", array('classname' => 'locale', 'id' => "{fieldNamePrefix}locale_id_{n}", 'name' => "{fieldNamePrefix}locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)')));
$vs_type_element_template = $vs_label_list ? $t_label->htmlFormElement('type_id', "^ELEMENT", array('classname' => 'labelType', 'id' => "{fieldNamePrefix}type_id_{n}", 'name' => "{fieldNamePrefix}type_id_{n}", "value" => "{type_id}", 'no_tooltips' => true, 'list_code' => $vs_label_list, 'dont_show_null_value' => true, 'hide_select_if_no_options' => true)) : null;
$vn_locale_width = $vs_locale_element_template && !preg_match('/type=["\']hidden["\']/', $vs_locale_element_template) ? 3 : 0;
$vn_type_width = $vs_type_element_template && !preg_match('/type=["\']hidden["\']/', $vs_type_element_template) ? 3 : 0;
$vn_name_width = 12 - $vn_locale_width - $vn_type_width;
?>
<?php print TooltipManager::getLoadHTML('bundle_ca_list_item_labels_preferred'); ?>
<div id="<?php print $vs_id_prefix; ?>NPLabels" class="component component-bundle component-bundle-labels-nonpreferred">
    <textarea class="label-template hidden" title="Contains the template for each value of the multi-value field.">
        <div id="{fieldNamePrefix}Label_{n}" class="repeating-item">
            <div class="elements-container removable">
                <div class="row">
                    <div class="col-md-<?php print $vn_name_width; ?>">
                        <div class="form-group">
                            <?php print $vs_name_element_template; ?>
                        </div>
                    </div>

                    <?php if ($vn_locale_width > 0): ?>
                        <div class="col-md-<?php print $vn_locale_width; ?>">
                            <div class="form-group">
                                <?php print $vs_locale_element_template; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php print $vs_locale_element_template; ?>
                    <?php endif; ?>

                    <?php if ($vn_type_width > 0): ?>
                        <div class="col-md-<?php print $vn_type_width; ?>">
                            <div class="form-group">
                                <?php print $vs_type_element_template; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php print $vs_type_element_template; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!$vb_read_only): ?>
                <button type="button" class="remove" title="<?php print _t('Remove this label'); ?>">
                    <?php print $this->getVar('remove_label'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            <?php endif; ?>
        </div>
    </textarea>

    <?php if ($vb_batch): ?>
        <?php print caBatchEditorNonPreferredLabelsModeControl($t_label, $vs_id_prefix); ?>
    <?php endif; ?>
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.'NPLabels', $va_settings); ?>
    <div class="bundleContainer">
        <div class="label-list"></div>
        <button type="button" class="add top-right" title="<?php print _t('Add another label'); ?>">
            <?php print $this->getVar('add_label') ?: _t('Add label'); ?>
            <span class="glyphicon glyphicon-plus"></span>
        </button>
    </div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            caUI.initLabelBundle('#<?php print $vs_id_prefix; ?>NPLabels', {
                mode: 'nonpreferred',
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
                templateValues: [ 'name', 'locale_id', 'type_id' ],
                forceNewValues: <?php print json_encode($va_force_new_labels); ?>,
                initialValues: <?php print json_encode($va_initial_values); ?>,
                labelID: 'Label_',
                localeClassName: 'locale',
                templateClassName: 'label-template',
                labelListClassName: 'label-list',
                addButtonClassName: 'add',
                deleteButtonClassName: 'remove',
                bundlePreview: <?php print caEscapeForBundlePreview($vs_bundle_preview); ?>,
                readonly: <?php print json_encode($vb_read_only); ?>,
                defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
            });
        });
    }(jQuery));
</script>
