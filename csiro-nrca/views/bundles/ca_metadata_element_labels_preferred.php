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
$vs_name_element_template = $t_label->htmlFormElement('name', null, array('name' => "{fieldNamePrefix}name_{n}", 'id' => "{fieldNamePrefix}name_{n}", "value" => "{{name}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only));
$vs_locale_element_template = $t_label->htmlFormElement('locale_id', "^LABEL ^ELEMENT", array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}locale_id_{n}", 'name' => "{fieldNamePrefix}locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)')));
$vs_description_element_template = $t_label->htmlFormElement('description', null, array('name' => "{fieldNamePrefix}description_{n}", 'id' => "{fieldNamePrefix}description_{n}", "value" => "{{description}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only));
$vn_locale_width = $vs_locale_element_template && !preg_match('/type=["\']hidden["\']/', $vs_locale_element_template) ? 3 : 0;
$vn_name_width = 12 - $vn_locale_width;
?>
<div id="<?php print $vs_id_prefix; ?>Labels" class="component component-bundle component-bundle-labels-preferred">
    <textarea class="label-template hidden" title="Contains the template for each value of the multi-value field.">
        <div id="{fieldNamePrefix}Label_{n}" class="repeating-item">
            <div class="elements-container removable">
                <div class="row">
                    <div class="col-md-<?php print $vn_name_width; ?>">
                        <?php print $vs_name_element_template; ?>
                    </div>
                    <?php if ($vn_locale_width > 0): ?>
                        <div class="col-md-<?php print $vn_locale_width; ?>">
                            <?php print $vs_locale_element_template; ?>
                        </div>
                    <?php else: ?>
                        <?php print $vs_locale_element_template; ?>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php print $vs_description_element_template; ?>
                    </div>
                </div>
            </div>
            <button type="button" class="remove" title="<?php print _t('Remove this label'); ?>">
                <?php print $this->getVar('remove_label'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
        </div>
    </textarea>

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
        templateValues: ['name', 'description', 'locale_id'],
        initialValues: <?php print json_encode($va_initial_values); ?>,
        forceNewValues: <?php print json_encode($va_force_new_labels); ?>,
        labelID: 'Label_',
        localeClassName: 'labelLocale',
        templateClassName: 'label-template',
        labelListClassName: 'label-list',
        addButtonClassName: 'add',
        deleteButtonClassName: 'remove',
        bundlePreview: <?php $va_cur = current($va_initial_values); print caEscapeForBundlePreview($va_cur['name']); ?>,
        readonly: <?php print $vb_read_only ? "1" : "0"; ?>,
        defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
    });
</script>
