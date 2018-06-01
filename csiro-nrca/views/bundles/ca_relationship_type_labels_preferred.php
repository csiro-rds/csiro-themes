<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_labels = $this->getVar('labels');
$t_label = $this->getVar('t_label');
$va_initial_values = $this->getVar('label_initial_values');
if (!$va_force_new_labels = $this->getVar('new_labels')) {
    $va_force_new_labels = array();
}
$va_settings = $this->getVar('settings');
$vs_add_label =	$this->getVar('add_label');
$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel('ca_relationship_types', 'preferred_labels') == __CA_BUNDLE_ACCESS_READONLY__));
?>
<div id="<?php print $vs_id_prefix; ?>Labels" class="component component-bundle component-bundle-relationship-type-labels-preferred">
	<textarea class='label-new-item-template hidden'>
		<div id="{fieldNamePrefix}Label_{n}" class="labelInfo repeating-item">
            <div class="elements-container removable">
                <div>
                    <?php print $t_label->htmlFormElement('typename', null, array('name' => "{fieldNamePrefix}typename_{n}", 'id' => "{fieldNamePrefix}typename_{n}", "value" => "{{typename}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                </div>
                <div>
                    <?php print $t_label->htmlFormElement('description', null, array('name' => "{fieldNamePrefix}description_{n}", 'id' => "{fieldNamePrefix}description_{n}", "value" => "{{description}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                </div>
                <div>
                    <?php print $t_label->htmlFormElement('typename_reverse', null, array('name' => "{fieldNamePrefix}typename_reverse_{n}", 'id' => "{fieldNamePrefix}typename_reverse{n}", "value" => "{{typename_reverse}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                </div>
                <div>
                    <?php print $t_label->htmlFormElement('description_reverse', null, array('name' => "{fieldNamePrefix}description_reverse_{n}", 'id' => "{fieldNamePrefix}description_reverse{n}", "value" => "{{description_reverse}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $vb_read_only)); ?>
                </div>
                <div>
                    <?php print $t_label->htmlFormElement('locale_id', '^LABEL ^ELEMENT', array('classname' => 'label-locale', 'id' => "{fieldNamePrefix}locale_id_{n}", 'name' => "{fieldNamePrefix}locale_id_{n}", "value" => "{locale_id}", 'no_tooltips' => true, 'dont_show_null_value' => true, 'hide_select_if_only_one_option' => true, 'WHERE' => array('(dont_use_for_cataloguing = 0)'))); ?>
                </div>
            </div>
            <button type="button" class="remove">
                <?php print _t('Remove') ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
		</div>
	</textarea>

	<div class="bundleContainer">
		<div class="label-item-list"></div>
        <button type="button" class="add top-right">
            <span class="glyphicon glyphicon-plus"></span>
            <?php print $vs_add_label ?: _t("Add label"); ?>
        </button>
	</div>
</div>
<script>
	caUI.initLabelBundle('#<?php print $vs_id_prefix; ?>Labels', {
		mode: 'preferred',
		fieldNamePrefix: '<?php print $vs_id_prefix; ?>',
		templateValues: ['typename', 'description', 'typename_reverse', 'description_reverse', 'locale_id'],
		initialValues: <?php print json_encode($va_initial_values); ?>,
		forceNewValues: <?php print json_encode($va_force_new_labels); ?>,
		labelID: 'Label_',
		localeClassName: 'label-locale',
		templateClassName: 'label-new-item-template',
		labelListClassName: 'label-item-list',
		addButtonClassName: 'label-add',
		deleteButtonClassName: 'remove',
		readonly: <?php print $vb_read_only ? "1" : "0"; ?>,
		bundlePreview: <?php $va_cur = current($va_initial_values); print caEscapeForBundlePreview($va_cur['typename']); ?>,
		defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
	});
</script>
