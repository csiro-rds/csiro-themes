<?php
AssetLoadManager::register('hierBrowser');
AssetLoadManager::register('tabUI');

$t_subject = $this->getVar('t_subject');
$pa_ancestors = $this->getVar('ancestors');
$pn_id = $this->getVar('id');
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_lookup_urls = caJSONLookupServiceUrl($this->request, $t_subject->tableName(), array('noInline' => 1));
$vn_items_in_hier = $t_subject->getHierarchySize();
$vs_bundle_preview = '('.$vn_items_in_hier. ') '. caProcessTemplateForIDs("^preferred_labels", $t_subject->tableName(), array($t_subject->getPrimaryKey()));
$pa_bundle_settings = $this->getVar('settings');
$vb_objects_x_collections_hierarchy_enabled = (bool)$t_subject->getAppConfig()->get('ca_objects_x_collections_hierarchy_enabled');

if (in_array($t_subject->tableName(), array('ca_objects', 'ca_collections')) && $vb_objects_x_collections_hierarchy_enabled) {
    $va_lookup_urls = array(
        'search' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'Get'),
        'levelList' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'GetHierarchyLevel'),
        'ancestorList' => caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'GetHierarchyAncestorList')
    );
    $vs_edit_url = caNavUrl($this->request, 'lookup', 'ObjectCollectionHierarchy', 'Edit').'/id/';
    $vn_init_id = $t_subject->tableName()."-".$pn_id;
} else {
    $va_lookup_urls 	= caJSONLookupServiceUrl($this->request, $t_subject->tableName(), array('noInline' => 1));
    $vs_edit_url = caEditorUrl($this->request, $t_subject->tableName());
    $vn_init_id = $pn_id;
}

$va_object_collection_collection_ancestors = $this->getVar('object_collection_collection_ancestors');
$vb_do_objects_x_collections_hierarchy = false;
if ($vb_objects_x_collections_hierarchy_enabled && is_array($va_object_collection_collection_ancestors)) {
    $pa_ancestors = $va_object_collection_collection_ancestors + $this->getVar('ancestors');
    $vb_do_objects_x_collections_hierarchy = true;
}

$vs_item_id = $vb_do_objects_x_collections_hierarchy ? ($va_item['table'].'-'.$va_item['item_id']) : $va_item['item_id'];
?>

<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-hierarchy-navigation">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $va_settings); ?>

    <div class="bundleContainer">
        <div class="hierNav">
            <div class="clearfix">
                <?php if ($pn_id > 0): ?>
                    <div class="pull-right text-right">
                        <?php if ($vn_items_in_hier > 0): ?>
                            <?php print _t("Number of %1 in hierarchy: %2", caGetTableDisplayName($t_subject->tableName(), true), $vn_items_in_hier); ?>
                        <?php endif; ?>
                        <div class="<?php print ((isset($pa_bundle_settings['no_close_button']) && $pa_bundle_settings['no_close_button']) ? 'hidden' : ''); ?>">
                            <button type="button" id="<?php print $vs_id_prefix; ?>browseToggle" class="btn btn-default btn-sm">
                                <?php print _t('Show in browser'); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (is_array($pa_ancestors) && sizeof($pa_ancestors) > 0): ?>
                    <?php if (is_array($pa_ancestors) && sizeof($pa_ancestors) > 0): ?>
                        <?php foreach ($pa_ancestors as $vn_id => $va_item): ?>
                            <?php if ($vn_id === ''): ?>
                                <a href="#">
                                    <?php print _t('New %1', $t_subject->getTypeName()); ?>
                                </a>
                            <?php elseif ($pn_id && $va_item[$t_subject->primaryKey()] && ($vs_item_id != $pn_id)): ?>
                                <a href="<?php print caEditorUrl($this->request, $t_subject->tableName(), $vn_id); ?>">
                                    <?php print $va_item['label']; ?>
                                </a>
                            <?php else: ?>
                                <a href="#" onclick="jQuery('#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer').slideDown(250); o<?php print $vs_id_prefix; ?>HierarchyBrowser.setUpHierarchy('<?php print preg_replace('/^.*-(.*?)$/', '$1', $vs_item_id); ?>'); return false;">
                                    <?php print $va_item['label']; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
		</div>
	</div>

    <?php if ($pn_id > 0): ?>
		<div id="<?php print $vs_id_prefix; ?>HierarchyBrowserContainer" class="editorHierarchyBrowserContainer">
			<div  id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs">
				<ul>
                    <li>
                        <a href="#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-explore" onclick="_init<?php print $vs_id_prefix; ?>ExploreHierarchyBrowser();">
                            <?php print _t('Explore'); ?>
                        </a>
                    </li>
				</ul>
		
				<div id="<?php print $vs_id_prefix; ?>HierarchyBrowserTabs-explore" class="<?php print (isset($pa_bundle_settings['hierarchy_browse_tab_class']) && $pa_bundle_settings['hierarchy_browse_tab_class']) ? $pa_bundle_settings['hierarchy_browse_tab_class'] : "hierarchyBrowseTab"; ?>">	
					<div class="hierarchyBrowserMessageContainer">
						<?php print _t('Use the browser to explore the hierarchy. You may edit other hierarchy items by clicking on the arrows.'); ?>
					</div>
					<div id="<?php print $vs_id_prefix; ?>HierarchyBrowser" class="hierarchyBrowserSmall">
					</div>
				</div>
			</div>
		</div>
    <?php endif; ?>
