<?php
AssetLoadManager::register('hierBrowser');
AssetLoadManager::register('tabUI');

$t_subject = $this->getVar('t_subject');
$vs_subject_label = $t_subject->getLabelForDisplay();

$vs_priv_table = $t_subject->tableName();
if ($vs_priv_table === 'ca_list_items') {
    $vs_priv_table = 'ca_lists';
}

$vb_batch = $this->getVar('batch');
$pn_parent_id = $this->getVar('parent_id');
$pa_ancestors = $this->getVar('ancestors');
$pn_id = $this->getVar('id');
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_items_in_hier = $t_subject->getHierarchySize();
$vs_bundle_preview = '(' . $vn_items_in_hier . ') ' . caProcessTemplateForIDs("^preferred_labels", $t_subject->tableName(), array($t_subject->getPrimaryKey()));

switch ($vs_priv_table) {
    case 'ca_relationship_types':
        $vb_has_privs = $this->request->user->canDoAction('can_configure_relationship_types');
        break;
    case 'ca_tour_stops':
        $vb_has_privs = $this->request->user->canDoAction('can_create_ca_tours');
        break;
    default:
        $vb_has_privs = $this->request->user->canDoAction('can_create_'.$vs_priv_table);
        break;
}

$vb_objects_x_collections_hierarchy_enabled = (bool)$t_subject->getAppConfig()->get('ca_objects_x_collections_hierarchy_enabled');
$vs_disabled_items_mode = $t_subject->getAppConfig()->get($t_subject->tableName() . '_hierarchy_browser_disabled_items_mode');
$vs_disabled_items_mode = $vs_disabled_items_mode ?: 'hide';
$t_object = new ca_objects();
$t_ui = ca_editor_uis::loadDefaultUI('ca_movements', $this->request, null, array('editorPref' => 'quickadd'));

$va_search_lookup_extra_params = array('noInline' => 1);
if ($t_subject->getProperty('HIERARCHY_ID_FLD') && ($vn_hier_id = (int)$t_subject->get($t_subject->getProperty('HIERARCHY_ID_FLD')))) {
    $va_search_lookup_extra_params['currentHierarchyOnly'] = $vn_hier_id;
}
if (in_array($t_subject->tableName(), array('ca_objects', 'ca_collections')) && $vb_objects_x_collections_hierarchy_enabled) {
    $va_lookup_urls_for_move = $va_lookup_urls = array(
        'search' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'Get', $va_search_lookup_extra_params),
        'levelList' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'GetHierarchyLevel'),
        'ancestorList' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'GetHierarchyAncestorList'),
        'sortSave' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'SetSortOrder')
    );
    $va_lookup_urls_for_move['search'] = caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'Get', array_merge($va_search_lookup_extra_params, ['currentHierarchyOnly' => null]));

    $vs_edit_url = caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'Edit').'/id/';
    $vn_init_id = $t_subject->tableName()."-".$pn_id;
} else {
    $va_lookup_urls 			= caJSONLookupServiceUrl($this->request, $t_subject->tableName(), $va_search_lookup_extra_params);
    $va_lookup_urls_for_move 	= caJSONLookupServiceUrl($this->request, $t_subject->tableName(), array_merge($va_search_lookup_extra_params, ['currentHierarchyOnly' => null]));
    $vs_edit_url = caEditorUrl($this->request, $t_subject->tableName());
    $vn_init_id = $pn_id;
}

$vb_strict_type_hierarchy = (bool)$this->request->config->get($t_subject->tableName().'_enforce_strict_type_hierarchy');
$vs_type_selector = trim($t_subject->getTypeListAsHTMLFormElement("{$vs_id_prefix}type_id", array('id' => "{$vs_id_prefix}TypeList"), array('childrenOfCurrentTypeOnly' => $vb_strict_type_hierarchy, 'includeSelf' => !$vb_strict_type_hierarchy, 'directChildrenOnly' => $vb_strict_type_hierarchy)));
$vs_object_type_selector = trim($t_object->getTypeListAsHTMLFormElement("{$vs_id_prefix}object_type_id", array('id' => "{$vs_id_prefix}ObjectTypeList"), array('childrenOfCurrentTypeOnly' => $vb_strict_type_hierarchy, 'includeSelf' => !$vb_strict_type_hierarchy, 'directChildrenOnly' => $vb_strict_type_hierarchy)));

