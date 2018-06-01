<?php
AssetLoadManager::register('hierBrowser');

$vs_id_prefix = $this->getVar('placement_code') . $this->getVar('id_prefix');
$t_instance = $this->getVar('t_instance');
$t_subject = $this->getVar('t_subject');
$t_item = $this->getVar('t_item');
$t_item_rel = $this->getVar('t_item_rel');
$t_root_item = new ca_list_items();
$va_settings = $this->getVar('settings');
$vb_batch = $this->getVar('batch');
$vb_objects_to_object_lots = $t_item->tableName() === 'ca_objects' && $t_subject->tableName() === 'ca_object_lots';
$vb_objects_to_object_representations = $t_item->tableName() === 'ca_objects' && $t_subject->tableName() === 'ca_object_representations';

$vs_sort = ((isset($va_settings['sort']) && $va_settings['sort'])) ? $va_settings['sort'] : '';
$vs_bundle_name = $this->getVar('bundle_name') ?: $t_item->tableName();
$vb_read_only = ((isset($va_settings['readonly']) && $va_settings['readonly']) ||
    ($this->request->user->getBundleAccessLevel($t_instance->tableName(), $vs_bundle_name) === __CA_BUNDLE_ACCESS_READONLY__));
$vb_dont_show_del = ((isset($va_settings['dontShowDeleteButton']) && $va_settings['dontShowDeleteButton'])) ? true : false;
$vb_quick_add_enabled = $this->getVar('quickadd_enabled');
$va_relationship_types_by_sub_type = $this->getVar('relationship_types_by_sub_type');

$o_dm = Datamodel::load();
$vs_instance_name_singular_upper = preg_replace('/\s+/', '', ucwords($o_dm->getTableProperty($t_item->tableNum(), 'NAME_SINGULAR')));
$vs_instance_name_plural_lower = preg_replace('/\s+/', '_', $o_dm->getTableProperty($t_item->tableNum(), 'NAME_PLURAL'));

// Overridable display settings for panels and the checklist/relationship bundles
$vb_create_quickadd_panel = $this->getVar('createQuickAddPanel') !== false;
$vb_create_editor_panel = $this->getVar('createEditorPanel') !== false;
$vb_create_relationship_bundle = $this->getVar('createRelationshipBundle') !== false;
$vb_create_checklist_bundle = $this->getVar('createCheckListBundle');
$vb_create_hierarchy_bundle = (bool)$va_settings['useHierarchicalBrowser'];

$va_restrict_to_lists = $va_settings['restrict_to_lists'];

// params to pass during typeahead lookup
$va_lookup_params = array(
    'types' => isset($va_settings['restrict_to_types']) ? $va_settings['restrict_to_types'] : (isset($va_settings['restrict_to_type']) ? $va_settings['restrict_to_type'] : ''),
    'noSubtypes' => (int)$va_settings['dont_include_subtypes_in_type_restriction'],
    'noInline' => (!$vb_quick_add_enabled || (bool)preg_match("/QuickAdd$/", $this->request->getController())) ? 1 : 0,
    'limit' => 20
);

$vs_autocomplete_url = caNavUrl($this->request, 'lookup', $this->getVar('autocompleteController') ?: $vs_instance_name_singular_upper, 'Get', $va_lookup_params);
$vs_module_path = ($this->getVar('moduleDirectory') ?: 'editor') . '/' . $vs_instance_name_plural_lower;

$va_quickadd_params = array(
    $t_item->primaryKey() => 0,
    'dont_include_subtypes_in_type_restriction' => (int)$va_settings['dont_include_subtypes_in_type_restriction'],
    'prepopulate_fields' => join(';', $va_settings['prepopulateQuickaddFields'])
);
$va_additional_quickaddd_params = $this->getVar('additionalQuickAddParameters');
if($va_additional_quickaddd_params && count($va_additional_quickaddd_params) > 0) {
    $va_quickadd_params = array_merge($va_quickadd_params, $va_additional_quickaddd_params);
}

