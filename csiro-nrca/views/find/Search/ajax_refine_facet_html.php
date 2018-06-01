<?php
$va_facet = $this->getVar('grouped_facet');
$vs_facet_name = $this->getVar('facet_name');
$va_facet_info = $this->getVar('facet_info');
$vm_modify_id = $this->getVar('modify') ?: '0';
?>

<div class="component component-ajax-refine-facet">
    <?php if ($va_facet && $vs_facet_name): ?>
        <label><?php print unicode_ucfirst($va_facet_info['label_plural']); ?></label>

        <div class="clearfix">
            <div class="pull-right">
                <?php if (isset($va_facet_info['groupings']) && is_array($va_facet_info['groupings']) && sizeof($va_facet_info['groupings'] )): ?>
                    <label><?php print _t('Group by'); ?></label>
                    <div>
                        <?php foreach ($va_facet_info['groupings'] as $vs_grouping => $vs_grouping_label): ?>
                            <a href="#" onclick="caUpdateFacetDisplay('<?php print $vs_grouping; ?>');" class="<?php print ($vs_grouping === $this->getVar('grouping') ? 'text-primary' : ''); ?>">
                                <?php print $vs_grouping_label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($va_facet_info["group_mode"] === 'hierarchical'): ?>
                <div id="hierarchyBrowser"></div>
            <?php elseif ($va_facet_info["group_mode"] === 'none'): ?>
                <?php foreach ($va_facet as $va_item): ?>
                    <a href="<?php print caNavUrl($this->request, 'find', $this->request->getController(), (strlen($vm_modify_id) ? 'modifyCriteria' : 'addCriteria'), array('facet' => $vs_facet_name, 'id' => $va_item['id'], 'mod_id' => $vm_modify_id)); ?>" class="btn btn-default">
                        <?php print $va_item['label']; ?>
                    </a>
                <?php endforeach; ?>
            <?php else: /* alphabetical */ ?>
                <div class="pagination">
                    <?php foreach (array_keys($va_facet) as $vs_group): ?>
                        <a href="#<?php print $vs_group; ?>"><?php print $vs_group; ?></a>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($va_facet as $vs_group => $va_items): ?>
                    <div>
                        <a name="<?php print $vs_group; ?>"></a>
                        <h3><?php print $vs_group; ?></h3>
                        <div>
                            <?php foreach ($va_facet as $va_item): ?>
                                <a href="<?php print caNavUrl($this->request, 'find', $this->request->getController(), (strlen($vm_modify_id) ? 'modifyCriteria' : 'addCriteria'), array('facet' => $vs_facet_name, 'id' => $va_item['id'], 'mod_id' => $vm_modify_id)); ?>" class="btn btn-default">
                                    <?php print $va_item['label']; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="text-muted"><?php print _t('No facet defined'); ?></p>
    <?php endif; ?>
</div>

<script>
    <?php if ($va_facet && $vs_facet_name): ?>
        function caUpdateFacetDisplay(grouping) {
            caUIBrowsePanel.showBrowsePanel('<?php print $vs_facet_name; ?>', <?php print ((intval($vm_modify_id) > 0) ? 'true' : 'false'); ?>, <?php print ((intval($vm_modify_id) > 0) ?  $vm_modify_id : 'null'); ?>, grouping);
        }

        <?php if ($va_facet_info["group_mode"] === 'hierarchical'): ?>
            (function ($) {
                $(function() {
                    caUI.initHierBrowser('hierarchyBrowser', {
                        levelDataUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacetHierarchyLevel', array('facet' => $vs_facet_name)); ?>',
                        initDataUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacetHierarchyAncestorList', array('facet' => $vs_facet_name)); ?>',
                        editUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'addCriteria', array('facet' => $vs_facet_name, 'id' => '')); ?>',
                        editButtonIcon: '<span class="glyphicon glyphicon-edit"></span>',
                        initItemID: '<?php print $this->getVar('browse_last_id'); ?>',
                        indicator: '<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>', // TODO FIXME
                        currentSelectionDisplayID: 'browseCurrentSelection'
                    });
                });
            }(jQuery));
        <?php endif; ?>
    <?php endif; ?>
</script>