$pa_bundle_settings = $this->getVar('settings');
$vb_read_only		=	((isset($pa_bundle_settings['readonly']) && $pa_bundle_settings['readonly'])  || ($this->request->user->getBundleAccessLevel($t_subject->tableName(), 'hierarchy_location') == __CA_BUNDLE_ACCESS_READONLY__));

$va_errors = $this->request->getActionErrors('hierarchy_location');

$va_path = array();
$va_object_collection_collection_ancestors = $this->getVar('object_collection_collection_ancestors');
$vb_do_objects_x_collections_hierarchy = false;
if ($vb_objects_x_collections_hierarchy_enabled && is_array($va_object_collection_collection_ancestors)) {
    $pa_ancestors = $va_object_collection_collection_ancestors + $this->getVar('ancestors');
    $vb_do_objects_x_collections_hierarchy = true;
}

if (is_array($pa_ancestors) && sizeof($pa_ancestors) > 0) {
    foreach ($pa_ancestors as $vn_id => $va_item) {
        $vs_item_id = $vb_do_objects_x_collections_hierarchy ? ($va_item['table'].'-'.$va_item['item_id']) : $va_item['item_id'];
        if ($vn_id === '') {
            $va_path[] = "<a href='#'>"._t('New %1', $t_subject->getTypeName())."</a>";
        } else {
            $vs_label = $va_item['label'];
            $vs_editor_url = caEditorUrl($this->request, $va_item['table'], $va_item['item_id']);
            if (($va_item['table'] != $t_subject->tableName()) || ($va_item['item_id'] && ($va_item['item_id'] != $pn_id))) {
                $va_path[] = "<a href=\"{$vs_editor_url}\">{$vs_label}</a>";
            } else {
                $vn_item_id = array_pop(explode("-", $vs_item_id));
                $va_path[] = "<a href=\"#\" onclick=\"$('#{$vs_id_prefix}HierarchyBrowserContainer').slideDown(250); o{$vs_id_prefix}ExploreHierarchyBrowser.setUpHierarchy('{$vn_item_id}'); return false;\">{$vs_label}</a>";
            }
        }
    }
}

$va_nav = $t_ui->getScreensAsNavConfigFragment($this->request, null, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), array(), array());

$t_movement = new ca_movements();

$va_bundle_list = array();
$va_form_elements = $t_movement->getBundleFormHTMLForScreen(
    $va_nav['defaultScreen'],
    array(
        'request' => $this->request,
        'formName' => $vs_id_prefix.'StorageLocationMovementForm',
        'omit' => array('ca_storage_locations')
    ),
    $va_bundle_list
);

$va_add_types = array(_t('under (child)') => 'under');

if (!$vb_strict_type_hierarchy) {
    $va_add_types[_t('above (parent)')] = 'above';
}

if (($pn_parent_id > 0) && !$vb_strict_type_hierarchy) {
    $va_add_types[_t('next to (sibling)')] = 'next_to';
}
?>

<?php if ($vb_batch): ?>
    <?php print caBatchEditorIntrinsicModeControl($t_subject, $vs_id_prefix); ?>
<?php endif; ?>

<?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $va_settings); ?>

