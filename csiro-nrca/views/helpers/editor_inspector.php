<?php
require_once(__CA_MODELS_DIR__.'/ca_sets.php');
require_once(__CA_MODELS_DIR__.'/ca_data_exporters.php');
require_once(__CA_MODELS_DIR__.'/ca_watch_list.php');
require_once(__CA_MODELS_DIR__.'/ca_object_lots.php');
require_once(__CA_MODELS_DIR__.'/ca_objects.php');

$o_dm = Datamodel::load();
$o_app_plugin_manager = new ApplicationPluginManager();

$t_item = $this->getVar('t_item');
$t_type_instance = $t_item->getTypeInstance();
$vn_row_id = $t_item->get('row_id');
$vn_set_table_num = $t_item->get('table_num') ?: $this->request->getParameter('table_num', pInteger);
$vs_set_table_name = caGetTableDisplayName($vn_set_table_num);

$t_user = new ca_users($t_item->get('user_id') ?: $this->request->getUserID());
$t_content_instance = $t_item->getAppDatamodel()->getInstanceByTableNum($t_item->get('table_num'));
$t_list_item = new ca_list_items();
$t_screen = new ca_editor_ui_screens($this->request->getParameter('screen_id', pInteger));
$t_watch_list = new ca_watch_list();
$t_set = new ca_sets();
$t_object = new ca_objects();

$t_list = $this->getVar('t_list');
$vn_list_id = $t_list ? $t_list->getPrimaryKey() : null;

$t_rel_instance = $t_item->getAppDatamodel()->getInstanceByTableNum($t_item->get('table_num'), true);

$va_uis = method_exists($t_item, 'getUIs') ? $t_item->getUIs() : array();

$va_creation = $t_item->getCreationTimestamp();
$vs_creation_name = trim($va_creation['fname'] . ' ' . $va_creation['lname']) ?: null;
$vs_creation_interval = (($vn_t = (time() - $va_creation['timestamp'])) == 0) ? _t('Just now') : _t('%1 ago', caFormatInterval($vn_t , 2));

$va_last_change = $t_item->getLastChangeTimestamp();
$vs_last_change_name = trim($va_last_change['fname'] . ' ' . $va_last_change['lname']) ?: null;
$vs_last_change_interval = (($vn_t = (time() - $va_last_change['timestamp'])) == 0) ? _t('Just now') : _t('%1 ago', caFormatInterval($vn_t , 2));

$va_violations = array();
if (method_exists($t_item, 'getMetadataDictionaryRuleViolations')){
    $va_violations = $t_item->getMetadataDictionaryRuleViolations() ?: array();
}
$vn_num_violations = sizeof($va_violations);
$vs_num_violations_display = ($vn_num_violations > 1) ? _t('%1 problems require attention', $vn_num_violations) : _t('%1 problem requires attention', $vn_num_violations);
$va_violation_messages = array_map(function ($va_violation) use ($t_item) { return '<li>' . $t_item->getDisplayLabel($va_violation['bundle_name']) . $va_violation['violationMessage'] . '</li>'; }, $va_violations);

$vs_table_name = $vs_priv_table_name = $t_item->tableName();
if ($vs_table_name === 'ca_list_items') {
    $vs_priv_table_name = 'ca_lists';
}

$vs_idno = $t_item->get($t_item->getProperty('ID_NUMBERING_ID_FIELD'));
$vn_item_id = $t_item->getPrimaryKey();
$o_result_context = $this->getVar('result_context');
$t_ui = $this->getVar('t_ui');
$t_type = method_exists($t_item, "getTypeInstance") ? $t_item->getTypeInstance() : null;
$vs_type_name = method_exists($t_item, "getTypeName") ? $t_item->getTypeName() : $t_item->getProperty('NAME_SINGULAR');
$va_stats = method_exists($t_item, 'getMappingStatistics') ? $t_item->getMappingStatistics() : array();

$vs_screen_name = $t_screen->getLabelForDisplay() ?: _t('new screen');

$vs_rel_table = $this->request->getParameter('rel_table', pString);
$vn_rel_type_id = $this->request->getParameter('rel_type_id', pString);
$vn_rel_id = $this->request->getParameter('rel_id', pInteger);
$t_rel = $this->request->datamodel->getTableInstance($vs_rel_table);
$t_rel_table_default_ui = ca_editor_uis::loadDefaultUI($vs_rel_table, $this->request, null);
$vs_rel_table_ui_screen_url_suffix = $t_rel_table_default_ui ? '/' . $t_rel_table_default_ui->getScreenWithBundle('ca_object_representations', $this->request) : '';

$va_representations = array_filter(
    $this->getVar('representations') ?: array(),
    function ($pa_representation) {
        return $pa_representation['info']['preview170']['WIDTH'] && $pa_representation['info']['preview170']['HEIGHT'];
    }
);

$vn_primary_representation_index = array_filter($va_representations, function ($pa_representation) {
    return $pa_representation['is_primary'];
})[0];

if ($t_item->isHierarchical()) {
    $va_ancestors = $this->getVar('ancestors');
    $vn_parent_id = $t_item->get($t_item->getProperty('HIERARCHY_PARENT_ID_FLD'));
} else {
    $va_ancestors = array();
    $vn_parent_id = null;
}
$vn_parent_index = (sizeof($va_ancestors) - 1);

// action extra to preserve currently open screen across next/previous links
$vs_screen_extra = ($this->getVar('screen')) ? '/'.$this->getVar('screen') : '';

$vs_pk = $t_item->primaryKey();
$vs_table_name = $t_item->tableName();
$vs_priv_table_name = ($vs_table_name === 'ca_list_items') ? 'ca_lists' : $vs_table_name;

// Result set navigation
$va_found_ids = $o_result_context->getResultList();
$vn_current_pos = $o_result_context->getIndexInResultList($vn_item_id);
$vs_back_text = _t('Results (%1 / %2)', $vn_current_pos, sizeof($va_found_ids));

$vn_prev_id = $o_result_context->getPreviousID($vn_item_id);
$vb_can_access_previous = $this->request->user->canAccess($this->request->getModulePath(),$this->request->getController(), 'Edit', array($vs_pk => $vn_prev_id));
$vs_previous_action = ($vb_can_access_previous && !$this->request->getAppConfig()->get($vs_table_name.'_editor_defaults_to_summary_view')) ? ('Edit/' . $this->request->getActionExtra()) : 'Summary';
$vs_previous_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $vs_previous_action, array($vs_pk => $vn_prev_id));

