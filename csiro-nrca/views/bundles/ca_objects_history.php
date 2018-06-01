<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_table_num = $this->getVar('table_num');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vb_read_only = (isset($va_settings['readonly']) && $va_settings['readonly']);
$va_history = $this->getVar('history');
$vs_mode = $this->getVar('mode');
$vs_relationship_type = $this->getVar('location_relationship_type');
$vs_change_location_url = $this->getVar('location_change_url');
$va_storage_location_elements = caGetOption('ca_storage_locations_elements', $va_settings, array());
$va_occ_types = $this->getVar('occurrence_types');
$va_lookup_params = array();
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-objects-history">
    <?php if (!$vb_read_only): ?>
        <div class="caUseHistoryButtonBar labelInfo text-left">
            <?php if(!caGetOption('hide_add_to_loan_controls', $va_settings, false)): ?>
                <button type="button" class="btn btn-primary add-loan" id="<?php print $vs_id_prefix; ?>AddLoan">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php print _t('Add to loan'); ?>
                </button>
            <?php endif; ?>
            <?php if(!caGetOption('hide_update_location_controls', $va_settings, false)): ?>
                <button type="button" class="btn btn-primary add-location" id="<?php print $vs_id_prefix; ?>ChangeLocation">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php print _t('Update location'); ?>
                </button>
            <?php endif; ?>
            <?php if(!caGetOption('hide_add_to_occurrence_controls', $va_settings, false)): ?>
                <?php foreach($va_occ_types as $vn_type_id => $va_type_info):?>
                    <button type="button" class="btn btn-default add-occurrence<?php print $vn_type_id; ?>" id="<?php print $vs_id_prefix; ?>AddOcc<?php print $vn_type_id; ?>">
                        <span class="glyphicon glyphicon-primary"></span>
                        <?php print _t('Add to %1', $va_type_info['name_singular']); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="item-list-location"> </div>
    <div class="loan-item-list"> </div>
    <?php if(!caGetOption('hide_add_to_occurrence_controls', $va_settings, false)): ?>
        <?php foreach($va_occ_types as $vn_type_id => $va_type_info): ?>
            <div class="occurrence-item-list<?php print $vn_type_id; ?>"> </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php foreach($va_history as $vn_date => $va_history_entries_for_date): ?>
        <?php foreach($va_history_entries_for_date as $vn_i => $va_history_entry): ?>
            <div class="caUseHistoryEntry <?php print ($vn_i == 0) ? 'caUseHistoryEntryFirst' : ''; ?>">
                <?php print $va_history_entry['icon']; ?>
                <div><?php print $va_history_entry['display']; ?></div>
                <div class="caUseHistoryDate"><?php print $va_history_entry['date']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php if ($vs_mode == 'ca_storage_locations'): ?>
        <textarea class="location-new-item-template hidden">
            <div id="<?php print $vs_id_prefix; ?>Location_{n}" class="labelInfo related-item-location">
                <h2 class="caUseHistorySetLocationHeading"><?php print _t('Update location'); ?></h2>
                <?php if (!(bool)$va_settings['useHierarchicalBrowser']): ?>
                    <div class="caListItem">
                        <div class="elements-container removable">
                            <input type="text" size="60" name="<?php print $vs_id_prefix; ?>_location_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_location_autocomplete{n}" class="lookupBg"/>
                            <input type="hidden" name="<?php print $vs_id_prefix; ?>_location_id{n}" id="<?php print $vs_id_prefix; ?>_location_id{n}" value="{id}"/>
                        </div>
                        <button type="button" class="remove remove-location">
                            <?php print _t('Remove'); ?>
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="elements-container removable">
                        <div class="text-right hierarchyBrowserSearchBar">
                            <label for="<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}"><?php print _t('Search'); ?></label>
                            <input type="text" id="<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}" name="search" />
                        </div>
                        <div id="<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}" class="hierarchyBrowser">
                            <!-- Content for hierarchy browser is dynamically inserted here by ca.hierbrowser -->
                        </div>
                        <div class="hierarchyBrowserCurrentSelectionText">
                            <input type="hidden" name="<?php print $vs_id_prefix; ?>_location_id{n}" id="<?php print $vs_id_prefix; ?>_location_id{n}" value="{id}"/>
                            <span class="hierarchyBrowserCurrentSelectionText" id="<?php print $vs_id_prefix; ?>_browseCurrentSelectionText{n}"> </span>
                        </div>
                    </div>
                    <button type="button" class="remove remove-location">
                        <?php print _t('Remove'); ?>
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>

                    <?php if(is_array($va_storage_location_elements) && sizeof($va_storage_location_elements)): ?>
                        <?php $t_rel = Datamodel::load()->getInstanceByTableName('ca_objects_x_storage_locations', true); ?>
                        <?php foreach($va_storage_location_elements as $vs_element): ?>
                            <div class="row">
                                <?php if($t_rel->hasField($vs_element)): ?>
                                    <?php $vs_field_type = $t_rel->getFieldInfo($vs_element, 'FIELD_TYPE'); ?>
                                    <?php $vs_field_class = ''; ?>
                                    <?php if(in_array($vs_field_type, array(FT_DATETIME, FT_HISTORIC_DATETIME, FT_DATERANGE, FT_HISTORIC_DATERANGE))): ?>
                                        <?php $vs_field_class = 'dateBg'; ?>
                                    <?php endif; ?>
                                    <div class="col-md-3">
                                        <?php print $t_rel->getDisplayLabel($t_rel->tableName().".".$vs_element); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php print $t_rel->htmlFormElement($vs_element, '', ['name' => $vs_id_prefix.'_location_'.$vs_element.'{n}', 'id' => $vs_id_prefix.'_location_'.$vs_element.'{n}', 'value' => _t('now'), 'classname' => $vs_field_class]); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <?php print $t_rel->getDisplayLabel($t_rel->tableName().".".$vs_element); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php print $t_rel->getAttributeHTMLFormBundle($this->request, null, $vs_element, $this->getVar('placement_code'), $va_settings, ['elementsOnly' => true]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <script>
                        (function($) {
                            $(function() {
                                var <?php print $vs_id_prefix; ?>oHierBrowser{n} = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}', {
                                    uiStyle: 'horizontal',
                                    levelDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'GetHierarchyLevel', array()); ?>',
                                    initDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'GetHierarchyAncestorList'); ?>',
                                    selectOnLoad : true,
                                    browserWidth: '100%',
                                    dontAllowEditForFirstLevel: false,
                                    className: 'hierarchyBrowserLevel',
                                    classNameContainer: 'hierarchyBrowserContainer',
                                    indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>",
                                    editButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_RIGHT_ARROW__, 1); ?>",
                                    disabledButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_DOT__, 1); ?>",
                                    indicatorUrl: '<?php print $this->request->getThemeUrlPath(); ?>/graphics/icons/indicator.gif',
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
                                                <?php print $vs_id_prefix; ?>oHierBrowser{n}.setUpHierarchy(ui.item.id);	// jump browser to selected item
                                            }
                                            event.preventDefault();
                                            $('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}').val('');
                                        }
                                    }
                                );
                                $('#<?php print $vs_id_prefix; ?>_location_effective_date{n}').datepicker({dateFormat: 'yy-mm-dd'});  // attempt to add date picker
                            })
                        })(jQuery);
                    </script>
                <?php endif; ?>
            </div>
        </textarea>
    <?php endif; ?>

    <?php if(!caGetOption('hide_add_to_loan_controls', $va_settings, false)): ?>
        <textarea class='loan-new-item-template hidden'>
            <div id="<?php print $vs_id_prefix; ?>Loan_{n}" class="labelInfo related-loan">
                <div class="elements-container removable">
                    <div class="row">
                        <div class="col-md-4">
                            <h2><?php print _t('Add to loan'); ?></h2>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="<?php print $vs_id_prefix; ?>_loan_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_loan_autocomplete{n}" class="lookupBg"/>
                        </div>
                        <div class="col-md-4">
                            <select name="<?php print $vs_id_prefix; ?>_loan_type_id{n}" id="<?php print $vs_id_prefix; ?>_loan_type_id{n}" class="hidden"></select>
                            <input type="hidden" name="<?php print $vs_id_prefix; ?>_loan_id{n}" id="<?php print $vs_id_prefix; ?>_loan_id{n}" value="{id}"/>
                        </div>
                    </div>
                </div>
                <button type="button" class="remove remove-loan">
                    <?php print _t('Remove'); ?>
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            </div>
        </textarea>
    <?php endif; ?>

    <?php if(!caGetOption('hide_add_to_occurrence_controls', $va_settings, false)): ?>
        <?php foreach($va_occ_types as $vn_type_id => $va_type_info): ?>
            <textarea class="caUseHistorySetOccurrenceTemplate<?php print $vn_type_id; ?> hidden">
                <div id="<?php print $vs_id_prefix; ?>Occurrence_<?php print $vn_type_id; ?>_{n}" class="labelInfo occurrence-list-item">
                    <div class="elements-container removable">
                        <div class="row">
                            <div class="col-md-4">
                                <h2><?php print _t('Add to %1', $va_type_info['name_singular']); ?></h2>
                            </div>
                            <div class="col-md-4">
                                <input type="text" size="60" name="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_autocomplete{n}" value="{{label}}" id="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_autocomplete{n}" class="lookupBg"/>
                            </div>
                            <div class="col-md-2">
                                <select name="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_type_id{n}" class="hidden"></select>
                                <input type="hidden" name="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_id{n}" value="{id}"/>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove remove-occurrence<?php print $vn_type_id; ?>">
                        <?php print _t('Remove'); ?>
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                </div>
            </textarea>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal">
	<div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
