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
$vs_bundle_preview = (isset($va_settings['displayTemplate']) ? $t_subject->getWithTemplate($va_settings['displayTemplate']) : null) ?: current($va_initial_values)['name_plural'];
$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($vs_table_name, 'nonpreferred_labels') == __CA_BUNDLE_ACCESS_READONLY__));
$vs_label_list = $this->request->config->get($vs_table_name . '_nonpreferred_label_type_list');
$vs_name_singular_element_template = $t_label->htmlFormElement('name_singular', null, array('name' => "{fieldNamePrefix}name_singular_{n}", 'id' => "{fieldNamePrefix}name_singular_{n}", "value" => "{{name_singular}}", 'no_tooltips' => false, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only, 'tooltip_namespace' => 'bundle_ca_list_item_labels_preferred'));
$vs_name_plural_element_template = $t_label->htmlFormElement('name_plural', null, array('name' => "{fieldNamePrefix}name_plural_{n}", 'id' => "{fieldNamePrefix}name_plural_{n}", "value" => "{{name_plural}}", 'no_tooltips' => false, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only, 'tooltip_namespace' => 'bundle_ca_list_item_labels_preferred'));
$vs_locale_element_template = $t_label->htmlFormElement('locale_id', '^LABEL ^ELEMENT', array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}locale_id_{n}", 'name' => "{fieldNamePrefix}locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => false, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)'), 'tooltip_namespace' => 'bundle_ca_list_item_labels_preferred'));
$vs_type_element_template = $vs_label_list ? $t_label->htmlFormElement('type_id', "^LABEL ^ELEMENT", array('classname' => 'labelType', 'id' => "{fieldNamePrefix}type_id_{n}", 'name' => "{fieldNamePrefix}type_id_{n}", "value" => "{type_id}", 'no_tooltips' => true, 'list_code' => $vs_label_list, 'dont_show_null_value' => true, 'hide_select_if_no_options' => true)) : '';
?>
<div id="<?php print $vs_id_prefix; ?>NPLabels">
    <textarea class="label-template hidden">
        <div id="{fieldNamePrefix}Label_{n}" class="repeating-item">
            <div class="elements-container removable">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $vs_name_singular_element_template; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $vs_name_plural_element_template; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $vs_locale_element_template; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $vs_type_element_template; ?>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="remove" title="<?php print _t('Remove this label'); ?>">
                <?php print $this->getVar('remove_label'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
        </div>
        <?php print TooltipManager::getLoadHTML('bundle_ca_list_item_labels_preferred'); ?>
    </textarea>

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
    caUI.initLabelBundle('#<?php print $vs_id_prefix; ?>NPLabels', {
        mode: 'nonpreferred',
        fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
        templateValues: ['name_singular', 'name_plural', 'description', 'locale_id', 'type_id'],
        initialValues: <?php print json_encode($va_initial_values); ?>,
        forceNewValues: <?php print json_encode($va_force_new_labels); ?>,
        labelID: 'Label_',
        localeClassName: 'labelLocale',
        templateClassName: 'label-template',
        labelListClassName: 'label-list',
        addButtonClassName: 'add',
        deleteButtonClassName: 'remove',
        bundlePreview: <?php print caEscapeForBundlePreview($vs_bundle_preview); ?>,
        readonly: <?php print json_encode($vb_read_only); ?>,
        defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
    });
</script>
