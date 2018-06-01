<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance = $this->getVar('t_instance');
$t_item = $this->getVar('t_item');
$t_item_rel = $this->getVar('t_item_rel');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$va_rel_types = $this->getVar('relationship_types');
$vb_batch = $this->getVar('batch');
$va_initial_values = $this->getVar('initialValues');

$va_ids = array();
foreach($va_initial_values as $vn_rel_id => $va_rel_info) {
    $va_ids[$vn_rel_id] = $va_rel_info['id'];
}

$va_additional_search_controller_params = array(
    'ids' => json_encode($va_ids),
    'interstitialPrefix' => $vs_id_prefix . 'Item_',
    'relatedRelTable' => $t_item_rel->tableName(),
    'primaryTable' => $t_subject->tableName(),
    'primaryID' => $t_subject->getPrimaryKey(),
    'relatedTable' => $t_item->tableName(),
);

$vs_url_string = '';
foreach($va_additional_search_controller_params as $vs_key => $vs_val) {
    $vs_url_string .= '/' . $vs_key . '/' . urlencode($vs_val);
}

$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly']) ||
    $this->request->user->getBundleAccessLevel($t_instance->tableName(), $this->getVar('bundle_name')) === __CA_BUNDLE_ACCESS_READONLY__);

// params to pass during related item lookup
$va_lookup_params = array(
    'type' => isset($va_settings['restrict_to_type']) ? $va_settings['restrict_to_type'] : '',
    'noSubtypes' => (int)$va_settings['dont_include_subtypes_in_type_restriction'],
    'noInline' => (bool) preg_match("/QuickAdd$/", $this->request->getController()) ? 1 : 0
);

if ($vb_batch) {
    print caBatchEditorRelationshipModeControl($t_item, $vs_id_prefix);
}
print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix . $t_item->tableNum() . '_rel', $va_settings);

$va_errors = array();
foreach($va_action_errors = $this->request->getActionErrors($this->getVar('placement_code')) as $o_error) {
    $va_errors[] = $o_error->getErrorDescription();
}

$vn_type_field_width = sizeof($this->getVar('relationship_types_by_sub_type')) > 0 ? 3 : 0;
$vn_main_column_width = 8 - $vn_type_field_width;
?>
<div id="<?php print $vs_id_prefix.$t_item->tableNum() . '_rel'; ?>" class="component component-bundle component-bundle-related-list">
    <textarea class="related-item-template hidden">
        <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="related-item">
            <div class="elements-container removable">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="form-control" />
                    </div>
                    <?php if ($vn_type_field_width > 0): ?>
                        <div class="col-md-<?php print $vn_type_field_width; ?>">
                            <select name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" class="form-control hidden"></select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="button" class="remove">
                <span class="glyphicon glyphicon-remove"></span>
            </button>
            <a href="<?php print urldecode(caEditorUrl($this->request, $t_item->tableName(), '{'.$t_item->primaryKey().'}')); ?>" id="<?php print $vs_id_prefix; ?>_edit_related_{n}" class="edit-interstitial">
                <span class="glyphicon glyphicon-edit"></span>
            </a>
        </div>
    </textarea>

<div id="tableContent<?php print $vs_id_prefix; ?>"></div>

<div class="bundleContainer">
    <div class="item-list">
        <?php if (sizeof($va_errors)): ?>
            <span class="formLabelError"><?php print join("; ", $va_errors); ?></span>
        <?php endif; ?>
    </div>
    <input type="hidden" name="<?php print $vs_id_prefix; ?>BundleList" id="<?php print $vs_id_prefix; ?>BundleList" value=""/>
    <?php if (!$vb_read_only): ?>
        <button type="button" class="add top-right">
            <span class="glyphicon glyphicon-plus"></span>
            <?php print $this->getVar('add_label') ?: _t("Add relationship"); ?>
        </button>
    <?php endif; ?>
</div>
</div>

<div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal" role="dialog">
    <div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
</div>
<div id="caRelationEditorPanel<?php print $vs_id_prefix; ?>"  class="modal fade" data-toggle="modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="caRelationEditorPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-body"></div>
        </div>
    </div>
    <textarea class="caBundleDisplayTemplate hidden">
            <?php print caGetRelationDisplayString($this->request, $t_instance->tableName(), array(), array('display' => '_display', 'makeLink' => false)); ?>
        </textarea>
</div>