</div>

<?php if (!$vb_read_only): ?>
    <script>
        (function($) {
            $(function() {
                var quickAddPanelId = "caRelationQuickAddPanel<?php print $vs_id_prefix; ?>";
                var caRelationQuickAddPanel<?php print $vs_id_prefix; ?>;

                if (caUI.initPanel) {
                    caRelationQuickAddPanel<?php print $vs_id_prefix; ?> = caUI.initPanel({
                        panelID: quickAddPanelId,
                        panelContentID: quickAddPanelId + "ContentArea",
                        initialFadeIn: false,
                        useExpose: false,
                        onOpenCallback: function() {
                            $('#' + quickAddPanelId).modal('show');
                        },
                        onCloseCallback: function() {
                            $('#' + quickAddPanelId).modal('hide');
                        }
                    });
                }
                <?php if ($vs_mode === 'ca_storage_locations'): ?>
                    caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix; ?>', {
                        fieldNamePrefix: '<?php print $vs_id_prefix; ?>_location_',
                        templateValues: ['label', 'type_id', 'id'],
                        initialValues: [],
                        initialValueOrder: [],
                        itemID: '<?php print $vs_id_prefix; ?>Location_',
                        placementID: '<?php print $vn_placement_id; ?>',
                        templateClassName: 'location-new-item-template',
                        initialValueTemplateClassName: null,
                        itemListClassName: 'item-list-location',
                        listItemClassName: 'related-item-location',
                        addButtonClassName: 'add-location',
                        deleteButtonClassName: 'remove-location',
                        showEmptyFormsOnLoad: 0,
                        relationshipTypes: <?php print json_encode($this->getVar('location_relationship_types_by_sub_type')); ?>,
                        autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'StorageLocation', 'Get', $va_lookup_params); ?>',
                        minChars:1,
                        readonly: false,
                        isSortable: false,
                        listSortItems: 'div.roundedRel',
                        autocompleteInputID: '<?php print $vs_id_prefix; ?>_autocomplete',
                        quickaddPanel: caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
                        quickaddUrl: '<?php print caNavUrl($this->request, 'editor/storage_locations', 'StorageLocationQuickAdd', 'Form', array('location_id' => 0, 'dont_include_subtypes_in_type_restriction' => (int)$va_settings['dont_include_subtypes_in_type_restriction'])); ?>',
                        minRepeats: 0,
                        maxRepeats: 2,
                        addMode: 'prepend',
                        useAnimation: 1,
                        onAddItem: function(id, options, isNew) {
                            $(".caUseHistoryButtonBar").slideUp(250);
                        },
                        onDeleteItem: function(id) {
                            $(".caUseHistoryButtonBar").slideDown(250);
                        }
                    });
                <?php else: ?>
                    var panelContentID = '#' + caRelationQuickAddPanel<?php print $vs_id_prefix; ?>.getPanelContentID();
                    $(panelContentID)
                        .data('relatedID', <?php print (int)$t_subject->getPrimaryKey(); ?>)
                        .data('relatedTable', 'ca_objects')
                        .data('relationshipType', '<?php print $vs_relationship_type; ?>')
                        .data('panel', caRelationQuickAddPanel<?php print $vs_id_prefix; ?>);

                    $("#<?php print $vs_id_prefix; ?>ChangeLocation").on("click", function() {
                        caRelationQuickAddPanel<?php print $vs_id_prefix; ?>.showPanel('<?php print $vs_change_location_url; ?>');
                        return false;
                    });
                <?php endif; ?>
                caRelationBundle<?php print $vs_id_prefix; ?>_ca_loans = caUI.initRelationBundle('#<?php print $vs_id_prefix; ?>', {
                    fieldNamePrefix: '<?php print $vs_id_prefix; ?>_loan_',
                    templateValues: ['label', 'id', 'type_id', 'typename', 'idno_sort'],
                    initialValues: [],
                    initialValueOrder: [],
                    itemID: '<?php print $vs_id_prefix; ?>Loan_',
                    placementID: '<?php print $vn_placement_id; ?>',
                    templateClassName: 'loan-new-item-template',
                    initialValueTemplateClassName: null,
                    itemListClassName: 'loan-item-list',
                    listItemClassName: 'related-loan',
                    addButtonClassName: 'add-loan',
                    deleteButtonClassName: 'remove-loan',
                    hideOnNewIDList: [],
                    showEmptyFormsOnLoad: 0,
                    relationshipTypes: <?php print json_encode($this->getVar('loan_relationship_types_by_sub_type')); ?>,
                    autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'Loan', 'Get', $va_lookup_params); ?>',
                    types: <?php print json_encode($va_settings['restrict_to_types']); ?>,
                    readonly: <?php print $vb_read_only ? "true" : "false"; ?>,
                    isSortable: <?php print ($vb_read_only || $vs_sort) ? "false" : "true"; ?>,
                    listSortOrderID: '<?php print $vs_id_prefix; ?>LoanBundleList',
                    listSortItems: 'div.roundedRel',
                    autocompleteInputID: '<?php print $vs_id_prefix; ?>_autocomplete',
                    quickaddPanel: caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
                    quickaddUrl: '<?php print caNavUrl($this->request, 'editor/loans', 'LoanQuickAdd', 'Form', array('loan_id' => 0, 'dont_include_subtypes_in_type_restriction' => (int)$va_settings['dont_include_subtypes_in_type_restriction'])); ?>',
                    minRepeats: 0,
                    maxRepeats: 2,
                    useAnimation: 1,
                    onAddItem: function(id, options, isNew) {
                        $(".caUseHistoryButtonBar").slideUp(250);
                    },
                    onDeleteItem: function(id) {
                        $(".caUseHistoryButtonBar").slideDown(250);
                    }
                });
                <?php if(!caGetOption('hide_add_to_occurrence_controls', $va_settings, false)): ?>
                    <?php foreach($va_occ_types as $vn_type_id => $va_type_info): ?>
                        caRelationBundle<?php print $vs_id_prefix; ?>_ca_occurrences_<?php print $vn_type_id; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix; ?>', {
                            fieldNamePrefix: '<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_',
                            templateValues: ['label', 'id', 'type_id', 'typename', 'idno_sort'],
                            initialValues: [],
                            initialValueOrder: [],
                            itemID: '<?php print $vs_id_prefix; ?>Occurrence_<?php print $vn_type_id; ?>_',
                            placementID: '<?php print $vn_placement_id; ?>',
                            templateClassName: 'caUseHistorySetOccurrenceTemplate<?php print $vn_type_id; ?>',
                            initialValueTemplateClassName: null,
                            itemListClassName: 'occurrence-item-list<?php print $vn_type_id; ?>',
                            listItemClassName: 'occurrence-list-item',
                            addButtonClassName: 'add-occurrence<?php print $vn_type_id; ?>',
                            deleteButtonClassName: 'remove-occurrence<?php print $vn_type_id; ?>',
                            hideOnNewIDList: [],
                            showEmptyFormsOnLoad: 0,
                            relationshipTypes: <?php print json_encode($this->getVar('occurrence_relationship_types_by_sub_type')); ?>,
                            autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'Occurrence', 'Get', $va_lookup_params); ?>',
                            types: <?php print json_encode($va_settings['restrict_to_types']); ?>,
                            readonly: <?php print $vb_read_only ? "true" : "false"; ?>,
                            isSortable: <?php print ($vb_read_only || $vs_sort) ? "false" : "true"; ?>,
                            listSortOrderID: '<?php print $vs_id_prefix; ?>OccurrenceBundleList',
                            listSortItems: 'div.roundedRel',
                            autocompleteInputID: '<?php print $vs_id_prefix; ?>_occurrence_<?php print $vn_type_id; ?>_autocomplete',
                            quickaddPanel: caRelationQuickAddPanel<?php print $vs_id_prefix; ?>,
                            quickaddUrl: '<?php print caNavUrl($this->request, 'editor/occurrences', 'OccurrenceQuickAdd', 'Form', array('types' => $vn_type_id,'occurrence_id' => 0, 'dont_include_subtypes_in_type_restriction' => (int)$va_settings['dont_include_subtypes_in_type_restriction'])); ?>',
                            minRepeats: 0,
                            maxRepeats: 2,
                            useAnimation: 1,
                            onAddItem: function(id, options, isNew) {
                                $(".caUseHistoryButtonBar").slideUp(250);
                            },
                            onDeleteItem: function(id) {
                                $(".caUseHistoryButtonBar").slideDown(250);
                            }
                        });
                    <?php endforeach; ?>
                <?php endif; ?>
            });
        })(jQuery);
    </script>
<?php endif; ?>