$vn_next_id = $o_result_context->getNextID($vn_item_id) ?: (sizeof($va_found_ids) > 0 ? $va_found_ids[0] : null);
$vb_can_access_next = $this->request->user->canAccess($this->request->getModulePath(),$this->request->getController(), 'Edit', array($vs_pk => $vn_next_id));
$vs_next_action = ($vb_can_access_next && !$this->request->getAppConfig()->get($vs_table_name.'_editor_defaults_to_summary_view')) ? ('Edit/' . $this->request->getActionExtra()) : 'Summary';
$vs_next_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $vs_next_action, array($vs_pk => $vn_next_id));

// Info box
$vs_color = ($t_type ? trim($t_type->get('color')) : null) ?: ($t_ui ? trim($t_ui->get('color')) : null) ?: 'ffffff';
$vs_icon = ($t_type ? $t_type->getMediaTag('icon', 'icon') : null) ?: ($t_ui ? $t_ui->getMediaTag('icon', 'icon') : null);
$vs_description = ($this->request->user->canDoAction("can_edit_".$vs_priv_table_name) && (sizeof($t_item->getTypeList()) > 1)) ? 'Editing %1' : 'Viewing %1';
$va_history_placements = $t_ui && method_exists($t_ui, 'getPlacementsForBundle') ? $t_ui->getPlacementsForBundle('ca_objects_history') : null;
$va_history_bundle_settings = is_array($va_history_placements) && !empty($va_history_placements) ? array_shift($va_history_placements)['settings'] : array();
$va_history = method_exists($t_item, 'getObjectHistory') ? $t_item->getObjectHistory($va_history_bundle_settings, array('limit' => 1, 'currentOnly' => true)) : null;
$vs_location_template = "<ifdef code='ca_storage_locations.parent.preferred_labels'>^ca_storage_locations.parent.preferred_labels ➜ </ifdef>^ca_storage_locations.preferred_labels.name";
$vs_inspector_current_location_label = $this->request->config->get("ca_objects_inspector_current_location_label") ?: _t('Current');
$va_current_location = is_array($va_history) && !empty($va_history) ? array_shift(array_shift($va_history)) : null;
$vs_current_location = null;
$vs_full_location_hierarchy = '';

if (is_array($va_current_location) && $va_current_location['display']) {
    $vs_current_location = $va_current_location['display'];
} elseif (method_exists($t_item, 'getLastLocationForDisplay')) {
    $vs_current_location = $t_item->getLastLocationForDisplay($vs_location_template);
    $vs_current_location = $vs_current_location ? _t('Location: %1', $vs_current_location) : '';
    $vs_full_location_hierarchy = $t_item->getLastLocationForDisplay("^ca_storage_locations.hierarchy.preferred_labels.name%delimiter=_➜_");
}

$va_display_flags = $this->request->config->getAssoc("{$vs_table_name}_inspector_display_flags") ?: array();

$vb_dont_use_labels_for_ca_objects = (bool)$t_item->getAppConfig()->get('ca_objects_dont_use_labels');
$vs_inspector_title_template = $this->request->config->get("{$vs_table_name}_inspector_display_title");
$vs_inspector_below_media_template = $this->request->config->get("{$vs_table_name}_inspector_display_below_media");

$va_inspector_additional_info_templates = $this->request->config->get("{$vs_table_name}_inspector_additional_info");
if ($va_inspector_additional_info_templates && !is_array($va_inspector_additional_info_templates)) {
    $va_inspector_additional_info_templates = array( $va_inspector_additional_info_templates );
}

$vs_record_label = '';
$vb_show_idno = false;

if ($vn_item_id) {
    $vb_dont_use_labels_for_ca_objects = (bool)$t_item->getAppConfig()->get('ca_objects_dont_use_labels');
    if ($vs_table_name !== 'ca_objects' || !$vb_dont_use_labels_for_ca_objects) {
        if ($vs_inspector_title_template) {
            $vs_record_label = caProcessTemplateForIDs($vs_inspector_title_template, $vs_table_name, array($vn_item_id));
        } else {
            $va_object_collection_collection_ancestors = $this->getVar('object_collection_collection_ancestors');
            if (($vs_table_name === 'ca_objects') && $t_item->getAppConfig()->get('ca_objects_x_collections_hierarchy_enabled') && is_array($va_object_collection_collection_ancestors) && sizeof($va_object_collection_collection_ancestors)) {
                $va_collection_links = array();
                foreach ($va_object_collection_collection_ancestors as $va_collection_ancestor) {
                    $va_collection_links[] = caEditorLink($this->request, $va_collection_ancestor['label'], '', 'ca_collections', $va_collection_ancestor['collection_id']);
                }
                $vs_record_label .= join(" / ", $va_collection_links).' &gt; ';
            }

            if (method_exists($t_item, 'getLabelForDisplay') && ($t_item->getLabelTableInstance())) {
                $vn_parent_index = (sizeof($va_ancestors) - 1);
                if ($vn_parent_id && (($vs_table_name != 'ca_places') || ($vn_parent_index > 0))) {
                    $va_parent = $va_ancestors[$vn_parent_index];
                    $vs_disp_fld = $t_item->getLabelDisplayField();

                    $vs_editor_link = caEditorLink($this->request, $va_parent['NODE'][$vs_disp_fld], '', $vs_table_name, $va_parent['NODE'][$t_item->primaryKey()]);
                    if ($va_parent['NODE'][$vs_disp_fld] && $vs_editor_link) {
                        $vs_record_label .= $vs_editor_link . ' &gt; ' . $t_item->getLabelForDisplay();
                    } else {
                        $vs_record_label .= ($va_parent['NODE'][$vs_disp_fld] ? $va_parent['NODE'][$vs_disp_fld] . ' &gt; ' : '') . $t_item->getLabelForDisplay();
                    }
                } else {
                    $vs_record_label .= $t_item->getLabelForDisplay();
                    if (($vs_table_name === 'ca_editor_uis') && (in_array($this->request->getAction(), array('EditScreen', 'DeleteScreen', 'SaveScreen')))) {
                        $vs_record_label .= ' &gt; ' . ($t_screen->getLabelForDisplay() ?: _t('new screen'));
                    }
                }
            } else {
                $vs_record_label .= $t_item->hasField('name') ? $t_item->get('name') : $t_item->get(array_shift($t_item->getProperty('LIST_FIELDS')));
            }
        }
    }

    $vb_show_idno = $vs_idno && !$this->request->config->get("{$vs_table_name}_inspector_dont_display_idno");

    if (!$vs_record_label) {
        if (($vs_table_name === 'ca_objects') && $vb_dont_use_labels_for_ca_objects) {
            $vs_record_label = $vs_idno;
            $vb_show_idno = false;
        } else {
            $vs_record_label =  '['._t('BLANK').']';
        }
    }
}

$vn_num_objects = (($vs_table_name === 'ca_object_lots') && $vn_item_id) ? $t_item->numObjects(null, ['excludeChildObjects' => $this->request->config->get("exclude_child_objects_in_inspector_log_count")]) : -1;
$vn_num_components = (($vs_table_name === 'ca_object_lots') && $vn_item_id) ? $t_item->numObjects(null, array('return' => 'components')) : -1;

