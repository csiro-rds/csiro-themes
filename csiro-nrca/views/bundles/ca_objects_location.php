<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vs_current_location = $this->getVar('current_location');
$va_history = $this->getVar('location_history');
$vs_mode = $this->getVar('mode');

$va_location_colour_map = array(
    'FUTURE' => 'futureLocationColor',
    'CURRENT' => 'currentLocationColor',
    'PAST' => 'pastLocationColor'
);
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-objects-location">
    <div class="bundleContainer">
        <div class="caItemList">
            <div id="<?php print $vs_id_prefix; ?>Container" class="editorHierarchyBrowserContainer">
                <div  id="<?php print $vs_id_prefix; ?>Tabs">
                    <ul>
                        <li>
                            <a href="#<?php print $vs_id_prefix; ?>Tabs-location">
                                <?php print _t('Current location'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#<?php print $vs_id_prefix; ?>Tabs-history">
                                <?php print _t('History'); ?>
                            </a>
                        </li>
                    </ul>
                    <div id="<?php print $vs_id_prefix; ?>Tabs-location" class="hierarchyBrowseTab">
                        <?php if ($vs_current_location): ?>
                            <div style="background-color: <?php print '#' . $va_settings['currentLocationColor']; ?>">
                                <?php print $vs_current_location; ?>
                            </div>
                        <?php else: ?>
                            <?php print _t('No location set'); ?>
                        <?php endif; ?>
                    </div>
                    <div id="<?php print $vs_id_prefix; ?>Tabs-history" class="hierarchyBrowseTab caLocationHistoryTab">
                        <?php if (is_array($va_history) && sizeof($va_history)): ?>
                            <?php foreach ($va_history as $vn_id => $va_relation): ?>
                                <div style="background-color: <?php print '#' . $va_settings[$va_location_colour_map[$va_relation['status']]]; ?>">
                                    <?php print $va_relation['display']; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php print _t('No location history set'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="<?php print $vs_id_prefix; ?>ChangeLocation" class="caAddItemButton">
            <span class="glyphicon glyphicon-plus"></span>
            <?php print $this->getVar('add_label') ?: _t('Update location'); ?>
        </button>
    </div>

    <?php if ($vs_mode == 'ca_storage_locations'): ?>
        <textarea class="caNewItemTemplate hidden">
            <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="caRelatedItem clearfix">
                <div class="row">
                    <div class="col-md-11">
                        <?php if (!(bool)$va_settings['useHierarchicalBrowser']): ?>
                            <input name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="lookupBg"/>
                            <input type="hidden" name="<?php print $vs_id_prefix; ?>_location_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                        <?php else: ?>
                            <div class="clearfix">
                                <div id="<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}" class='hierarchyBrowser'></div>
                            </div>
                            <div class="clearfix">
                                <div style="pull-right">
                                    <?php print _t('Search'); ?>:
                                    <input id="<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}" name="search" value="" />
                                </div>
                                <div class="pull-left" id="<?php print $vs_id_prefix; ?>_browseCurrentSelectionText{n}"></div>
                                <input type="hidden" name="<?php print $vs_id_prefix; ?>_location_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="caDeleteItemButton">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                    </div>
                </div>
            </div>
        </textarea>
    <?php endif; ?>
</div>

<div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal" role="dialog">
    <div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
</div>

<script>
    var caRelationBundle<?php print $vs_id_prefix; ?>, caRelationQuickAddPanel<?php print $vs_id_prefix; ?>;

    (function ($) {
        $(function () {
            <?php if ($vs_mode == 'ca_storage_locations'): ?>
                // TODO Use bootstrap tabs not jQuery tabs
                $("#<?php print $vs_id_prefix; ?>Tabs").tabs({ selected: 0 });

                <?php include('common/relationship_bundle.php') ?>

                $.extend(caRelationBundle<?php print $vs_id_prefix; ?>, {
                    initialValues: [],
                    initialValueOrder: [],
                    showEmptyFormsOnLoad: 0,
                    autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'Get', $va_lookup_params); ?>',
                    minChars: 1,
                    readonly: false,
                    isSortable: false,
                    autocompleteInputID: '<?php print $vs_id_prefix; ?>_autocomplete',
                    minRepeats: 0,
                    maxRepeats: 1
                });
            <?php else: ?>
                $("#<?php print $vs_id_prefix; ?>Tabs").tabs({ selected: 0 });

                if (caUI.initPanel) {
                    <?php include('common/quick_add_panel.php') ?>
                    $(quickAddPanelContentId)
                        .data('relatedID', <?php print (int)$t_subject->getPrimaryKey(); ?>)
                        .data('relatedTable', 'ca_objects')
                        .data('relationshipType', '<?php print $this->getVar('location_relationship_type'); ?>')
                        .data('panel', caRelationQuickAddPanel<?php print $vs_id_prefix; ?>);

                    $("#<?php print $vs_id_prefix; ?>ChangeLocation").on("click", function() {
                        caRelationQuickAddPanel<?php print $vs_id_prefix; ?>.showPanel('<?php print $this->getVar('location_change_url'); ?>');
                        return false;
                    });
                }
            <?php endif; ?>

            <?php if ($va_settings['useHierarchicalBrowser']): ?>
                var <?php print $vs_id_prefix; ?>oHierBrowser{n} = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}', {
                    uiStyle: 'horizontal',
                    levelDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'GetHierarchyLevel', array()); ?>',
                    initDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'GetHierarchyAncestorList'); ?>',
                    selectOnLoad : true,
                    browserWidth: '100%',
                    dontAllowEditForFirstLevel: false,
                    className: 'hierarchyBrowserLevel',
                    classNameContainer: 'hierarchyBrowserContainer',
                    editButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_RIGHT_ARROW__, 1); ?>",
                    disabledButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_DOT__, 1); ?>",
                    indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>",
                    displayCurrentSelectionOnLoad: false,
                    currentSelectionDisplayID: '<?php print $vs_id_prefix; ?>_browseCurrentSelectionText{n}',
                    onSelection: function(item_id, parent_id, name, display, type_id) {
                        caRelationBundle<?php print $vs_id_prefix; ?>.select('{n}', {id: item_id, type_id: type_id}, display);
                    }
                });

                $('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}').autocomplete({
                        source: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'Get', array('noInline' => 1)); ?>',
                        minLength: 3, delay: 800, html: true,
                        select: function(event, ui) {
                            if (parseInt(ui.item.id) > 0) {
                                <?php print $vs_id_prefix; ?>oHierBrowser{n}.setUpHierarchy(ui.item.id);    // jump browser to selected item
                            }
                            event.preventDefault();
                            $('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}').val('');
                        }
                    }
                );
            <?php endif; ?>
        });
    }(jQuery));
</script>