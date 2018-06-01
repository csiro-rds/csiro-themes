<?php
$t_subject = $this->getVar('t_subject');
$t_list = new ca_lists();

$vb_show_add_checked_to_set = (bool)(is_array($va_sets = $this->getVar('available_sets')) && sizeof($va_sets) && $this->request->user->canDoAction('can_edit_sets'));
$vb_show_create_set_from_checked = (bool)$this->request->user->canDoAction('can_create_sets');

$va_set_options = array();
foreach ($va_sets as $vn_set_id => $va_set_info) {
    $va_set_options[$va_set_info['name']] = $vn_set_id;
}
?>

<div class="component component-search-sets">
    <?php if ($vb_show_add_checked_to_set || $vb_show_create_set_from_checked): ?>
        <div id="searchSetTools">
            <?php if ($vb_show_add_checked_to_set): ?>
                <form id="caAddToSet">
                    <label><?php print _t("Add checked records to set"); ?></label>
                    <div class="input-group">
                        <?php print caHTMLSelect('set_id', $va_set_options, array('id' => 'caAddToSetID', 'class' => 'form-control')); ?>
                        <div class="input-group-btn">
                            <button onclick="caAddItemsToSet();" class="btn btn-success">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <?php if ($vb_show_create_set_from_checked): ?>
                <form id="caCreateSetFromResults">
                    <label><?php print _t('Create set'); ?>&hellip;</label>
                    <div class="input-group">
                        <?php print caHTMLTextInput('set_name', array('id' => 'caCreateSetFromResultsInput', 'class' => 'form-control', 'value' => $this->getVar('result_context')->getSearchExpression())); ?>
                        <div class="input-group-btn">
                            <button type="button" onclick="caCreateSetFromResults();" class="btn btn-success">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label>
                            <input type="radio" name="set_create_mode" value="from_results" checked="checked" />
                            from results
                        </label>
                        <label>
                            <input type="radio" name="set_create_mode" value="from_checked" />
                            from checked
                        </label>
                    </div>
                    <?php if ($t_list->getAppConfig()->get('enable_set_type_controls')): ?>
                        <?php print $t_list->getListAsHTMLFormElement('set_types', 'set_type', array('id' => 'caCreateSetTypeID', 'class' => 'form-control')); ?>
                    <?php endif; ?>
                    <?php if ($this->request->user->canDoAction('can_batch_edit_'.$t_subject->tableName())): ?>
                        <label>
                            <input type="checkbox" name="batch_edit" value="1" id="caCreateSetBatchEdit" />
                            <?php print _t('open set for batch editing'); ?>
                        </label>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Find and return list of checked items to be added to set item_ids are returned in a simple array
    function caGetSelectedItemIDsToAddToSet() {
        var selectedItemIDS = [];
        jQuery('#caFindResultsForm .add-to-set').each(function(i, j) {
            if (jQuery(j).prop('checked')) {
                selectedItemIDS.push(jQuery(j).val());
            }
        });
        return selectedItemIDS;
    }

    function caToggleAddToSet() {
        jQuery('#caFindResultsForm .add-to-set').each(function(i, j) {
            jQuery(j).prop('checked', !jQuery(j).prop('checked'));
        });
        return false;
    }

    function caAddItemsToSet() {
        jQuery("#caAddToSetIDIndicator").show();
        jQuery.post(
            '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'addToSetx'); ?>',
            {
                set_id: jQuery('#caAddToSetID').val(),
                item_ids: caGetSelectedItemIDsToAddToSet().join(';')
            },
            function(res) {
                jQuery("#caAddToSetIDIndicator").hide();
                if (res['status'] === 'ok') {
                    var item_type_name;
                    if (res['num_items_added'] == 1) {
                        item_type_name = '<?php print addslashes($t_subject->getProperty('NAME_SINGULAR')); ?>';
                    } else {
                        item_type_name = '<?php print addslashes($t_subject->getProperty('NAME_PLURAL')); ?>';
                    }
                    var msg = '<?php print addslashes(_t('Added ^num_items ^item_type_name to <i>^set_name</i>'));?>';
                    msg = msg.replace('^num_items', res['num_items_added']);
                    msg = msg.replace('^item_type_name', item_type_name);
                    msg = msg.replace('^set_name', res['set_name']);

                    if (res['num_items_already_in_set'] > 0) {
                        msg += '<?php print addslashes(_t('<br/>(^num_dupes were already in the set.)')); ?>';
                        msg = msg.replace('^num_dupes', res['num_items_already_in_set']);
                    }

                    addNotification('<?php print __NOTIFICATION_TYPE_INFO__; ?>', msg);
                    jQuery('#caFindResultsForm .addItemToSetControl').attr('checked', false);
                } else {
                    addNotification('<?php print __NOTIFICATION_TYPE_ERROR__; ?>', res['error'] || 'An unknown error occurred');
                }
            },
            'json'
        );
        return false;
    }

    function caCreateSetFromResults() {
        jQuery("#caCreateSetFromResultsIndicator").show();
        jQuery.post(
            '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'createSetFromResult'); ?>',
            {
                set_name: jQuery('#caCreateSetFromResultsInput').val(),
                mode: jQuery('[name="set_create_mode"]:checked').val(),
                item_ids: caGetSelectedItemIDsToAddToSet().join(';'),
                set_type_id: jQuery('#caCreateSetTypeID').val()
            },
            function(res) {
                jQuery("#caCreateSetFromResultsIndicator").hide();
                if (res['status'] === 'ok') {
                    var item_type_name;
                    if (res['num_items_added'] == 1) {
                        item_type_name = '<?php print addslashes($t_subject->getProperty('NAME_SINGULAR')); ?>';
                    } else {
                        item_type_name = '<?php print addslashes($t_subject->getProperty('NAME_PLURAL')); ?>';
                    }
                    var msg = '<?php print addslashes(_t('Created set <i>^set_name</i> with ^num_items ^item_type_name'));?>';
                    msg = msg.replace('^num_items', res['num_items_added']);
                    msg = msg.replace('^item_type_name', item_type_name);
                    msg = msg.replace('^set_name', res['set_name']);

                    if (jQuery('#caCreateSetBatchEdit').prop('checked')) {
                        window.location = '<?php print caNavUrl($this->request, 'batch', 'Editor', 'Edit', array()); ?>/set_id/' + res['set_id'];
                    } else {
                        jQuery.jGrowl(msg, { header: '<?php print addslashes(_t('Create set')); ?>' });
                        // add new set to "add to set" list
                        jQuery('#caAddToSetID').append($("<option/>", {
                            value: res['set_id'],
                            text: res['set_name'],
                            selected: 1
                        }));
                        // add new set to search by set drop-down
                        jQuery("select.searchSetSelect").append($("<option/>", {
                            value: 'set:"' + res['set_code'] + '"',
                            text: res['set_name']
                        }));
                    }
                } else {
                    jQuery.jGrowl(res['error'], { header: '<?php print addslashes(_t('Create set')); ?>' });
                }
            },
            'json'
        );
    }
</script>