$vs_parent_name = '';
$vn_parent_id = $this->request->getParameter('parent_id', pInteger);
if ($vn_parent_id) {
    $t_parent = clone $t_item;
    $t_parent->load($vn_parent_id);
    $vs_parent_name = $t_parent->getLabelForDisplay();
    $t_rel_type = new ca_relationship_types($vn_parent_id);
}

$vb_watchable_type = in_array($vs_table_name, array( 'ca_objects', 'ca_object_lots', 'ca_entities', 'ca_places', 'ca_occurrences', 'ca_collections', 'ca_storage_locations' ));
$vb_watched = $vb_watchable_type && $t_watch_list->isItemWatched($vn_item_id, $t_item->tableNum(), $this->request->user->get("user_id"));

$va_restrict_add_child_control_to_types = $this->request->config->getList($vs_table_name.'_restrict_child_control_in_inspector_to_types');
$vb_show_add_child_control = $this->request->config->get($vs_table_name.'_show_add_child_control_in_inspector') &&
    (!is_array($va_restrict_add_child_control_to_types) || empty($va_restrict_add_child_control_to_types)) &&
    $t_type_instance && !in_array($t_type_instance->get('idno'), $va_restrict_add_child_control_to_types) &&
    !in_array($t_type_instance->getPrimaryKey(), $va_restrict_add_child_control_to_types);

$vb_enforce_strict_hierarchy = (bool)$this->request->config->get($vs_table_name.'_enforce_strict_type_hierarchy');
$vs_type_list = $vb_enforce_strict_hierarchy ?
    $t_item->getTypeListAsHTMLFormElement('type_id', null, array('childrenOfCurrentTypeOnly' => true, 'directChildrenOnly' => ($vb_enforce_strict_hierarchy !== '~'), 'returnHierarchyLevels' => true, 'access' => __CA_BUNDLE_ACCESS_EDIT__)) :
    $t_item->getTypeListAsHTMLFormElement('type_id', null, array('access' => __CA_BUNDLE_ACCESS_EDIT__));

$va_sets = caExtractValuesByUserLocale($t_set->getSetsForItem($t_item->tableNum(), $vn_item_id, array('user_id' => $this->request->getUserID(), 'access' => __CA_SET_READ_ACCESS__)));
$vs_select = $this->getVar('available_mappings_as_html_select');

$vb_is_currently_part_of_lot = (bool)$t_item->get('lot_id');
$vn_lot_id = $t_item->get('lot_id') ?: $this->request->getParameter('lot_id', pInteger);
$va_lot_lots = caGetTypeListForUser('ca_object_lots', array('access' => __CA_BUNDLE_ACCESS_READONLY__));
$t_lot = new ca_object_lots($vn_lot_id);
$vs_lot_displayname = $t_lot->get('idno_stub') ?: $t_lot->getLabelForDisplay() ?: "Lot {$vn_lot_id}";
$vs_part_of_lot_msg = $this->request->config->get("ca_objects_inspector_part_of_lot_msg") ?: _t('Part of lot');
$vs_will_be_part_of_lot_msg = $this->request->config->get("ca_objects_inspector_will_be_part_of_lot_msg") ?: _t('Will be part of lot');

$va_object_container_types = $this->request->config->getList('ca_objects_container_types');
$va_object_component_types = $this->request->config->getList('ca_objects_component_types');
$vb_can_add_component = (($vs_table_name === 'ca_objects') && $vn_item_id && ($this->request->user->canDoAction('can_create_ca_objects')) && $t_item->canTakeComponents());
$vn_component_count = method_exists($t_item, 'getComponentCount') ? $t_item->getComponentCount() : 0;
$vs_component_list_screen = $t_ui ? $t_ui->getScreenWithBundle("ca_objects_components_list", $this->request) : null;

$va_nonconforming_objects = method_exists($t_item, 'getObjectsWithNonConformingIdnos') ? $t_item->getObjectsWithNonConformingIdnos() : array();
$va_show_counts_for = $this->request->config->getList($t_item->tableName().'_show_related_counts_in_inspector_for');
$vn_representation_id = $t_item->get('representation_id');
$t_representation = $vn_representation_id ? new ca_object_representations($vn_representation_id) : null;
$vn_set_item_count = method_exists($t_item, 'getItemCount') ? $t_item->getItemCount(array('user_id' => $this->request->getUserID())) : 0;

AssetLoadManager::register("panel");

TooltipManager::add(".prev.record", "Previous");
TooltipManager::add(".next.record", "Next");
TooltipManager::add(".inspector-deaccessioned", $t_item->get('deaccession_notes') ?: '<span class="text-muted">No deaccession notes recorded.</span>');
TooltipManager::add(".inspector-current-location", $vs_full_location_hierarchy);
TooltipManager::add(".left-scroll", _t('Previous Image'));
TooltipManager::add(".right-scroll", _t('Next Image'));
TooltipManager::add("#caWatchItemButton", _t('Watch/Unwatch this record'));
TooltipManager::add("#inspectorChangeType", _t('Change Record Type'));
TooltipManager::add("#inspectorCreateChildButton", _t('Create a child record under this one'));
TooltipManager::add("#caDuplicateItemButton", _t('Duplicate this %1', mb_strtolower($vs_type_name, 'UTF-8')));
TooltipManager::add('#inspectorLotMediaDownloadButton', _t("Download all media associated with objects in this lot"));
TooltipManager::add('#inspectorSetMediaDownloadButton', _t("Download all media associated with records in this set"));
TooltipManager::add("#inspectorMoreInfo", _t('See more information about this record'));
TooltipManager::add("#caInspectorCreationDate", '<h2>' . _t('Created on') . '</h2>' . _t('Created on %1', caGetLocalizedDate($va_creation['timestamp'], array('dateFormat' => 'delimited'))));
TooltipManager::add("#caInspectorChangeDate", '<h2>' . _t('Last changed on') . '</h2>' . _t('Last changed on %1', caGetLocalizedDate($va_last_change['timestamp'], array('dateFormat' => 'delimited'))));
TooltipManager::add("#caInspectorViolationsList", '<h2>' . $vs_num_violations_display . '</h2><ol>' . join("\n", $va_violation_messages ?: array()) . '</ol>');
TooltipManager::add(".editorBatchSetEditorLink", _t('Batch Edit'));

$vb_can_change_type = $this->request->user->canDoAction("can_change_type_{$vs_table_name}");
if ($vb_can_change_type) {
    $vo_change_type_view = new View($this->request, $this->request->getViewsDirectoryPath() . "/bundles/");
    $vo_change_type_view->setVar('t_item', $t_item);
    FooterManager::add($vo_change_type_view->render("change_type_html.php"));
}

