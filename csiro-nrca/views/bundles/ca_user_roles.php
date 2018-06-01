<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance = $this->getVar('t_instance');
$t_item = $this->getVar('t_role');
$t_rel = $this->getVar('t_rel');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vb_read_only =	((isset($va_settings['readonly']) && $va_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') == __CA_BUNDLE_ACCESS_READONLY__));
$va_initial_values = $this->getVar('initialValues');
if (!is_array($va_initial_values)) {
    $va_initial_values = array();
}
$va_role_list = $t_item->getRoleList();
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-user-roles">
	<textarea class="user-role-new-item-template hidden">
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
			<span class="formLabelError">{error}</span>
			<div class="objectRepresentationListItem">
                <?php if (sizeof($va_role_list) > 0): ?>
                    <div class="col-md-4">
                        <p>
                            <label><?php print "{$va_role_info['name']}" ?></label>
                        </p>
                        <?php print $t_rel->htmlFormElement('access', '^ELEMENT', array('name' => $vs_id_prefix."_access_{$vn_role_id}", 'id' => "{$vs_id_prefix}_access_{$vn_role_id}", 'no_tooltips' => true, 'value' => '{{access}}')); ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php print _t('No roles are available'); ?>
                        </div>
                    </div>
                <?php endif; ?>
			</div>
		</div>
	</textarea>

	<div class="bundleContainer">
		<div class="list-item"></div>
	</div>
</div>

<script>
	caUI.initrolelistbundle('#<?php print $vs_id_prefix; ?>', {
		fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
		templateValues: ['role_id'],
		initialValues: <?php print json_encode($va_initial_values); ?>,
		initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
		errors: <?php print json_encode($va_errors); ?>,
		itemID: '<?php print $vs_id_prefix; ?>Item_',
		templateClassName: 'user-role-new-item-template',
		itemListClassName: 'list-item',
		readonly: <?php print $vb_read_only ? "true" : "false"; ?>
	});
</script>
