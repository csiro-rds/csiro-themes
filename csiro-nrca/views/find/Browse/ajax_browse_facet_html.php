<?php
$t_item = $this->getVar('t_item');
$t_subject = $this->getVar('t_subject');
$va_facet = $this->getVar('grouped_facet');
$vs_facet_name = $this->getVar('facet_name');
$va_facet_info = $this->getVar('facet_info');
$vb_individual_group_display = (bool)$this->getVar('individual_group_display');
$vm_modify_id = $this->getVar('modify') ?: '0';

$va_service_urls = caJSONLookupServiceUrl($this->request, $va_facet_info['table'], array('noInline' => 1, 'noSymbols' => 1));
$vb_multiple_selection_facet = caGetOption('multiple', $va_facet_info, false, ['castTo' => 'boolean']);
$vs_selected_group = $vb_individual_group_display ? $this->getVar('only_show_group') : null;
$va_facet = ($vs_selected_group && isset($va_facet[$vs_selected_group])) ? array($vs_selected_group => $va_facet[$vs_selected_group]) : $va_facet;
?>

<div id="browseSelectPanelContentArea" class="component component-ajax-refine-facet">
    <?php if ($va_facet && $vs_facet_name): ?>
        <?php if ($vb_multiple_selection_facet): ?>
            <a href="#" class="facetApply pull-right btn btn-default" data-facet="<?php print $vs_facet_name; ?>">
                <?php print _t('Apply'); ?>
            </a>
        <?php endif; ?>

        <h2><?php print unicode_ucfirst($va_facet_info['label_plural']); ?></h2>

        <?php if ($va_facet_info['group_mode'] === 'hierarchical'): ?>
            <div id="<?php print $vs_facet_name; ?>_facet_container">
                <div id="hierarchyBrowser"></div>
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($t_item && $t_subject): ?>
                            <div class="well">
                                <?php print _t("Click on a %1 to find %2 related to it. Click on the arrow next to a %3 to see more specific %4 within that %5, or use the search field.", $t_item->getProperty('NAME_SINGULAR'), $t_subject->getProperty('NAME_PLURAL'), $t_item->getProperty('NAME_SINGULAR'), $t_item->getProperty('NAME_PLURAL'), $t_item->getProperty('NAME_SINGULAR') ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <label for="hierarchyBrowserSearch"><?php print _t("Search"); ?>:</label>
                        <input id="hierarchyBrowserSearch" type="text" size="40" />
                        <span class="ui-helper-hidden-accessible" role="status" aria-live="polite"></span>
                    </div>
                </div>
            </div>
        <?php elseif ($va_facet_info['group_mode'] === 'none'): ?>
            <div id="<?php print $vs_facet_name; ?>_facet_container">
                <?php foreach ($va_facet as $va_item): ?>
                    <a href="<?php print caNavUrl($this->request, 'find', $this->request->getController(), ((strlen($vm_modify_id)) ? 'modifyCriteria' : 'addCriteria'), array('facet' => $vs_facet_name, 'id' => urlencode($va_item['id']), 'mod_id' => $vm_modify_id)); ?>" class="facetItem btn btn-default" data-facet_item_id="<?php print $va_item['id']; ?>">
                        <?php print html_entity_decode(caGetLabelForDisplay($va_facet, $va_item, $va_facet_info)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: /* alphabetical */ ?>
            <div class="clearfix">
                <div class="pull-left">
                    <label><?php print _t('Jump to group'); ?></label>
                    <div class="btn-group">
                        <?php foreach (array_keys($va_facet) as $vs_group): ?>
                            <?php if ($vb_individual_group_display): ?>
                                <a href="#" onclick="loadFacetGroup('<?php print (($vs_group === '~') ? '~' : $vs_group); ?>'); return false;" class="browse-facet btn <?php print (($vs_selected_group == $vs_group) ? 'btn-primary' : 'btn-default'); ?>">
                                    <?php print $vs_group; ?>
                                </a>
                            <?php else: ?>
                                <a href="#<?php print $vs_group; ?>">
                                    <?php print $vs_group; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="pull-right">
                    <?php if (isset($va_facet_info['groupings']) && is_array($va_facet_info['groupings']) && sizeof($va_facet_info['groupings'] )): ?>
                        <label><?php print _t('Group by'); ?></label>
                        <?php foreach ($va_facet_info['groupings'] as $vs_grouping => $vs_grouping_label): ?>
                            <a href="#" onclick="caUpdateFacetDisplay('<?php print $vs_grouping; ?>');">
                                <?php print $vs_grouping_label; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="<?php print $vs_facet_name; ?>_facet_container">
                <?php foreach ($va_facet as $vs_group => $va_items): ?>
                    <div>
                        <a name="<?php print $vs_group; ?>"></a>
                        <h2><?php print $vs_group; ?></h2>
                        <?php foreach (array_chunk($va_items, 4) as $va_row): ?>
                            <?php foreach ($va_row as $va_item): ?>
                                <span class="facetItem" data-facet_item_id="<?php print $va_item['id']; ?>">
                                    <a href="<?php print caNavUrl($this->request, 'find', $this->request->getController(), ((strlen($vm_modify_id)) ? 'modifyCriteria' : 'addCriteria'), array('facet' => $vs_facet_name, 'id' => urlencode($va_item['id']), 'mod_id' => $vm_modify_id)); ?>" class="btn btn-default btn-sm">
                                        <?php print html_entity_decode(caGetLabelForDisplay($va_facet, $va_item, $va_facet_info)); ?>
                                    </a>
                                </span>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted"><?php print _t('No facet defined'); ?></p>
    <?php endif; ?>
</div>

<?php if ($va_facet && $vs_facet_name): ?>
    <script>
        (function ($) {
            'use strict';

            <?php if ($va_facet_info['group_mode'] === 'hierarchical'): ?>
                var oHierBrowser;

                $(function() {
                    oHierBrowser = caUI.initHierBrowser('hierarchyBrowser', {
                        levelDataUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacetHierarchyLevel', array('facet' => $vs_facet_name)); ?>',
                        initDataUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacetHierarchyAncestorList', array('facet' => $vs_facet_name)); ?>',
                        editUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'addCriteria', array('facet' => $vs_facet_name, 'id' => '')); ?>',
                        editButtonIcon: '<span class="glyphicon glyphicon-edit"></span>',
                        initItemID: '<?php print $this->getVar('browse_last_id'); ?>',
                        indicator: '<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>', // TODO
                        currentSelectionDisplayID: 'browseCurrentSelection',
                        selectMultiple: <?php print json_encode($vb_multiple_selection_facet); ?>
                    });

                    $('#hierarchyBrowserSearch').autocomplete({
                        source: '<?php print $va_service_urls['search']; ?>',
                        minLength: 3,
                        delay: 800,
                        html: true,
                        select: function(event, ui) {
                            if (parseInt(ui.item.id) > 0) {
                                oHierBrowser.setUpHierarchy(ui.item.id); // jump browser to selected item
                            }
                            event.preventDefault();
                            $('#hierarchyBrowserSearch').val('');
                        }
                    });
                });
            <?php endif; ?>

            <?php if ($vb_multiple_selection_facet): ?>
                $(function() {
                    $('.facetItem').click(function () {
                        $(this).attr('facet_item_selected', $(this).attr('facet_item_selected') ? '' : '1');
                        $("#facet_apply").toggle($(".facetItem[facet_item_selected='1']").length > 0);
                        return false;
                    });

                    $('.facetApply').hide().click(function() {
                        var ids = [];
                        $('#<?php print $vs_facet_name; ?>_facet_container [facet_item_selected=1]').each(function(key, element) {
                            ids.push($(element).data('facet_item_id'));
                        });
                        if (ids.length > 0) {
                            window.location = '<?php print caNavUrl($this->request, 'find', $this->request->getController(),((strlen($vm_modify_id)) ? 'modifyCriteria' : 'addCriteria'), array('mod_id' => $vm_modify_id)); ?>/facet/<?php print $vs_facet_name; ?>/id/' + ids.join('|');
                        }
                        return false;
                    });
                });
            <?php endif; ?>
        }(jQuery));

        function loadFacetGroup(g) {
            jQuery('#browseSelectPanelContentArea').parent().load('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacet', array('facet' => $vs_facet_name, 'grouping' => $this->getVar('grouping'), 'show_group' => '')); ?>' + escape(g));
        }

        function caUpdateFacetDisplay(grouping) {
            caUIBrowsePanel.showBrowsePanel('<?php print $vs_facet_name; ?>', <?php print json_encode(intval($vm_modify_id) > 0); ?>, <?php print ((intval($vm_modify_id) > 0) ? $vm_modify_id : 'null'); ?>, grouping);
        }
    </script>
<?php endif; ?>
