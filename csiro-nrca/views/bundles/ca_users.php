<?php
$vs_id_prefix= $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance	= $this->getVar('t_instance');
$t_item	= $this->getVar('t_user');
$t_rel = $this->getVar('t_rel');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vs_add_label = $this->getVar('add_label') ?: _t("Add user access");
$vb_read_only = $va_settings['readonly'] === true  || $this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') === __CA_BUNDLE_ACCESS_READONLY__;
$va_initial_values = $this->getVar('initialValues');
if (!is_array($va_initial_values)) {
    $va_initial_values = array();
}
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-users">
	<textarea class="user-new-item-template hidden">
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="repeating-item">
            <div class="elements-container">
                <?php print _t('User'); ?>
                <input type="text" name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="lookupBg"/>
                <?php print $t_rel->htmlFormElement('access', '^ELEMENT', array('name' => $vs_id_prefix.'_access_{n}', 'id' => $vs_id_prefix.'_access_{n}', 'no_tooltips' => true, 'value' => '{{access}}')); ?>
                <?php if ($t_rel->hasField('effective_date')): ?>
                    <?php print _t(' for period ').$t_rel->htmlFormElement('effective_date', '^ELEMENT', array('name' => $vs_id_prefix.'_effective_date_{n}', 'no_tooltips' => true, 'value' => '{{effective_date}}', 'classname'=> 'dateBg')); ?>
                <?php endif;?>
                <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
            </div>
            <?php if (!$vb_read_only): ?>
                <button type="button" class="remove">
                    <?php print _t('Remove'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            <?php endif; ?>
		</div>
	</textarea>

	<div class="bundleContainer">
		<div class="list-item"></div>
        <?php if (!$vb_read_only): ?>
            <div class='button add'>
                <button type="button" class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php print $vs_add_label; ?>
                </button>
            </div>
        <?php endif; ?>
	</div>
</div>

<script>
    (function($) {
        $(function () {
            caUI.initRelationBundle('#<?php print $vs_id_prefix; ?>', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: ['label', 'effective_date', 'access', 'id'],
                initialValues: <?php print json_encode($va_initial_values); ?>,
                initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                templateClassName: 'user-new-item-template',
                itemListClassName: 'list-item',
                addButtonClassName: 'add',
                deleteButtonClassName: 'remove',
                showEmptyFormsOnLoad: 0,
                readonly: <?php print $vb_read_only ? "true" : "false"; ?>,
                autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'User', 'Get', array()); ?>'
            });
        });
    })(jQuery);
</script>