if ($vn_item_id && $vb_show_add_child_control && $vs_type_list) {
    $vo_create_child_view = new View($this->request, $this->request->getViewsDirectoryPath() . "/bundles/");
    $vo_create_child_view->setVar('t_item', $t_item);
    $vo_create_child_view->setVar('type_list', $vs_type_list);
    FooterManager::add($vo_create_child_view->render("create_child_html.php"));
}

if ($vb_can_add_component) {
    FooterManager::add($this->render("create_component_html.php"));
}
?>
<div class="component component-editor-info">
    <?php if (($this->request->getAction() === 'Delete') && ($this->request->getParameter('confirm', pInteger))): ?>
        <div class="info-box" style="<?php print ($vs_color ? "border-color: #$vs_color;" : ''); ?>">
            <?php print $vs_icon; ?>
            <h2><?php print _t('Deleted $1', $vs_type_name); ?></h2>
        </div>
    <?php else: ?>
        <?php if ($vn_item_id): ?>
            <div class="info-box" style="<?php print ($vs_color ? "border-color: #$vs_color;" : ''); ?>">
                <?php print $vs_icon; ?>
                <?php if (!$this->request->config->get("{$vs_priv_table_name}_inspector_disable_headline")): ?>
                    <h2><?php print _t($vs_description, $vs_type_name); ?></h2>
                <?php endif; ?>

                <?php if ($t_item->hasField('is_deaccessioned') && $t_item->get('is_deaccessioned') && ($t_item->get('deaccession_date', array('getDirectDate' => true)) <= caDateToHistoricTimestamp(_t('now')))): ?>
                    <p class="deaccessioned-notes"><?php print _t('Deaccessioned %1', $t_item->get('deaccession_date')); ?></p>
                <?php elseif ($this->request->user->canDoAction('can_see_current_location_in_inspector_ca_objects') && $vs_current_location): ?>
                    <label><?php print $vs_inspector_current_location_label; ?></label>
                    <div class="inspector-current-location">
                        <?php print $vs_current_location; ?>
                    </div>
                <?php endif; ?>

                <?php foreach($va_display_flags as $vs_exp => $vs_display_flag): ?>
                    <?php
                    $va_exp_vars = ExpressionParser::getVariableList($vs_exp);
                    $va_exp_var_values = array_combine($va_exp_vars, array_map(function ($vs_var_name) use ($t_item) { return $t_item->get($vs_var_name, array('convertCodesToIdno' => true)); }, $va_exp_vars));
                    ?>
                    <?php if (ExpressionParser::evaluate($vs_exp, $va_exp_var_values)): ?>
                        <span class="flag label label-default">
                            <?php print $t_item->getWithTemplate("{$vs_display_flag}"); ?>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="<?php print $vs_table_name; ?>">
                    <?php print $vs_record_label; ?>
                    <?php if ($vb_show_idno && $vs_idno): ?>
                        <span title="$vs_idno"><?php print $vs_idno; ?></span>
                    <?php endif; ?>
                </div>

                <?php if (sizeof($va_representations) > 0): ?>
                    <div id="image-slideshow-viewer" class="clearfix">
                        <?php if (sizeof($va_representations) > 1): ?>
                            <a href="#" class="scroll pull-left" onclick="inspectorInfoRepScroller.scrollToPreviousImage(); return false;">
                                <span class="glyphicon glyphicon-menu-left"></span>
                            </a>
                        <?php endif; ?>
                        <?php if (sizeof($va_representations) > 1): ?>
                            <a href="#" class="scroll pull-right" onclick="inspectorInfoRepScroller.scrollToNextImage(); return false;">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </a>
                        <?php endif; ?>
                        <div id="image-slideshow-container">
                            <div id="image-slideshow"></div>
                        </div>
                        <div id="image-slideshow-count"></div>
                    </div>
                    <?php if ($vs_inspector_below_media_template): ?>
                        <?php print caProcessTemplateForIDs($vs_inspector_below_media_template, $vs_table_name, array($vn_item_id)); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($vs_table_name === 'ca_object_lots' && $vn_item_id): ?>
                    <div>
                        <?php print ($vn_num_objects === 1 ? _t('Lot contains %1 object', $vn_num_objects) : _t('Lot contains %1 objects', $vn_num_objects)); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->request->config->get("include_custom_inspector") && file_exists($this->request->getViewsDirectoryPath()."/bundles/inspector_info.php")): ?>
                    <?php print $this->render('inspector_info.php'); ?>
                <?php endif; ?>
            </div>

            <?php print caFormTag($this->request, 'Edit', 'DuplicateItemForm', $this->request->getModulePath().'/'.$this->request->getController(), 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true)); ?>
                <div class="btn-group inspector-controls">
                    <?php if ($vb_watchable_type): ?>
                        <a href="#" title="<?php print _t('Add/remove item to/from watch list.'); ?>" onclick="caToggleItemWatch(); return false;" id="caWatchItemButton" class="btn btn-default">
                            <span class="glyphicon glyphicon-eye-open <?php print ($vb_watched ? '' : 'text-muted'); ?> "></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->request->user->canDoAction("can_change_type_{$vs_table_name}")): ?>
                        <a href="#" onclick="caTypeChangePanel.showPanel(); return false;" id="inspectorChangeType" class="btn btn-default">
                            <span class="glyphicon glyphicon-edit" title="<?php print _t('Change type'); ?>"></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($vn_item_id && $vb_show_add_child_control && $vs_type_list): ?>
                        <a href="#" onclick="caCreateChildPanel.showPanel(); return false;" id="inspectorCreateChildButton" class="btn btn-default">
                            <span class="glyphicon glyphicon-hand-down" title="<?php print _t('Create Child Record'); ?>"></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->request->user->canDoAction('can_duplicate_'.$vs_table_name) && $vn_item_id): ?>
                        <button id="caDuplicateItemButton" class="btn btn-default">
                            <span class="glyphicon glyphicon-duplicate"></span>
                        </button>
                        <?php print caHTMLHiddenInput($t_item->primaryKey(), array('value' => $vn_item_id)); ?>
                        <?php print caHTMLHiddenInput('mode', array('value' => 'dupe')); ?>
                    <?php endif; ?>

                    <?php if ($vn_num_objects > 0): ?>
                        <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getLotMedia', array('lot_id' => $vn_item_id, 'download' => 1), array()); ?>" id="inspectorLotMediaDownloadButton" class="btn btn-default">
                            <span class="glyphicon glyphicon-download"></span>
                        </a>
                    <?php endif; ?>

                    <?php if(($vs_table_name === 'ca_sets') && (sizeof($t_item->getItemRowIDs())>0)): ?>
                        <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getSetMedia', array('set_id' => $vn_item_id, 'download' => 1), array()); ?>" id="inspectorSetMediaDownloadButton" class="btn btn-default">
                            <span class="glyphicon glyphicon-download"></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($vn_item_id): ?>
                        <button type="button" id="inspectorMoreInfo" class="btn btn-default">
                            <span class="glyphicon glyphicon-info-sign"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        <?php elseif ($vs_parent_name): ?>
            <div>
                <?php print _t("Creating new %1", $vs_type_name); ?>
                <?php if ($vs_parent_name): ?>
                    <?php print _t("%1 &gt; New %2", $vs_parent_name, $vs_type_name); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($vn_item_id): ?>
            <?php if (is_array($va_inspector_additional_info_templates)): ?>
                <?php foreach ($va_inspector_additional_info_templates as $vs_info): ?>
                    <div>
                        <?php print caProcessTemplateForIDs($vs_info, $vs_table_name, array( $vn_item_id ), array( 'requireLinkTags' => true )); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div id="inspectorInfo">
                <?php if (is_array($va_sets) && sizeof($va_sets)): ?>
                    <div>
                        <strong><?php print ((sizeof($va_sets) == 1) ? _t("In set") : _t("In sets")); ?></strong>
                        <?php foreach ($va_sets as $vn_set_id => $va_set): ?>
                            <a href="<?php print caEditorUrl($this->request, 'ca_sets', $vn_set_id); ?>"><?php print $va_set['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($vn_item_id && $vs_select): ?>
                    <div class="inspectorExportControls">
                        <?php print caFormTag($this->request, 'exportItem', 'caExportForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                            <?php print $vs_select; ?>
                            <?php print caHTMLHiddenInput($t_item->primaryKey(), array('value' => $vn_item_id)); ?>
                            <?php print caHTMLHiddenInput('download', array('value' => 1)); ?>
                            <?php print caFormSubmitLink($this->request, 'Export &rsaquo;', 'button', 'caExportForm'); ?>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if ($va_creation['timestamp'] || $va_last_change['timestamp']): ?>
                    <table class="table table-bordered table-condensed small">
                        <thead>
                            <tr>
                                <th>What</th>
                                <th>Who</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($va_creation['timestamp']): ?>
                                <tr>
                                    <td><?php print _t('Created'); ?></td>
                                    <td><?php print ($vs_creation_name ?: '?'); ?></td>
                                    <td id="caInspectorCreationDate"><?php print $vs_creation_interval; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($va_last_change['timestamp'] && ($va_creation['timestamp'] !== $va_last_change['timestamp'])): ?>
                                <tr>
                                    <td><?php print _t('Last changed'); ?></td>
                                    <td><?php print ($vs_last_change_name ?: '?'); ?></td>
                                    <td id="caInspectorChangeDate"><?php print $vs_last_change_interval; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if ($vn_num_violations > 0): ?>
                    <div id="caInspectorViolationsList">
                        <span class="glyphicon glyphicon-warning-sign"></span>
                        <?php print $vs_num_violations_display; ?>
                    </div>
                <?php endif; ?>

                <?php if ($vs_get_spec = $this->request->config->get("{$vs_table_name}_inspector_display_more_info")): ?>
                    <?php print caProcessTemplateForIDs($vs_get_spec, $vs_table_name, array($vn_item_id)); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$vn_item_id && $vs_rel_table && $this->request->datamodel->tableExists($vs_rel_table) && $vn_rel_type_id && $vn_rel_id && $t_rel && $t_rel->load($vn_rel_id)): ?>
            <?php _t("Will be related to %1", $t_rel->getTypeName()); ?>:
            <?php $t_rel->getLabelForDisplay(); ?>
        <?php endif; ?>

        <?php // Type-specific details ?>

        <?php if (($vs_table_name === 'ca_objects') && $vn_lot_id && ($t_lot->get('deleted') === 0) && in_array($t_lot->get('type_id'), $va_lot_lots) && $vs_lot_displayname): ?>
            <div>
                <strong><?php print ($vb_is_currently_part_of_lot ? $vs_part_of_lot_msg : $vs_will_be_part_of_lot_msg); ?></strong>:
                <?php print caNavLink($this->request, $vs_lot_displayname, '', 'editor/object_lots', 'ObjectLotEditor', 'Edit', array('lot_id' => $vn_lot_id)); ?>
            </div>
        <?php endif; ?>

        <?php if (method_exists($t_item, 'getComponentCount') && $vn_component_count): ?>
            <div>
                <strong><?php print _t('Has'); ?></strong>
                <?php if ($t_ui && $vs_component_list_screen && ($vs_component_list_screen !== $this->request->getActionExtra())): ?>
                    <?php print caNavLink($this->request, (($vn_component_count == 1) ? _t('%1 component', $vn_component_count) : _t('%1 components', $vn_component_count)), '', '*', '*', $this->request->getAction().'/'.$vs_component_list_screen, array($t_item->primaryKey() => $vn_item_id)); ?>
                <?php else: ?>
                    <?php print ($vn_component_count === 1 ? _t('%1 component', $vn_component_count) : _t('%1 components', $vn_component_count)); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($vb_can_add_component): ?>
            <a href="#" onclick="caObjectComponentPanel.showPanel('<?php print caNavUrl($this->request, '*', 'ObjectComponent', 'Form', array('parent_id' => $vn_item_id)); ?>'); return false;">
                <span class="caret"></span>
            </a>
        <?php endif; ?>

        <?php if (($vs_table_name === 'ca_object_lots') && $vn_item_id): ?>
            <?php if (is_array($va_object_component_types) && sizeof($va_object_component_types)): ?>
                <div>
                    <?php print ($vn_num_objects === 1 ? _t('Lot contains %1 object', $vn_num_objects) : _t('Lot contains %1 objects', $vn_num_objects)); ?>
                </div>
                <div>
                    <?php print ($vn_num_components == 1 ? _t('Lot contains %1 component', $vn_num_components) : _t('Lot contains %1 components', $vn_num_components)); ?>
                </div>
            <?php else: ?>
                <div>
                    <?php print ($vn_num_objects === 1 ? _t('Lot contains %1 object', $vn_num_objects) : _t('Lot contains %1 objects', $vn_num_objects)); ?>
                </div>
            <?php endif; ?>

            <?php if (((bool)$this->request->config->get('allow_automated_renumbering_of_objects_in_a_lot')) && sizeof($va_nonconforming_objects) > 0): ?>
                <div>
                    <div>
                        <span class="label label-warning">
                            <?php print ((sizeof($va_nonconforming_objects) === 1) ? _t('There is %1 object with non-conforming numbering', sizeof($va_nonconforming_objects)) : _t('There are %1 objects with non-conforming numbering', sizeof($va_nonconforming_objects))); ?>
                        </span>
                        <a href="#" onclick="jQuery('#inspectorNonConformingNumberList').toggle(250); return false;">
                            <span class="caret"></span>
                        </a>
                    </div>
                    <div class="hidden">
                        <ol>
                            <?php foreach($va_nonconforming_objects as $vn_object_id => $va_object_info): ?>
                                <li>
                                    <?php caEditorLink($this->request, $va_object_info['idno'], '', 'ca_objects', $vn_object_id); ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <a href="<?php caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'renumberObjects', array('lot_id' => $vn_item_id)); ?>" class="btn btn-default">
                        <?php print _t('Re-number objects'); ?>
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!(bool)$this->request->config->get('disable_add_object_to_lot_inspector_controls')): ?>
                <div>
                    <form action="#" id="caAddObjectToLotForm">
                        <?php if ((bool)$this->request->config->get('ca_objects_enforce_strict_type_hierarchy')): ?>
                            <?php print _t('Add new %1 to lot', $t_object->getTypeListAsHTMLFormElement('type_id', array('id' => 'caAddObjectToLotForm_type_id'), array('childrenOfCurrentTypeOnly' => true, 'directChildrenOnly' => ($this->request->config->get('ca_objects_enforce_strict_type_hierarchy') == '~') ? false : true, 'returnHierarchyLevels' => true, 'access' => __CA_BUNDLE_ACCESS_EDIT__))); ?>
                        <?php else: ?>
                            <?php print _t('Add new %1 to lot', $t_object->getTypeListAsHTMLFormElement('type_id', array('id' => 'caAddObjectToLotForm_type_id'), array('access' => __CA_BUNDLE_ACCESS_EDIT__))); ?>
                        <?php endif; ?>
                        <a href="#" onclick="caAddObjectToLotForm();">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (is_array($va_show_counts_for) && sizeof($va_show_counts_for)): ?>
            <?php foreach ($va_show_counts_for as $vs_rel_table): ?>
                <?php if (($vn_count = (int)$t_item->getRelatedItems($vs_rel_table, ['returnAs' => 'count'])) > 0): ?>
                    <div>
                        <?php print caSearchLink($this->request, _t('%1 related %2', $vn_count, $o_dm->getTableProperty($vs_rel_table, ($vn_count === 1) ? 'NAME_SINGULAR' : 'NAME_PLURAL')), '', $vs_rel_table, $t_item->primaryKey(true).":".$vn_item_id); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_object_representations'): ?>
            <?php foreach (array('ca_objects', 'ca_object_lots', 'ca_entities', 'ca_places', 'ca_occurrences', 'ca_collections', 'ca_storage_locations', 'ca_loans', 'ca_movements') as $vs_rel_table): ?>
                <?php if (sizeof($va_objects = $t_item->getRelatedItems($vs_rel_table))): ?>
                    <div>
                        <label><?php print _t("Related %1", $o_dm->getTableProperty($vs_rel_table, 'NAME_PLURAL')); ?></label>
                        <ul>
                            <?php foreach ($va_objects as $vn_rel_id => $va_rel_info): ?>
                                <?php if ($vs_label = array_shift($va_rel_info['labels'])): ?>
                                    <li>
                                        <a href="<?php print caEditorUrl($this->request, $vs_rel_table, $va_rel_info[$o_dm->getTablePrimaryKeyName($vs_rel_table)], array(), array(), array('action' => 'Edit'.$vs_rel_table_ui_screen_url_suffix)); ?>">
                                            <?php print $vs_label; ?>
                                            (<?php print $va_rel_info['idno']; ?>)
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_representation_annotations' && $vn_representation_id): ?>
            <div>
                <label><?php print _t('Applied to representation'); ?></label>
                <a href="<?php caNavUrl($this->request, 'editor/object_representations', 'ObjectRepresentationEditor', 'Edit/' . $this->getVar('representation_editor_screen'), array('representation_id' => $vn_representation_id)); ?>">
                    <?php print $t_representation->getLabelForDisplay(); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_sets'): ?>
            <?php if (($vn_set_item_count > 0) && ($this->request->user->canDoAction('can_batch_edit_'.$vs_table_name))): ?>
                <a href="<?php print caNavUrl($this->request, 'batch', 'Editor', 'Edit', array('set_id' => $vn_item_id)); ?>" class="btn btn-default editorBatchSetEditorLink">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>
            <?php endif; ?>
            <div>
                <label><?php print _t('Number of items'); ?></label>
                <?php print $vn_set_item_count; ?>
            </div>
            <?php if ($vn_item_id || $vn_set_table_num): ?>
                <div>
                    <label><?php print _t('Type of content'); ?></label>
                    <?php print caGetTableDisplayName($vn_set_table_num); ?>
                </div>
            <?php endif; ?>
            <?php if ($vn_item_id && !(bool)$this->request->config->get('ca_sets_disable_duplication_of_items') && $this->request->user->canDoAction('can_duplicate_items_in_sets') && $this->request->user->canDoAction('can_duplicate_' . $vs_set_table_name)): ?>
                <?php print caFormTag($this->request, 'DuplicateItems', 'caDupeSetItemsForm', 'manage/sets/SetEditor', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                    <?php print _t("Duplicate items in this set and add to"); ?>
                    <?php print caHTMLSelect('setForDupes', array( _t('current set') => 'current', _t('new set') => 'new' )); ?>
                    <?php print caHTMLHiddenInput('set_id', array('value' => $vn_item_id)); ?>
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-duplicate"></span>
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($t_user->getPrimaryKey()): ?>
                <div>
                    <label><?php print _t('Owner'); ?></label>
                    <?php print $t_user->get('fname'); ?> <?php print $t_user->get('lname'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->request->user->canDoAction('can_export_'.$vs_set_table_name) && $vn_item_id && (sizeof(ca_data_exporters::getExporters($vn_set_table_num))>0)): ?>
                <div>
                    <label><?php print _t('Export this set of records'); ?></label>
                    <a href="#" class="btn btn-default" onclick="jQuery('#exporterFormList').show();">
                        <span class="glyphicon glyphicon-export"></span>
                    </a>
                    <?php print caFormTag($this->request, 'ExportData', 'caExportForm', 'manage/MetadataExport', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                        <div id="exporterFormList">
                            <?php print ca_data_exporters::getExporterListAsHTMLFormElement('exporter_id', $vn_set_table_num, array('id' => 'caExporterList'),array('width' => '135px')); ?>
                            <?php print caHTMLHiddenInput('set_id', array('value' => $vn_item_id)); ?>
                            <button class="btn btn-default">
                                <span class="glyphicon glyphicon-export"></span>
                                <?php print _t('Export'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_set_items' && $t_set->load($t_item->get('set_id'))): ?>
            <div>
                <label><?php print _t("Part of set"); ?></label>
                <a href="<?php print caEditorLink($this->request, 'ca_sets', $t_item->get('set_id')); ?>">
                    <?php print $t_set->getLabelForDisplay(); ?>
                </a>
                <?php if ($t_content_instance->load($vn_row_id)): ?>
                    <div>
                        <label><?php print _t("Is %1", caGetTableDisplayName($t_item->get('table_num'), false)); ?></label>
                        <a href="<?php print caEditorUrl($this->request, $t_item->get('table_num'), $vn_row_id); ?>">
                            <?php print $t_content_instance->getLabelForDisplay(); ?>
                            <?php if ($t_content_instance->getProperty('ID_NUMBERING_ID_FIELD')): ?>
                                (<?php print $t_content_instance->get($t_content_instance->getProperty('ID_NUMBERING_ID_FIELD')); ?>)
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (($vs_table_name === 'ca_lists') && $vn_item_id): ?>
            <label><?php print _t("Number of items"); ?></label>
            <?php print $t_item->numItemsInList(); ?>
            <?php $t_list_item->load(array('list_id' => $vn_item_id, 'parent_id' => null)); ?>
            <?php $vs_type_list = $t_list_item->getTypeListAsHTMLFormElement('type_id', array(), array('access' => __CA_BUNDLE_ACCESS_EDIT__)); ?>
            <?php if ($vs_type_list): ?>
                <?php print caFormTag($this->request, 'Edit', 'NewChildForm', 'administrate/setup/list_item_editor/ListItemEditor', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                    <?php print caHTMLHiddenInput($t_list_item->primaryKey(), array('value' => '0')); ?>
                    <?php print caHTMLHiddenInput('parent_id', array('value' => $t_list_item->getPrimaryKey())); ?>
                    <?php print _t('Add a %1 to this list', $vs_type_list); ?>
                    <button class="add">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_list_items' && $t_list): ?>
            <div>
                <label><?php print _t("Part of"); ?></label>
                <a href="<?php print caEditorUrl($this->request, 'ca_lists', $vn_list_id); ?>">
                    <?php print $t_list->getLabelForDisplay(); ?>
                </a>
            </div>
            <?php if ($t_item->get('is_default')): ?>
                <div>
                    <?php print _t("Is default for list"); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_relationship_types' && $t_rel_instance): ?>
            <div>
                <label><?php print _t("Is a"); ?></label>
                <?php print $t_rel_instance->getProperty('NAME_SINGULAR'); ?>
            </div>
        <?php endif; ?>

        <?php if (($vs_table_name === 'ca_metadata_elements') && $vn_item_id): ?>
            <div>
                <label><?php print _t("Element code"); ?></label>
                <?php print $t_item->get('element_code'); ?>
            </div>
            <?php if (sizeof($va_uis) > 0): ?>
                <div>
                    <label><?php print _t("Referenced by user interfaces"); ?></label>
                    <ul>
                        <?php foreach($va_uis as $vn_ui_id => $va_ui_info): ?>
                            <a href="<?php print caNavUrl($this->request, 'administrate/setup/interface_screen_editor', 'InterfaceScreenEditor', 'Edit', array('ui_id' => $vn_ui_id, 'screen_id' => $va_ui_info['screen_id'])); ?>">
                                <?php print $va_ui_info['name']; ?>
                            </a>
                            (<?php print $o_dm->getTableProperty($va_ui_info['editor_type'], 'NAME_PLURAL'); ?>)
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_editor_uis'): ?>
            <div>
                <label><?php print _t("Number of screens"); ?></label>
                <?php print $t_item->getScreenCount(); ?>
                <label><?php print _t("Edits"); ?></label>
                <?php print caGetTableDisplayName($vn_item_id ? $t_item->get('editor_type') : $this->request->getParameter('editor_type', pInteger)); ?>
            </div>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_editor_ui_screens'): ?>
            <div>
                <label><?php print _t("Part of"); ?></label>
                <a href="<?php print caNavUrl($this->request, 'administrate/setup/interface_editor', 'InterfaceEditor', 'Edit', array('ui_id' => $t_item->get('ui_id'))); ?>">
                    <?php print (new ca_editor_uis($t_item->get('ui_id')))->getLabelForDisplay(); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_bundle_displays'): ?>
            <div>
                <label><?php print _t("Number of placements"); ?></label>
                <?php print $t_item->getPlacementCount(array('user_id' => $this->request->getUserID())); ?>
                <?php if ($vn_item_id): ?>
                    <label><?php _t("Type of content"); ?></label>
                    <?php caGetTableDisplayName($t_item->get('table_num')); ?>
                <?php elseif ($this->request->getParameter('table_num', pInteger)): ?>
                    <div>
                        <strong><?php print _t("Type of content"); ?></strong>
                        <?php print caGetTableDisplayName($this->request->getParameter('table_num', pInteger)); ?>
                    </div>
                <?php endif; ?>
                <?php if ($t_user->getPrimaryKey()): ?>
                    <div>
                        <label><?php print _t('Owner'); ?></label>
                        <?php print $t_user->get('fname'); ?> <?php print $t_user->get('lname'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_search_forms'): ?>
            <div>
                <label><?php print _t("Number of placements"); ?></label>
                <?php print $t_item->getPlacementCount(array('user_id' => $this->request->getUserID())); ?>
                <?php if ($vn_item_id): ?>
                    <label><?php print _t("Searches for"); ?></label>
                    <?php print caGetTableDisplayName($t_item->get('table_num')); ?>
                <?php elseif ($this->request->getParameter('table_num', pInteger)): ?>
                    <label><?php print _t("Searches for"); ?></label>
                    <?php print caGetTableDisplayName($this->request->getParameter('table_num', pInteger)); ?>
                <?php endif; ?>
            </div>
            <?php if ($t_user->getPrimaryKey()): ?>
                <div>
                    <label><?php print _t('Owner'); ?></label>
                    <?php print $t_user->get('fname'); ?> <?php print $t_user->get('lname'); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (($vs_table_name === 'ca_tours') && $vn_item_id): ?>
            <div>
                <label><?php print _t("Number of stops"); ?></label>
                <?php print $t_item->getStopCount(); ?>
            </div>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_tour_stops'): ?>
            <label><?php print _t("Part of"); ?></label>
            <a href="<?php print caEditorUrl($this->request, 'ca_tours', $t_item->get('tour_id')); ?>">
                <?php print (new ca_tours($t_item->get('tour_id')))->getLabelForDisplay(); ?>
            </a>
        <?php endif; ?>

        <?php if ($vs_table_name === 'ca_bundle_mappings'): ?>
            <?php if ($vn_item_id): ?>
                <div>
                    <label><?php print _t("Type of content"); ?></label>
                    <?php print caGetTableDisplayName($t_item->get('table_num')); ?>
                </div>
                <div>
                    <label><?php print _t("Type"); ?></label>
                    <?php print $t_item->getChoiceListValue('direction', $t_item->get('direction')); ?>
                </div>
                <div>
                    <label><?php print _t("Target format"); ?></label>
                    <?php print $t_item->get('target'); ?>
                </div>
                <div>
                    <strong><?php print _t("Number of groups"); ?></strong>
                    <?php print $va_stats['groupCount']; ?>
                </div>
                <div>
                    <label><?php print _t("Number of rules"); ?></label>
                    <?php print $va_stats['ruleCount']; ?>
                </div>
            <?php elseif ($this->request->getParameter('table_num', pInteger)): ?>
                <div>
                    <label><?php print _t("Type of content"); ?></label>
                    <?php print caGetTableDisplayName($this->request->getParameter('table_num', pInteger)); ?>
                </div>
                <div>
                    <label><?php print _t("Type"); ?></label>
                    <?php print $t_item->getChoiceListValue('direction', $this->request->getParameter('direction', pString)); ?>
                </div>
                <div>
                    <label><?php print _t("Target format"); ?></label>
                    <?php print $this->request->getParameter('target', pString); ?>
                </div>
                <div>
                    <strong><?php print _t("Number of groups"); ?></strong>
                    0
                </div>
                <div>
                    <label><?php print _t("Number of rules"); ?></label>
                    0
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php // Export ?>

        <?php if ($this->request->user->canDoAction('can_export_'.$vs_table_name) && $vn_item_id && (sizeof(ca_data_exporters::getExporters($t_item->tableNum()))>0)): ?>
            <div>
                <?php print _t('Export this %1', mb_strtolower($vs_type_name, 'UTF-8')); ?>
                <button type="button" class="btn btn-default" onclick="jQuery('#exporterFormList').show();">
                    <span class="glyphicon glyphicon-export"></span>
                </button>
                <?php print caFormTag($this->request, 'ExportSingleData', 'caExportForm', 'manage/MetadataExport', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                    <div id="exporterFormList">
                        <?php print ca_data_exporters::getExporterListAsHTMLFormElement('exporter_id', $t_item->tableNum(), array('id' => 'caExporterList'), array('width' => '120px', 'recordType' => $t_item->getTypeCode())); ?>
                        <?php print caHTMLHiddenInput('item_id', array('value' => $vn_item_id)); ?>
                        <button>
                            <span class="glyphicon glyphicon-export"></span>
                            <?php print _t('Export'); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php // Results navigation ?>

        <?php if ($vn_item_id || ($this->request->getAction() === 'Delete')): ?>
            <?php if (is_array($va_found_ids) && sizeof($va_found_ids)): ?>
                <div class="btn-group">
                    <?php if ($vn_prev_id > 0): ?>
                        <a href="<?php print $vs_previous_url; ?>" class="btn btn-default prev record">
                            <span class="glyphicon glyphicon-menu-left"></span>
                        </a>
                    <?php else: ?>
                        <span class="btn btn-default disabled prev">
                            <span class="glyphicon glyphicon-menu-left"></span>
                        </span>
                    <?php endif; ?>

                    <?php print ResultContext::getResultsLinkForLastFind($this->request, $vs_table_name, $vs_back_text, 'btn btn-default results-list'); ?>

                    <?php if ($vn_next_id > 0): ?>
                        <a href="<?php print $vs_next_url; ?>" class="btn btn-default next record">
                            <span class="glyphicon glyphicon-menu-right"></span>
                        </a>
                    <?php else: ?>
                        <span class="btn btn-default disabled next">
                            <span class="glyphicon glyphicon-menu-right"></span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php elseif ($vn_item_id): ?>
                <?php print ResultContext::getResultsLinkForLastFind($this->request, $vs_table_name,  $vs_back_text, 'btn btn-default'); ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    var inspectorInfoRepScroller;

    function caToggleItemWatch() {
        var url = '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'toggleWatch', array( $t_item->primaryKey() => $vn_item_id) ); ?>';

        jQuery.getJSON(url, {}, function(data) {
            var $watchButton = jQuery('#caWatchItemButton > span');
            if (data['status'] === 'ok') {
                $watchButton[data['state'] === 'watched' ? 'addClass' : 'removeClass']('text-primary');
                $watchButton[data['state'] !== 'watched' ? 'addClass' : 'removeClass']('text-muted');
            } else if (console && console.log) {
                console.log('Error toggling watch status for item: ' + data['errors']);
            }
        });
    }

    function caAddObjectToLotForm() {
        window.location = '<?php print caEditorUrl($this->request, 'ca_objects', 0, false, array('lot_id' => $vn_item_id, 'rel' => 1, 'type_id' => '')); ?>' + jQuery('#caAddObjectToLotForm_type_id').val();
    }

    (function ($) {
        var inspectorCookieJar = $.cookieJar('caCookieJar');

        $(function () {
            inspectorInfoRepScroller = caUI.initImageScroller(
                <?php print json_encode(array_values(array_map(
                    function ($pa_representation) use ($t_item, $vn_item_id) {
                        $vs_panel_url = caNavUrl($this->request, '*', '*', 'GetMediaOverlay', array($t_item->primaryKey() => $vn_item_id, 'representation_id' => $pa_representation['representation_id']));
                        return array(
                            'url' => $pa_representation['urls']['preview170'],
                            'width' => $pa_representation['info']['preview170']['WIDTH'],
                            'height' => $pa_representation['info']['preview170']['HEIGHT'],
                            'link' => '#',
                            'onclick' => 'caMediaPanel.showPanel(\'' . $vs_panel_url . '\'); return false;'
                        );
                    },
                    $va_representations
                ))); ?>,
                'image-slideshow',
                {
                    containerWidth: 170,
                    containerHeight: 170,
                    noHorizCentering: true,
                    imageCounterID: 'image-slideshow-count',
                    scrollingImageClass: 'preview',
                    initialIndex: <?php print $vn_primary_representation_index ?: 0; ?>
                }
            );

            <?php if ($vn_item_id): ?>
                if (inspectorCookieJar.get('inspectorMoreInfoIsOpen') === undefined) {
                    inspectorCookieJar.set('inspectorMoreInfoIsOpen', 1);
                }

                $('#inspectorInfo').toggle(inspectorCookieJar.get('inspectorMoreInfoIsOpen') === 1);

                $('#inspectorMoreInfo').click(function() {
                    $('#inspectorInfo').slideToggle(350, function() {
                        var visible = this.style.display === 'block';
                        inspectorCookieJar.set('inspectorMoreInfoIsOpen', visible ? 1 : 0);
                    });
                    return false;
                });
            <?php endif; ?>

            $('#objectLotsNonConformingNumberList').hide();
            $('#exporterFormList').hide();
        });
    }(jQuery));
</script>

<?php print ($o_app_plugin_manager->hookAppendToEditorInspector(array( 't_item' => $t_item)) ?: array())['caEditorInspectorAppend']; ?>