<script>
    var caRelationBundle<?php print $vs_id_prefix; ?>,
        initialRelationBundleOptions<?php print $vs_id_prefix; ?>,
        caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
        caRelationEditorPanel<?php print $vs_id_prefix; ?>,
        caAsyncSearchResultForm<?php print $vs_id_prefix; ?>;

    (function ($) {
        'use strict';

        $(function () {
            caAsyncSearchResultForm<?php print $vs_id_prefix; ?> = function (data) {
                var tableContent = $('#tableContent<?php print $vs_id_prefix; ?>');

                if (data) {
                    tableContent.html(data);
                }

                // have to re-init the relation bundle because the interstitial buttons have only now been loaded
                caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>', initialRelationBundleOptions<?php print $vs_id_prefix; ?>);

                $('#tableContent<?php print $vs_id_prefix; ?> .list-header-unsorted a, #tableContent<?php print $vs_id_prefix; ?> .list-header-sorted-desc a, #tableContent<?php print $vs_id_prefix; ?> .list-header-sorted-asc a').click(function(event) {
                    event.preventDefault();
                    $.get(event.target + '<?php print $vs_url_string; ?>', caAsyncSearchResultForm<?php print $vs_id_prefix; ?>);
                });

                tableContent.find('form').each(function() {
                    $(this).submit(function(event) {
                        event.preventDefault();
                        $.ajax({
                            type: 'POST',
                            url: event.target.action + '<?php print $vs_url_string; ?>',
                            data: $(this).serialize(),
                            success: caAsyncSearchResultForm<?php print $vs_id_prefix; ?>
                        });
                    });
                });
            };

            if (caUI.initPanel) {
                var quickAddPanelId = "caRelationQuickAddPanel<?php print $vs_id_prefix; ?>";
                caRelationQuickAddPanel<?php print $vs_id_prefix; ?> = caUI.initPanel({
                    panelID: quickAddPanelId,
                    panelContentID: quickAddPanelId + 'ContentArea',
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function () {
                        $('#' + quickAddPanelId).modal('show');
                    },
                    onCloseCallback: function () {
                        $('#' + quickAddPanelId).modal('hide');
                    }
                });

                var relationEditorPanelId = "caRelationEditorPanel<?php print $vs_id_prefix; ?>";
                caRelationEditorPanel<?php print $vs_id_prefix; ?> = caUI.initPanel({
                    panelID: relationEditorPanelId,
                    panelContentID: relationEditorPanelId + 'ContentArea',
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function () {
                        $('#' + relationEditorPanelId).modal('show');
                    },
                    onCloseCallback: function () {
                        $('#' + relationEditorPanelId).modal('hide');
                    }
                });
            }

            initialRelationBundleOptions<?php print $vs_id_prefix; ?> = {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                initialValues: <?php print json_encode($this->getVar('initialValues')); ?>,
                initialValueOrder: <?php print json_encode(array_keys($this->getVar('initialValues'))); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                placementID: '<?php print ((int)$va_settings['placement_id']); ?>',
                templateClassName: 'related-item-template',
                itemListClassName: 'item-list',
                listItemClassName: 'related-item',
                addButtonClassName: 'add',
                deleteButtonClassName: 'remove',
                hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_edit_related_'],
                showEmptyFormsOnLoad: 1,
                autocompleteUrl: '<?php print $vs_navurl = caNavUrl($this->request, 'lookup', ucfirst($t_item->getProperty('NAME_SINGULAR')), 'Get', $va_lookup_params); ?>',
                types: <?php print json_encode($va_settings['restrict_to_types']); ?>,
                restrictToSearch: <?php print json_encode($va_settings['restrict_to_search']); ?>,
                bundlePreview: <?php print caGetBundlePreviewForRelationshipBundle($this->getVar('initialValues')); ?>,
                readonly: <?php print $vb_read_only ? "true" : "false"; ?>,
                isSortable: false,

                quickaddPanel: caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
                quickaddUrl: '<?php print caEditorUrl($this->request, $t_item->tableName(), null, false, null, array('quick_add' => true)); ?>',

                interstitialButtonClassName: 'edit-interstitial',
                interstitialPanel: caRelationEditorPanel<?php print $vs_id_prefix; ?>,
                interstitialUrl: '<?php print caNavUrl($this->request, 'editor', 'Interstitial', 'Form', array('t' => $t_item_rel->tableName())); ?>',
                interstitialPrimaryTable: '<?php print $t_instance->tableName(); ?>',
                interstitialPrimaryID: <?php print (int)$t_instance->getPrimaryKey(); ?>,

                relationshipTypes: <?php print json_encode($this->getVar('relationship_types_by_sub_type')); ?>,
                templateValues: ['label', 'id', 'type_id'],

                minRepeats: <?php print caGetOption('minRelationshipsPerRow', $va_settings, 0); ?>,
                maxRepeats: <?php print caGetOption('maxRelationshipsPerRow', $va_settings, 65535); ?>
            };

            <?php if (sizeof($va_initial_values)): ?>
            $.get('<?php print caNavUrl($this->request, 'find', 'RelatedList', 'Index', $va_additional_search_controller_params); ?>', caAsyncSearchResultForm<?php print $vs_id_prefix; ?>);
            <?php endif; ?>

            // only init bundle if there are no values, otherwise we do it after the content is loaded
            <?php if (!sizeof($va_initial_values)): ?>
            caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>', initialRelationBundleOptions<?php print $vs_id_prefix; ?>);
            <?php endif; ?>
        });
    }(jQuery));
</script>