</div>

<?php if ($pn_id > 0): ?>
	<script>
		var o<?php print $vs_id_prefix; ?>HierarchyBrowser;

        (function ($) {
            'use strict';

            $(function() {
                o<?php print $vs_id_prefix; ?>HierarchyBrowser = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>HierarchyBrowser', {
                    levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                    initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',
                    readOnly: false,
                    initItemID: '<?php print $vn_init_id; ?>',
                    indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>", // TODO FIXME loading animation
                    dontAllowEditForFirstLevel: <?php print (in_array($t_subject->tableName(), array('ca_places', 'ca_storage_locations', 'ca_list_items', 'ca_relationship_types')) ? 'true' : 'false'); ?>,
                    disabledItems: '<?php print $vs_disabled_items_mode; ?>',
                    editUrl: '<?php print $vs_edit_url; ?>',
                    editButtonIcon: '<span class="glyphicon glyphicon-menu-right"></span>',
                    disabledButtonIcon: '<span class="glyphicon glyphicon-remove-circle"></span>',
                    currentSelectionDisplayID: 'browseCurrentSelection',
                    autoShrink: <?php print (caGetOption('auto_shrink', $pa_bundle_settings, false) ? 'true' : 'false'); ?>,
                    autoShrinkAnimateID: '<?php print $vs_id_prefix; ?>HierarchyBrowser'
                });

                $("#<?php print $vs_id_prefix; ?>browseToggle").click(function(e, opts) {
                    var delay = (opts && opts.delay && (parseInt(opts.delay) >= 0)) ? opts.delay :  250;
                    $("#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer").slideToggle(delay, function() {
                        $("#<?php print $vs_id_prefix; ?>browseToggle").html((this.style.display === 'block') ? '<?php print _t('Close browser'); ?>' : '<?php print _t('Show in browser'); ?>');
                    });
                    return false;
                });

                $('#<?php print $vs_id_prefix; ?>HierarchyBrowserContainer').hide(0);

                // TODO Change jQuery tabs to bootstrap tabs
                $("#<?php print $vs_id_prefix; ?>HierarchyBrowserTabs").tabs({ selected: 0 });
            });

            <?php if (isset($pa_bundle_settings['open_hierarchy']) && (bool)$pa_bundle_settings['open_hierarchy']): ?>
                $("#<?php print $vs_id_prefix; ?>browseToggle").trigger("click", { "delay": 0 });
            <?php endif; ?>
        }(jQuery));
	</script>
<?php endif; ?>
