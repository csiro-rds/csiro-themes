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
$vs_bundle_preview = (isset($va_settings['displayTemplate']) ? $t_subject->getWithTemplate($va_settings['displayTemplate']) : null) ?: current($va_initial_values)['name'];
$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($vs_table_name, 'preferred_labels') == __CA_BUNDLE_ACCESS_READONLY__));
$vs_label_list = $this->request->config->get($vs_table_name . '_preferred_label_type_list');
$vs_entity_class = $t_subject->getTypeSetting('entity_class');
?>
<div id="<?php print $vs_id_prefix; ?>Labels" class="component component-bundle component-bundle-labels-preferred">
    <textarea class="label-template hidden" title="Contains the template for each value of the multi-value field.">
        <div id="{fieldNamePrefix}Label_{n}" class="repeating-item">
            <div id="caDupeLabelMessageBox_{n}" class="alert alert-danger hidden"></div>
            <div class="elements-container removable">
                <?php if ($vs_entity_class === 'ORG'): ?>
                    <?php print $t_label->htmlFormElement('prefix', null, array('name' => "{fieldNamePrefix}_prefix_{n}", 'id' => "{fieldNamePrefix}_prefix_{n}", "value" => "{{suffix}}", 'hidden' => true)); ?>
                    <?php print $t_label->htmlFormElement('forename', null, array('name' => "{fieldNamePrefix}_forename_{n}", 'id' => "{fieldNamePrefix}_forename_{n}", "value" => "{{forename}}", 'hidden' => true)); ?>
                    <?php print $t_label->htmlFormElement('middlename', null, array('name' => "{fieldNamePrefix}_middlename_{n}", 'id' => "{fieldNamePrefix}_middlename_{n}", "value" => "{{middlename}}", 'hidden' => true)); ?>
                    <?php print $t_label->htmlFormElement('other_forenames', null, array('name' => "{fieldNamePrefix}_other_forenames-{n}", 'id' => "{fieldNamePrefix}_other_forenames_{n}", "value" => "{{other_forenames}}", 'hidden' => true)); ?>
                    <?php print $t_label->htmlFormElement('displayname', null, array('name' => "{fieldNamePrefix}_displayname_{n}", 'id' => "{fieldNamePrefix}_displayname_{n}", "value" => "{{displayname}}", 'hidden' => true)); ?>
                    <div class="row">
                        <div class="col-md-8">
                            <?php print $t_label->htmlFormElement('surname', null, array('label' => _t('Name'), 'description' => _t('The full name of the organization, excluding for suffixes.'), 'name' => "{fieldNamePrefix}_surname_{n}", 'id' => "{fieldNamePrefix}_surname_{n}", "value" => "{{surname}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php print $t_label->htmlFormElement('suffix', null, array('name' => "{fieldNamePrefix}_suffix_{n}", 'id' => "{fieldNamePrefix}_suffix_{n}", "value" => "{{suffix}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php print $t_label->htmlFormElement('locale_id', "^LABEL ^ELEMENT", array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}_locale_id_{n}", 'name' => "{fieldNamePrefix}_locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)'))); ?>
                            <?php print ($vs_label_list ? $t_label->htmlFormElement('type_id', "^LABEL ^ELEMENT", array('classname' => 'labelType', 'id' => "{fieldNamePrefix}_type_id_{n}", 'name' => "{fieldNamePrefix}_type_id_{n}", "value" => "{type_id}", 'no_tooltips' => true, 'list_code' => $vs_label_list, 'dont_show_null_value' => true, 'hide_select_if_no_options' => true)) : ''); ?>
                        </div>
                    </div>
                <?php elseif ($vs_entity_class === 'IND_SM'): ?>
                    <?php print $t_label->htmlFormElement('other_forenames', null, array('name' => "{fieldNamePrefix}_other_forenames-{n}", 'id' => "{fieldNamePrefix}_other_forenames_{n}", "value" => "{{other_forenames}}", 'hidden' => true)); ?>
                    <div class="row">
                        <div class="col-md-1">
                            <?php print $t_label->htmlFormElement('prefix', null, array('name' => "{fieldNamePrefix}_prefix_{n}", 'id' => "{fieldNamePrefix}_prefix_{n}", "value" => "{{prefix}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('forename', null, array('name' => "{fieldNamePrefix}_forename_{n}", 'id' => "{fieldNamePrefix}_forename_{n}", "value" => "{{forename}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('middlename', null, array('name' => "{fieldNamePrefix}_middlename_{n}", 'id' => "{fieldNamePrefix}_middlename_{n}", "value" => "{{middlename}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php print $t_label->htmlFormElement('surname', null, array('name' => "{fieldNamePrefix}_surname_{n}", 'id' => "{fieldNamePrefix}_surname_{n}", "value" => "{{surname}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-1">
                            <?php print $t_label->htmlFormElement('suffix', null, array('name' => "{fieldNamePrefix}_suffix_{n}", 'id' => "{fieldNamePrefix}_suffix_{n}", "value" => "{{suffix}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('locale_id', "^LABEL<br/>^ELEMENT", array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}_locale_id_{n}", 'name' => "{fieldNamePrefix}_locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)'))); ?>
                            <?php print ($vs_label_list ? $t_label->htmlFormElement('type_id', "^LABEL ^ELEMENT", array('classname' => 'labelType', 'id' => "{fieldNamePrefix}_type_id_{n}", 'name' => "{fieldNamePrefix}_type_id_{n}", "value" => "{type_id}", 'no_tooltips' => true, 'list_code' => $vs_label_list, 'dont_show_null_value' => true, 'hide_select_if_no_options' => true)) : ''); ?>
                        </div>
                        <div class="col-md-6 col-md-offset-3">
                            <?php print $t_label->htmlFormElement('displayname', null, array('name' => "{fieldNamePrefix}_displayname_{n}", 'id' => "{fieldNamePrefix}_displayname_{n}", "value" => "{{displayname}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred', 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-1">
                            <?php print $t_label->htmlFormElement('prefix', null, array('name' => "{fieldNamePrefix}_prefix_{n}", 'id' => "{fieldNamePrefix}_prefix_{n}", "value" => "{{prefix}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('forename', null, array('name' => "{fieldNamePrefix}_forename_{n}", 'id' => "{fieldNamePrefix}_forename_{n}", "value" => "{{forename}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('middlename', null, array('name' => "{fieldNamePrefix}_middlename_{n}", 'id' => "{fieldNamePrefix}_middlename_{n}", "value" => "{{middlename}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php print $t_label->htmlFormElement('surname', null, array('name' => "{fieldNamePrefix}_surname_{n}", 'id' => "{fieldNamePrefix}_surname_{n}", "value" => "{{surname}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-1">
                            <?php print $t_label->htmlFormElement('suffix', null, array('name' => "{fieldNamePrefix}_suffix_{n}", 'id' => "{fieldNamePrefix}_suffix_{n}", "value" => "{{suffix}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('locale_id', "^LABEL<br/>^ELEMENT", array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}_locale_id_{n}", 'name' => "{fieldNamePrefix}_locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)'))); ?>
                            <?php print ($vs_label_list ? $t_label->htmlFormElement('type_id', "^LABEL ^ELEMENT", array('classname' => 'labelType', 'id' => "{fieldNamePrefix}_type_id_{n}", 'name' => "{fieldNamePrefix}_type_id_{n}", "value" => "{type_id}", 'no_tooltips' => true, 'list_code' => $vs_label_list, 'dont_show_null_value' => true, 'hide_select_if_no_options' => true)) : ''); ?>
                        </div>
                        <div class="col-md-3">
                            <?php print $t_label->htmlFormElement('other_forenames', null, array('name' => "{fieldNamePrefix}_other_forenames_{n}", 'id' => "{fieldNamePrefix}_other_forenames_{n}", "value" => "{{other_forenames}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred')); ?>
                        </div>
                        <div class="col-md-6">
                            <?php print $t_label->htmlFormElement('displayname', null, array('name' => "{fieldNamePrefix}_displayname_{n}", 'id' => "{fieldNamePrefix}_displayname_{n}", "value" => "{{displayname}}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_entity_labels_preferred', 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" class="remove" title="<?php print _t('Remove this label'); ?>">
                <?php print $this->getVar('remove_label'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
        </div>
        <?php print TooltipManager::getLoadHTML('bundle_ca_entity_labels_preferred'); ?>
    </textarea>

    <?php if ($vb_batch): ?>
        <?php print caBatchEditorPreferredLabelsModeControl($t_label, $vs_id_prefix); ?>
    <?php endif; ?>
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.'Labels', $va_settings); ?>

    <div class="bundleContainer">
        <div class="label-list"></div>
        <button type="button" class="add top-right" title="<?php print _t('Add another label'); ?>">
            <?php print $this->getVar('add_label') ?: _t('Add label'); ?>
            <span class="glyphicon glyphicon-plus"></span>
        </button>
    </div>
</div>

<script>
    caUI.initLabelBundle('#<?php print $vs_id_prefix; ?>Labels', {
        mode: 'preferred',
        fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
        templateValues: ['displayname', 'prefix', 'forename', 'other_forenames', 'middlename', 'surname', 'suffix', 'locale_id', 'type_id'],
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
        defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>,
        checkForDupes: <?php print json_encode($t_label->getAppConfig()->get('ca_entities_warn_when_preferred_label_exists')) ?>,
        checkForDupesUrl: '<?php print caNavUrl($this->request, 'editor/entities', 'EntityEditor', 'checkForDupeLabels')?>',
        dupeLabelWarning: '<?php print _t('Label is already in use'); ?>'
    });
</script>