<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-hierarchy-location">
	<div class="bundleContainer">
        <?php if (!$vb_batch): ?>
		    <div class="hierNav">
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

                <?php if (!$vb_batch && ($pn_id > 0)): ?>
                <div class="row">
                    <div class="col-md-9"><?php if($vn_items_in_hier > 0) { print _t("Number of %1 in hierarchy: %2", caGetTableDisplayName($t_subject->tableName(), true), $vn_items_in_hier); } ?></div>
                    <div class="col-md-3">
                        <a href="#" id="<?php print $vs_id_prefix; ?>browseToggle" class="form-button"><span class="btn btn-sm btn-default"><?php print _t('Show Hierarchy'); ?></span></a>
                    </div>
                </div>
                <?php endif; ?>

                <div id="<?php print $vs_id_prefix; ?>HierarchyPanelHeader" class="hierarchyPanelHeader">
                    <div id="<?php print $vs_id_prefix; ?>HierarchyHeaderContent" class="hierarchyHeaderContent">
                        <?php print join(' ➔ ', $va_path); ?>
				    </div>
                </div>

				<div id="<?php print $vs_id_prefix; ?>HierarchyHeaderScrollButtons">
					<div id="<?php print $vs_id_prefix; ?>NextPrevControls" class="btn-group">
                        <button type="button" id="<?php print $vs_id_prefix; ?>PrevControl" class="btn btn-default">&larr;</button>
                        <button type="button" id="<?php print $vs_id_prefix; ?>NextControl" class="btn btn-default">&rarr;</button>
                    </div>
				</div>
            </div>
        <?php endif; ?>

        <?php if ($pn_id > 0): ?>
		    <div id="<?php print $vs_id_prefix; ?>HierarchyBrowserContainer" class="editorHierarchyBrowserContainer">
			    <div  id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs">
				    <ul>
                        <?php if (!$vb_batch): ?>
                            <li>
                                <a href="#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-explore" onclick='_init<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser();'>
                                    <span><?php print _t('Explore'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ((!$vb_strict_type_hierarchy || $vb_batch) && !$vb_read_only): ?>
                            <li>
                                <a href="#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-move" onclick='_init<?php print $vs_id_prefix; ?>MoveHierarchyBrowser();'>
                                    <span><?php print _t('Move'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ((!$vb_read_only && $vb_has_privs && !$vb_batch) && (!$vb_strict_type_hierarchy || ($vb_strict_type_hierarchy && $vs_type_selector))): ?>
                            <li>
                                <a href="#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-add" onclick='_init<?php print $vs_id_prefix; ?>AddHierarchyBrowser();'>
                                    <span><?php print ($vb_objects_x_collections_hierarchy_enabled && ($t_subject->tableName() == 'ca_collections')) ? _t('Add level') : _t('Add'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ((!$vb_read_only && $vb_has_privs&& !$vb_batch) && $vb_objects_x_collections_hierarchy_enabled && ($t_subject->tableName() == 'ca_collections')): ?>
                            <li>
                                <a href="#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-addObject" onclick='_init<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser();'>
                                    <span><?php print _t('Add object'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php if (!$vb_batch): ?>
                        <div id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-explore" class="hierarchyBrowseTab">
                            <div class="clearfix">
                                <div class="pull-right col-md-8">
                                    <input type="text" id="<?php print $vs_id_prefix; ?>ExploreHierarchyBrowserSearch" name="search" value="" placeholder="<?php print _t('Find item in hierarchy...')?>" size="25"/>
                                </div>
                                <?php print _t('Click %1 names to explore. Click on an arrow icon to open a %1 for editing.', $t_subject->getProperty('NAME_SINGULAR')); ?>
                            </div>
                            <div id="<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser" class="hierarchyBrowserSmall">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ((!$vb_strict_type_hierarchy || $vb_batch) && !$vb_read_only): ?>
                        <div id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-move" class="hierarchyBrowseTab">
                            <div class="clearfix">
                                <div class="pull-right col-md-8">
                                    <label><?php print _t('Find'); ?></label>
                                    <input type="text" id="<?php print $vs_id_prefix; ?>MoveHierarchyBrowserSearch" name="search" value="" size="25"/>
                                </div>
                                <div><?php print _t('Click on an arrow to choose the location to move this record under.', $t_subject->getProperty('NAME_SINGULAR')); ?></div>
                                <div id='<?php print $vs_id_prefix; ?>HierarchyBrowserSelectionMessage' class='hierarchyBrowserNewLocationMessage'>
                                </div>
                            </div>
                            <div id="<?php print $vs_id_prefix; ?>MoveHierarchyBrowser" class="hierarchyBrowserSmall">
                            </div>
                            <?php if (($t_subject->tableName() == 'ca_storage_locations') && (bool)$t_subject->getAppConfig()->get('record_movement_information_when_moving_storage_location')): ?>
                                <?php if ($t_ui): ?>
                                    <div id="<?php print $vs_id_prefix; ?>StorageLocationMovementForm" style="width: 98%; margin: 5px 0px 2px 6px;">
                                        <h3><?php print _t('Movement details'); ?></h3>
                                        <?php print caHTMLHiddenInput($vs_id_prefix.'_movement_screen', array('value' => $va_nav['defaultScreen'])); ?>
                                        <?php print caHTMLHiddenInput($vs_id_prefix.'_movement_form_name', array('value' => $vs_id_prefix.'StorageLocationMovementForm')); ?>
                                        <?php foreach (groupFormElementsByBundle($va_bundle_list, $va_form_elements) as $va_group): ?>
                                            <div class="row">
                                                <?php print join("\n", $va_group); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ((!$vb_read_only && $vb_has_privs && !$vb_batch) && (!$vb_strict_type_hierarchy || ($vb_strict_type_hierarchy && $vs_type_selector))): ?>
                        <div id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-add"  class="hierarchyBrowseTab">
                            <div class="clearfix">
                                <div class="hierarchyBrowserMessageContainer">
                                    <strong>
                                        <?php print _t('Use the controls below to create new %1 relative to this record in the hierarchy.', $t_subject->getProperty('NAME_PLURAL'), $vs_subject_label); ?>
                                    </strong>
                                </div>

                                <div id='<?php print $vs_id_prefix; ?>HierarchyBrowseTypeMenu'>
                                    <div class="pull-left">
                                        <?php if ($vs_type_selector): ?>
                                            <div id="<?php print $vs_id_prefix; ?>HierarchyBrowseAdd">
                                                <?php print _t("Add a new %1 %2 <em>%3</em>", $vs_type_selector, caHTMLSelect('add_type', $va_add_types, array('id' => "{$vs_id_prefix}AddType")), $vs_subject_label); ?>
                                                <a href="#" onclick="_navigateToNewForm(
                                                    $('#<?php print $vs_id_prefix; ?>TypeList').val(),
                                                    $('#<?php print $vs_id_prefix; ?>AddType').val(),
                                                    $('#<?php print $vs_id_prefix; ?>AddType').val() === 'next_to' ?
                                                        <?php print intval($pn_parent_id) ?> :
                                                        <?php print intval($pn_id) . ',' . intval($pn_id) ?>)">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div id='<?php print $vs_id_prefix; ?>HierarchyBrowseAdd'>
                                                <?php print _t("Add a new %1 %2 <em>%3</em>",  $t_subject->getProperty('NAME_SINGULAR'), caHTMLSelect('add_type', $va_add_types, array('id' => "{$vs_id_prefix}AddType")), $vs_subject_label); ?>
                                                <a href="#" onclick="_navigateToNewForm(
                                                    0,
                                                    $('#<?php print $vs_id_prefix; ?>AddType').val(),
                                                    $('#<?php print $vs_id_prefix; ?>AddType').val() === 'next_to' ?
                                                    <?php print intval($pn_parent_id) ?> :
                                                    <?php print intval($pn_id) ?>)">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div id="<?php print $vs_id_prefix; ?>AddHierarchyBrowser" class="hierarchyBrowserSmall"></div>
                        </div>
                    <?php endif; ?>

                    <?php if ((!$vb_read_only && $vb_has_privs && !$vb_batch) && $vb_objects_x_collections_hierarchy_enabled && ($t_subject->tableName() == 'ca_collections')): ?>
                        <div id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-addObject"  class="hierarchyBrowseTab">
                            <div class="clearfix">
                                <div class="hierarchyBrowserMessageContainer">
                                    <?php print _t('Use the controls below to create new %1 relative to this %2 in the hierarchy.', $t_object->getProperty('NAME_PLURAL'), mb_strtolower($t_subject->getTypeName())); ?>
                                </div>

                                <div id='<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowseTypeMenu' style="margin-top: 15px;">
                                    <div style="float: left; width: 700px">
                                        <div id='<?php print $vs_id_prefix; ?>HierarchyBrowseAdd'>
                                            <?php print _t("Add a new %1 under <em>%2</em>", $vs_object_type_selector, $vs_subject_label); ?>
                                            <a href="#" onclick="_navigateToNewObjectForm($('#<?php print $vs_id_prefix; ?>ObjectTypeList').val(), <?php print intval($pn_id); ?>);">
                                                <span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser" class="hierarchyBrowserSmall">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" name="<?php print $vs_id_prefix; ?>_new_parent_id" id="<?php print $vs_id_prefix; ?>_new_parent_id" value="<?php print $pn_parent_id; ?>" />
        <?php endif; ?>
    </div>
</div>

<script>

    var _init<?php print $vs_id_prefix; ?>AddHierarchyBrowser,
        _init<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser,
        _init<?php print $vs_id_prefix; ?>MoveHierarchyBrowser,
        _navigateToNewForm,
        _navigateToNewObjectForm;

    (function ($) {
        'use strict';

        <?php if ($pn_id > 0): ?>
            $(function () {
                <?php if (!$vb_strict_type_hierarchy || $vb_batch): ?>
                    // Set up "move" hierarchy browse search
                    $('#<?php print $vs_id_prefix; ?>MoveHierarchyBrowserSearch').autocomplete(
                        {
                            source: '<?php print $va_lookup_urls_for_move['search']; ?>', minLength: 3, delay: 800, html: true,
                            select: function( event, ui ) {
                                if (ui.item.id) {
                                    $("#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer").slideDown(350);
                                    o<?php print $vs_id_prefix; ?>MoveHierarchyBrowser.setUpHierarchy(ui.item.id);	// jump browser to selected item
                                }
                                event.preventDefault();
                                $('#<?php print $vs_id_prefix; ?>MoveHierarchyBrowserSearch').val('');
                            }
                        }
                    ).click(function() { this.select() });
                <?php endif ?>

                $("#<?php print $vs_id_prefix; ?>browseToggle").click(function (e, opts) {
                    _init<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser();
                    var delay = (opts && opts.delay && (parseInt(opts.delay) >= 0)) ? opts.delay :  250;
                    $("#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer").slideToggle(delay, function() {
                        $("#<?php print $vs_id_prefix; ?>browseToggle").html((this.style.display === 'block') ? '<?php print '<span class="btn btn-default btn-sm">'._t('Close browser').'</span>';?>' : '<?php print '<span class="btn btn-default btn-sm">'._t('Show Hierarchy').'</span>';?>');
                    });
                    return false;
                });

                <?php if (!$vb_batch): ?>
                    // Set up "explore" hierarchy browse search
                    $('#<?php print $vs_id_prefix; ?>ExploreHierarchyBrowserSearch').autocomplete(
                        {
                            source: '<?php print $va_lookup_urls['search']; ?>', minLength: 3, delay: 800, html: true,
                            select: function( event, ui ) {
                                if (ui.item.id) {
                                    $("#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer").slideDown(350);
                                    o<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser.setUpHierarchy(ui.item.id);	// jump browser to selected item
                                }
                                event.preventDefault();
                                $('#<?php print $vs_id_prefix; ?>ExploreHierarchyBrowserSearch').val('');
                            }
                        }
                    ).click(function() { this.select() });

                    // Disable form change warnings to add type drop-downs
                    $('#<?php print $vs_id_prefix; ?>HierarchyBrowseAddUnder select').unbind('change');
                    $('#<?php print $vs_id_prefix; ?>HierarchyBrowseAddNextTo select').unbind('change');
                <?php endif; ?>

                $("#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs").tabs({ selected: 0 });
                $('#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer').hide(0);
            });

            <?php if (!$vb_batch): ?>
                _navigateToNewForm = function(type_id, action, id, after_id) {
                    switch (action) {
                        case 'above':
                            document.location = '<?php print caEditorUrl($this->request, $t_subject->tableName(), 0); ?>/type_id/' + type_id + '/above_id/' + id;
                            break;
                        case 'under':
                            document.location = '<?php print caEditorUrl($this->request, $t_subject->tableName(), 0); ?>/type_id/' + type_id + '/parent_id/' + id;
                            break;
                        case 'next_to':
                            document.location = '<?php print caEditorUrl($this->request, $t_subject->tableName(), 0); ?>/type_id/' + type_id + '/parent_id/' + id + '/after_id/' + after_id;
                            break;
                        default:
                            alert("Invalid action! (" + action + ")");
                            break;
                    }
                };

                _navigateToNewObjectForm = function(type_id, parent_collection_id) {
                    document.location = '<?php print caEditorUrl($this->request, "ca_objects", 0); ?>/type_id/' + type_id + '/collection_id/' + parent_collection_id;
                };

                // Set up "explore" hierarchy browser
                var o<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser = null;

                _init<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser = function() {
                    if (!o<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser) {
                        o<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser', {
                            levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                            initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',
                            dontAllowEditForFirstLevel: <?php print (in_array($t_subject->tableName(), array('ca_places', 'ca_storage_locations', 'ca_list_items', 'ca_relationship_types')) ? 'true' : 'false'); ?>,
                            readOnly: false, //<?php print $vb_read_only ? 1 : 0; ?>,
                            disabledItems: '<?php print $vs_disabled_items_mode; ?>',
                            editUrl: '<?php print $vs_edit_url; ?>',
                            editButtonIcon: '<span class="glyphicon glyphicon-edit"></span>',
                            disabledButtonIcon: '<span class="glyphicon glyphicon-remove-circle"></span>',
                            allowDragAndDropSorting: <?php print caDragAndDropSortingForHierarchyEnabled($this->request, $t_subject->tableName(), $t_subject->getPrimaryKey()) ? "true" : "false"; ?>,
                            sortSaveUrl: '<?php print $va_lookup_urls['sortSave']; ?>',
                            dontAllowDragAndDropSortForFirstLevel: true,
                            initItemID: '<?php print $vn_init_id; ?>',
                            indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>", // TODO FIXME loading animation
                            displayCurrentSelectionOnLoad: false,
                            autoShrink: <?php print (caGetOption('auto_shrink', $pa_bundle_settings, false) ? 'true' : 'false'); ?>,
                            autoShrinkAnimateID: '<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser'
                        });
                    }
                }
            <?php endif; ?>

            <?php if ((!$vb_strict_type_hierarchy || $vb_batch) && !$vb_read_only) : ?>
                // Set up "move" hierarchy browser
                var o<?php print $vs_id_prefix; ?>MoveHierarchyBrowser = null;

                _init<?php print $vs_id_prefix; ?>MoveHierarchyBrowser = function() {
                    if (!o<?php print $vs_id_prefix; ?>MoveHierarchyBrowser) {
                        o<?php print $vs_id_prefix; ?>MoveHierarchyBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>MoveHierarchyBrowser', {
                            levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                            initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',

                            readOnly: <?php print $vb_read_only ? 1 : 0; ?>,
                            disabledItems: '<?php print $vs_disabled_items_mode; ?>',

                            initItemID: '<?php print $vn_init_id; ?>',
                            indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>", // TODO FIXME loading animation
                            editButtonIcon: '<span class="glyphicon glyphicon-menu-right"></span>',
                            disabledButtonIcon: '<span class="glyphicon glyphicon-remove-circle"></span>',

                            allowDragAndDropSorting: <?php print caDragAndDropSortingForHierarchyEnabled($this->request, $t_subject->tableName(), $t_subject->getPrimaryKey()) ? "true" : "false"; ?>,
                            sortSaveUrl: '<?php print $va_lookup_urls['sortSave']; ?>',
                            dontAllowDragAndDropSortForFirstLevel: true,

                            currentSelectionIDID: '<?php print $vs_id_prefix; ?>_new_parent_id',
                            currentSelectionDisplayID: '<?php print $vs_id_prefix; ?>HierarchyBrowserSelectionMessage',
                            currentSelectionDisplayFormat: '<?php print addslashes(_t('Will be moved under <em>%1</em> after next save.')); ?>',

                            allowExtractionFromHierarchy: <?php print ($t_subject->getProperty('HIERARCHY_TYPE') == __CA_HIER_TYPE_ADHOC_MONO__) ? 'true' : 'false'; ?>,
                            extractFromHierarchyButtonIcon: '<span class="glyphicon glyphicon-export"></span>',
                            extractFromHierarchyMessage: '<?php print addslashes(_t('Will be placed at the top of its own hierarchy after next save.')); ?>',

                            onSelection: function(id, parent_id, name, formattedDisplay) {
                                // Update "move" status message
                                <?php if (($t_subject->tableName() == 'ca_collections')): ?>
                                if (id.substr(0, 10) == 'ca_objects') {
                                    formattedDisplay = '<?php print addslashes(_t("Cannot move collection under object")); ?>';
                                    $("#<?php print $vs_id_prefix; ?>HierarchyBrowserSelectionMessage").html(formattedDisplay);
                                    return;
                                }
                                <?php endif; ?>

                                $("#<?php print $vs_id_prefix; ?>HierarchyBrowserSelectionMessage").html(formattedDisplay);
                                if (caUI.utils.showUnsavedChangesWarning) {
                                    caUI.utils.showUnsavedChangesWarning(true);
                                }
                            },

                            displayCurrentSelectionOnLoad: false,
                            autoShrink: <?php print (caGetOption('auto_shrink', $pa_bundle_settings, false) ? 'true' : 'false'); ?>,
                            autoShrinkAnimateID: '<?php print $vs_id_prefix; ?>MoveHierarchyBrowser'
                        });
                    }
                }
            <?php endif; ?>

            <?php if (!$vb_batch && (!$vb_read_only && $vb_has_privs) && (!$vb_strict_type_hierarchy || ($vb_strict_type_hierarchy && $vs_type_selector))): ?>
                // Set up "add" hierarchy browser
                var o<?php print $vs_id_prefix; ?>AddHierarchyBrowser = null;

                _init<?php print $vs_id_prefix; ?>AddHierarchyBrowser = function() {
                    if (!o<?php print $vs_id_prefix; ?>AddHierarchyBrowser) {
                        o<?php print $vs_id_prefix; ?>AddHierarchyBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>AddHierarchyBrowser', {
                            levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                            initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',

                            readOnly: true,
                            allowSelection: false,
                            disabledItems: '<?php print $vs_disabled_items_mode; ?>',

                            initItemID: '<?php print $vn_init_id; ?>',
                            indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>", // TODO FIXME loading animation
                            displayCurrentSelectionOnLoad: true,
                            autoShrink: <?php print (caGetOption('auto_shrink', $pa_bundle_settings, false) ? 'true' : 'false'); ?>,
                            autoShrinkAnimateID: '<?php print $vs_id_prefix; ?>AddHierarchyBrowser'
                        });
                    }
                }
            <?php endif; ?>

            <?php if ((!$vb_batch && !$vb_read_only && $vb_has_privs) && $vb_objects_x_collections_hierarchy_enabled && ($t_subject->tableName() == 'ca_collections')): ?>
                // Set up "add object" hierarchy browser
                var o<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser = null;

                function _init<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser() {
                    if (!o<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser) {
                        o<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser', {
                            levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                            initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',

                            readOnly: true,
                            allowSelection: false,

                            initItemID: '<?php print $vn_init_id; ?>',
                            indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>", // TODO FIXME loading animation
                            displayCurrentSelectionOnLoad: true,
                            autoShrink: <?php print (caGetOption('auto_shrink', $pa_bundle_settings, false) ? 'true' : 'false'); ?>,
                            autoShrinkAnimateID: '<?php print $vs_id_prefix; ?>AddObjectHierarchyBrowser'
                        });
                    }
                }
            <?php endif; ?>
        <?php endif; ?>

        $(function() {
            // Remove "unsaved changes" warnings from search boxes
            $(".hierarchyBrowserFind input").unbind("change");

            // Remove "unsaved changes" warnings from drop-downs in "add" tab
            $("#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-add select").unbind("change");

            <?php if ($vb_batch): ?>
                $("#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer").show();
                $("#<?php print $vs_id_prefix; ?>").hide();
                _init<?php print $vs_id_prefix; ?>MoveHierarchyBrowser();
            <?php elseif (isset($pa_bundle_settings['open_hierarchy']) && (bool)$pa_bundle_settings['open_hierarchy']): ?>
                $("#<?php print $vs_id_prefix; ?>browseToggle").trigger("click", { "delay" : 0 });
            <?php endif; ?>

            //
            // Handle browse header scrolling
            //
            if ($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').width() > $('#<?php print $vs_id_prefix; ?>HierarchyPanelHeader').width()) {
                //
                // The content is wider than the content viewer area so set up the scroll
                //

                $('#<?php print $vs_id_prefix; ?>NextPrevControls').show(); // show controls

                var hierarchyHeaderContentWidth = $('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').width();
                var hierarchyPanelHeaderWidth = $('#<?php print $vs_id_prefix; ?>HierarchyPanelHeader').width();

                if (hierarchyHeaderContentWidth > hierarchyPanelHeaderWidth) {
                    $('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left', ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1) + "px"); // start at right side
                }

                //
                // Handle click on "specific" button
                //
                $('#<?php print $vs_id_prefix; ?>NextControl').click(function() {
                    if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) >  ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1)) {
                        //
                        // If we're not already at the right boundary then scroll right
                        //
                        var dl = (parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left')) - hierarchyPanelHeaderWidth);
                        if (dl < ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1)) { dl = ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1); }
                        $('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').stop().animate({'left': dl + "px" }, { duration: 300, easing: 'swing', queue: false, complete: function() {
                            if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) <=  ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1)) {
                                $('#<?php print $vs_id_prefix; ?>NextControl').hide();
                            }
                            if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) < 0) {
                                $('#<?php print $vs_id_prefix; ?>PrevControl').show();
                            }
                        }});
                    }
                    return false;
                });

                //
                // Handle click on "broader" button
                //
                $('#<?php print $vs_id_prefix; ?>PrevControl').click(function() {
                    if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) < 0) {
                        //
                        // Only scroll if we're not showing the extreme left side of the content area already.
                        //
                        var dl = parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left')) + hierarchyPanelHeaderWidth;
                        if (dl > 0) { dl = 0; }
                        $('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').stop().animate({'left': dl + "px"}, { duration: 300, easing: 'swing', queue: false, complete: function() {
                            if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) >= 0) {
                                $('#<?php print $vs_id_prefix; ?>PrevControl').hide();
                            }
                            if ((parseInt($('#<?php print $vs_id_prefix; ?>HierarchyHeaderContent').css('left'))) > ((hierarchyHeaderContentWidth - hierarchyPanelHeaderWidth) * -1)) {
                                $('#<?php print $vs_id_prefix; ?>NextControl').show();
                            }
                        }});
                    }
                    return false;
                });
                $('#<?php print $vs_id_prefix; ?>NextControl').hide();
            } else {
                //
                // Everything can fit without scrolling so hide the controls
                //
                $('#<?php print $vs_id_prefix; ?>NextPrevControls').hide();
            }
        });
    }(jQuery));
</script>
