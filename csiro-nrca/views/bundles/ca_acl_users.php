<?php
$t_acl = new ca_acl();
$vs_id_prefix = $this->getVar('id_prefix') . '_user';
$vn_table_num = $this->getVar('t_user')->tableNum();
$va_initial_values = $this->getVar('initialValues') ?: array();
$vs_element_template = $t_acl->htmlFormElement('access', '^ELEMENT', array('name' => $vs_id_prefix.'_access_{n}', 'id' => $vs_id_prefix.'_access_{n}', 'value' => '{{access}}', 'no_tooltips' => true));
?>
<div id="<?php print $vs_id_prefix . $vn_table_num; ?>_rel" class="component component-bundle component-bundle-acl-users">
    <textarea class="item-template hidden">
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <input name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="form-control" />
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <?php print $vs_element_template; ?>
                    <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}" />
                </div>
            </div>
            <div class="col-md-2 text-right">
                <div class="form-group">
                    <button type="button" class="remove-button btn btn-warning" title="<?php print _t('Remove user access'); ?>">
                        <?php print $this->getVar('remove_label') ?: _t('Remove user'); ?>
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                </div>
            </div>
        </div>
    </textarea>

    <div class="bundleContainer">
        <div class="row">
            <div class="col-md-5">
                <label><?php print _t('User'); ?></label>
            </div>
            <div class="col-md-5">
                <label><?php print _t('Access'); ?></label>
            </div>
        </div>
        <div class="item-list"></div>
        <div class="text-right">
            <button type="button" class="add-button btn btn-default" title="<?php print _t('Add user access'); ?>">
                <?php print $this->getVar('add_label') ?: _t('Add user'); ?>
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        </div>
    </div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            caUI.initRelationBundle('#<?php print $vs_id_prefix . $vn_table_num; ?>_rel', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: ['label', 'effective_date', 'access', 'id'],
                initialValues: <?php print json_encode($va_initial_values); ?>,
                initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                templateClassName: 'item-template',
                itemListClassName: 'item-list',
                addButtonClassName: 'add-button',
                deleteButtonClassName: 'remove-button',
                showEmptyFormsOnLoad: 0,
                readonly: false,
                autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'User', 'Get', array()); ?>'
            });
        });
    }(jQuery));
</script>