$vs_relation_display_string = caGetRelationDisplayString(
    $this->request,
    $t_item->tableName(),
    $va_settings['list_format'] === 'list' ?
        array() :
        array( 'id' => "{$vs_id_prefix}_edit_related_{n}" ),
    array(
        'display' => '_display',
        'makeLink' => true,
        'relationshipTypeDisplayPosition' => $vb_objects_to_object_lots ? 'none' : null
    )
);

$va_errors = $this->request->getActionErrors($this->getVar('placement_code'));
$vn_use_as_root_id = $vb_create_hierarchy_bundle &&
    sizeof($va_restrict_to_lists) === 1 &&
    $t_root_item->load(array('list_id' => $va_restrict_to_lists[0], 'parent_id' => null)) ?
        $t_root_item->getPrimaryKey() :
        'null';
?>

<div id="<?php print $vs_id_prefix . $t_item->tableNum(); ?>_rel" class="component component-bundle component-bundle-relationships">
    <textarea class="relationship-template hidden">
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="related-item <?php print $va_settings['list_format']; ?>">
            <div class="elements-container removable">
                <?php if ($va_settings['list_format'] === 'bubbles'): ?>
                    <span class="glyphicon glyphicon-menu-hamburger text-muted"></span>
                <?php endif; ?>
                <span id="<?php print $vs_id_prefix; ?>_BundleTemplateDisplay{n}">
                    <?php print $vs_relation_display_string; ?>
                </span>
                <?php if (!$vb_read_only && ca_editor_uis::loadDefaultUI($t_item_rel->tableNum(), $this->request)): ?>
                    <a href="<?php print urldecode(caEditorUrl($this->request, $t_item->tableName(), '{'.$t_item->primaryKey().'}')); ?>" class="edit-interstitial" id="<?php print $vs_id_prefix; ?>_edit_related_{n}">
                        <span class="glyphicon glyphicon-edit"></span>
                    </a>
                <?php endif; ?>
                <input type="hidden" name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" value="{type_id}"/>
                <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                <?php if ($va_settings['list_format'] === 'bubbles'): ?>
                    <div class="item-name hidden">{<?php print $this->getVar('labelField'); ?>}</div>
                    <div class="item-idno hidden">{idno_sort}</div>
                <?php endif; ?>
            </div>
            <?php if (!$vb_dont_show_del): ?>
                <button type="button" class="remove" title="<?php print _t('Remove this relationship'); ?>">
                    <?php print $this->getVar('remove_label'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            <?php endif; ?>
        </div>
    </textarea>

    <textarea class="relationship-new-item-template hidden">
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="related-item related-item-new">
            <div class="elements-container removable">
                <?php if (!$vb_create_hierarchy_bundle): ?>
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" />
                        </div>
                        <div class="col-md-3">
                            <?php if (sizeof($va_relationship_types_by_sub_type) > 1): ?>
                                <select name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" style="display: none;"></select>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (sizeof($va_relationship_types_by_sub_type) === 1): ?>
                        <input type="hidden" name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" />
                    <?php endif; ?>
                    <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                <?php else: ?>
                    <div>
                        <div id='<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}' class='hierarchyBrowser'>
                            <!-- Content for hierarchy browser is dynamically inserted here by ca.hierbrowser -->
                        </div>
                        <div class="row">
                            <div class='col-md-12 hierarchyBrowserSearchBar'>
                                <label for="<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}"></label>
                                <input type="text" id="<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}" name="search" placeholder="<?php print _t('Search'); ?>"/>
                            </div>
                        </div>
                        <div class="hierarchyBrowserCurrentSelectionText row">
                            <div class="col-md-6">
                                <span class="hierarchyBrowserCurrentSelectionText" id="<?php print $vs_id_prefix; ?>_browseCurrentSelectionText{n}"></span>
                                <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                            </div>
                            <div class="col-md-6">
                                <select title="<?php print _t('Relationship type')?>" name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" class="hidden"></select>
                            </div>
                        </div>
                    </div>
                    <script>
                        (function ($) {
                            'use strict';

                            $(function() {
                                var <?php print $vs_id_prefix; ?>oHierBrowser{n} = addHierBrowser('{n}');
                            });
                        }(jQuery));
                    </script>
                <?php endif; ?>
            </div>
            <?php if (!$vb_read_only): ?>
                <button type="button" class="remove" title="<?php print _t('Remove this relationship'); ?>">
                    <?php print $this->getVar('remove_label'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            <?php endif; ?>
        </div>
    </textarea>

    <?php if ($vb_batch): ?>
        <?php print caBatchEditorRelationshipModeControl($t_item, $vs_id_prefix); ?>
    <?php endif; ?>

    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix . $t_item->tableNum() . '_rel', $va_settings); ?>

    <div class="clearfix relationship-controls">
        <?php if (sizeof($this->getVar('initialValues'))): ?>
            <?php if (!$vb_read_only && !$vs_sort && ($va_settings['list_format'] !== 'list')): ?>
                <div class="pull-left text-nowrap">
                    <?php print caEditorBundleSortControls($this->request, $vs_id_prefix, $t_item->tableName(), $va_settings); ?>
                </div>
            <?php endif; ?>
            <div class="pull-right text-nowrap">
                <?php print caGetPrintFormatsListAsHTMLForRelatedBundles($vs_id_prefix, $this->request, $t_instance, $t_item, $t_item_rel, $this->getVar('initialValues')); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bundleContainer">
        <?php if (is_array($va_errors) && sizeof($va_errors)): ?>
            <div class="alert-danger">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                <?php print _t('Errors:'); ?>
                <ul>
                    <?php foreach ($va_errors as $vs_error): ?>
                        <li><?php print $vs_error->getErrorDescription(); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="item-list"></div>
        <?php if (!$vb_read_only): ?>
            <button type="button" class="add top-right" title="<?php print _t('Add another'); ?>">
                <?php print $this->getVar('add_label') ?: _t('Add relationship'); ?>
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        <?php endif; ?>
        <input type="hidden" name="<?php print $vs_id_prefix; ?>BundleList" id="<?php print $vs_id_prefix; ?>BundleList" value=""/>
    </div>

    <?php if ($vb_quick_add_enabled): ?>
        <div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal" role="dialog">
            <div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
        </div>
    <?php endif; ?>
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
</div>

<script>
    var caRelationBundle<?php print $vs_id_prefix; ?>,caRelationEditorPanel<?php print $vs_id_prefix; ?>, addHierBrowser;

    (function ($) {
        $(function() {
            var quickAddPanelId, relationEditorPanelId, caRelationQuickAddPanel<?php print $vs_id_prefix; ?>;


            if (caUI.initPanel) {
                <?php if($vb_create_quickadd_panel): ?>
                    quickAddPanelId = "caRelationQuickAddPanel<?php print $vs_id_prefix; ?>";
                    caRelationQuickAddPanel<?php print $vs_id_prefix; ?> = caUI.initPanel({
                        panelID: quickAddPanelId, /* DOM ID of the <div> enclosing the panel */
                        panelContentID: quickAddPanelId + 'ContentArea', /* DOM ID of the content area <div> in the panel */
                        initialFadeIn: false,
                        useExpose: false,
                        onOpenCallback: function () {
                            $('#' + quickAddPanelId).modal('show');
                        },
                        onCloseCallback: function () {
                            $('#' + quickAddPanelId).modal('hide');
                        }
                    });
                <?php endif; ?>
                <?php if($vb_create_editor_panel): ?>
                        relationEditorPanelId = "caRelationEditorPanel<?php print $vs_id_prefix; ?>";
                        caRelationEditorPanel<?php print $vs_id_prefix; ?> = caUI.initPanel({
                            panelID: relationEditorPanelId, /* DOM ID of the <div> enclosing the panel */
                            panelContentID: relationEditorPanelId + 'ContentArea', /* DOM ID of the content area <div> in the panel */
                            initialFadeIn: false,
                            useExpose: false,
                            onOpenCallback: function () {
                                $('#' + relationEditorPanelId).modal('show');
                            },
                            onCloseCallback: function () {
                                $('#' + relationEditorPanelId).modal('hide');
                            }
                        });
                <?php endif; ?>
                <?php if($vb_create_relationship_bundle): ?>
                $('#<?php print $vs_id_prefix; ?>caItemListSortControlTrigger').click(function () {
                    $('#<?php print $vs_id_prefix; ?>caItemListSortControls').slideToggle(200);
                    return false;
                });
                $('#<?php print $vs_id_prefix; ?>caItemListSortControls a.caItemListSortControl').click(function () {
                    $('#<?php print $vs_id_prefix; ?>caItemListSortControls').slideUp(200);
                    return false;
                });
            }
            caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix . $t_item->tableNum(); ?>_rel', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: <?php print json_encode($this->getVar('templateValues')); ?>,
                initialValues: <?php print json_encode($this->getVar('initialValues')); ?>,
                initialValueOrder: <?php print json_encode(array_keys($this->getVar('initialValues'))); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                placementID: '<?php print $va_settings['placement_id']; ?>',
                templateClassName: 'relationship-new-item-template',
                initialValueTemplateClassName: 'relationship-template',
                itemListClassName: 'item-list',
                listItemClassName: 'related-item',
                addButtonClassName: 'add',
                deleteButtonClassName: 'remove',
                hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_edit_related_'],
                showEmptyFormsOnLoad: 1,
                relationshipTypes: <?php print ($vb_objects_to_object_representations ? '{}' : json_encode($va_relationship_types_by_sub_type)); ?>,
                lists: <?php print json_encode($va_restrict_to_lists); ?>,
                types: <?php print json_encode($va_settings['restrict_to_types']); ?>,
                restrictToSearch: <?php print json_encode($va_settings['restrict_to_search']); ?>,
                bundlePreview: <?php print caGetBundlePreviewForRelationshipBundle($this->getVar('initialValues')); ?>,
                readonly: <?php print json_encode($vb_read_only); ?>,
                isSortable: <?php print json_encode(!$vb_read_only && !$vs_sort); ?>,
                listSortOrderID: '<?php print $vs_id_prefix; ?>BundleList',
                listSortItems: '.bubbles',
                itemColor: '<?php print (isset($va_settings['colorItem']) && $va_settings['colorItem'] ? $va_settings['colorItem'] : ''); ?>',
                firstItemColor: '<?php print (isset($va_settings['colorFirstItem']) && $va_settings['colorFirstItem'] ? $va_settings['colorFirstItem'] : ''); ?>',
                lastItemColor: '<?php print (isset($va_settings['colorLastItem']) && $va_settings['colorLastItem'] ? $va_settings['colorLastItem'] : ''); ?>',
                sortUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Sort', array('table' => $t_item_rel->tableName())); ?>',
                autocompleteUrl: '<?php print $vs_autocomplete_url ?>',
                <?php if ($vb_quick_add_enabled): ?>
                quickaddPanel: caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
                quickaddUrl: '<?php print caNavUrl($this->request, $vs_module_path, $vs_instance_name_singular_upper . 'QuickAdd', 'Form', $va_quickadd_params); ?>',
                <?php endif; ?>
                <?php if ($vb_objects_to_object_representations): ?>
                minRepeats: <?php print caGetOption('minRelationshipsPerRow', $va_settings, 0); ?>,
                maxRepeats: <?php print caGetOption('maxRelationshipsPerRow', $va_settings, 65535); ?>,
                <?php endif; ?>
                interstitialButtonClassName: 'edit-interstitial',
                interstitialPanel: caRelationEditorPanel<?php print $vs_id_prefix; ?>,
                interstitialUrl: '<?php print caNavUrl($this->request, 'editor', 'Interstitial', 'Form', array('t' => $t_item_rel->tableName())); ?>',
                interstitialPrimaryTable: '<?php print $t_instance->tableName(); ?>',
                interstitialPrimaryID: <?php print (int)$t_instance->getPrimaryKey(); ?>
            });
            <?php endif; ?>
            <?php if($vb_create_checklist_bundle): ?>
            caUI.initChecklistBundle('#<?php print $vs_id_prefix . $t_item->tableNum() . '_rel'; ?>', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: <?php print json_encode($this->getVar('templateValues')); ?>,
                initialValues: <?php print json_encode($this->getVar('initialValues')); ?>,
                initialValueOrder: <?php print json_encode(array_keys($this->getVar('initialValues'))); ?>,
                errors: <?php print json_encode($va_errors); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                templateClassName: 'relationship-new-item-template',
                itemListClassName: 'item-list',
                minRepeats: <?php print caGetOption('minRelationshipsPerRow', $va_settings, 0); ?>,
                maxRepeats: <?php print caGetOption('maxRelationshipsPerRow', $va_settings, 65535); ?>,
                defaultValues: <?php print json_encode($this->getVar('element_value_defaults')); ?>,
                readonly: <?php print json_encode($vb_read_only); ?>,
                defaultLocaleID: <?php print json_encode(ca_locales::getDefaultCataloguingLocaleID()); ?>
            });
            <?php endif; ?>
            <?php if($vb_create_hierarchy_bundle): ?>
                addHierBrowser = function (n) {
                    var hierBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>_hierarchyBrowser' + n, {
                        uiStyle: 'horizontal',
                        levelDataUrl: '<?php print caNavUrl($this->request, 'lookup', $vs_instance_name_singular_upper, 'GetHierarchyLevel', array('noSymbols' => 1, 'voc' => 1, 'lists' => is_array($va_restrict_to_lists) ? join(';', $va_restrict_to_lists) : "")); ?>',
                        initDataUrl: '<?php print caNavUrl($this->request, 'lookup', $vs_instance_name_singular_upper, 'GetHierarchyAncestorList'); ?>',
                        bundle: '<?php print $vs_id_prefix; ?>',
                        selectOnLoad: true,
                        browserWidth: "<?php print $va_settings['hierarchicalBrowserWidth']; ?>",
                        dontAllowEditForFirstLevel: false,
                        className: 'hierarchyBrowserLevel',
                        classNameContainer: 'hierarchyBrowserContainer',
                        editButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_RIGHT_ARROW__, 1); ?>",
                        disabledButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_DOT__, 1); ?>",
                        useAsRootID: <?php print $vn_use_as_root_id; ?>,
                        indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>",
                        displayCurrentSelectionOnLoad: true,
                        currentSelectionDisplayID: '<?php print $vs_id_prefix; ?>_browseCurrentSelectionText' + n,
                        onSelection: function (item_id, parent_id, name, display, type_id) {
                            caRelationBundle<?php print $vs_id_prefix; ?>.select(n, {
                                id: item_id,
                                type_id: type_id
                            }, display);
                            $('#<?php print $vs_id_prefix; ?>_type_id' + n).removeClass('hidden');
                        }
                    });
                    $('#' + hierBrowser.container).resizable({
                        containment: ".component-screen"
                    });
                    $('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch' + n).autocomplete({
                        source: '<?php print $vs_autocomplete_url; ?>',
                        minLength: 1, delay: 400, html: true,
                        select: function (event, ui) {
                            if (parseInt(ui.item.id) > 0) {
                                hierBrowser.setUpHierarchy(ui.item.id);	// jump browser to selected item
                            }
                            event.preventDefault();
                        }
                    });
                    return hierBrowser;
                };
            <?php endif; ?>
        });
    }(jQuery));
</script>
