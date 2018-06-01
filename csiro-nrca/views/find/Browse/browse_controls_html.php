<?php
$va_facets = $this->getVar('available_facets');
$va_info_for_facets = $this->getVar('facet_info');
$va_criteria = is_array($this->getVar('criteria')) ? $this->getVar('criteria') : array();
$va_available_facets = $this->getVar('available_facets');
?>
<div class="component component-browse-controls">
    <?php if (!$this->request->isAjax()): ?>
        <?php // TODO FIXME Use an explicit variable, not a table name comparison; see /views/find/Search/common/search.php ?>
        <?php if ($this->getVar('target') == 'ca_objects'): ?>
            <div id="quickLookOverlay">
                <div id="quickLookOverlayContent"></div>
            </div>
        <?php endif; ?>

        <div id="browse">
            <div class="relative">
                <?php if (sizeof($va_criteria)): ?>
                    <div class="well well-sm">
                        <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'clearCriteria'); ?>" class="pull-right">
                            <?php print _t('Start over'); ?>
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                        <label><?php print _t("You browsed for: "); ?></label>
                        <div>
                            <?php foreach ($va_criteria as $vs_facet_name => $va_row_ids): ?>
                                <?php foreach ($va_row_ids as $vn_row_id => $vs_label): ?>
                                    <div class="criterion">
                                        <?php print caGetOption('label_singular', $va_info_for_facets[$vs_facet_name], "???"); ?>
                                        <?php print $vs_label; ?>
                                        <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'removeCriteria', array('facet' => $vs_facet_name, 'id' => urlencode($vn_row_id))); ?>" class="remove">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (sizeof($va_facets)): ?>
                    <div class="clearfix">
                        <h2><?php print (sizeof($va_criteria) ? _t('Refine results by') : _t("Browse by")); ?></h2>
                        <?php foreach($va_available_facets as $vs_facet_code => $va_facet_info): ?>
                            <button type="button" class="btn btn-default facet-link facet-<?php print $vs_facet_code?>" onclick="caUIBrowsePanel.showBrowsePanel('<?php print $vs_facet_code?>'); $('.facet-link').removeClass('active'); $('.facet-<?php print $vs_facet_code?>').addClass('active');">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                                <?php print $va_facet_info['label_plural']; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div id="splashBrowsePanel">
        <div id="splashBrowsePanelContent"></div>
    </div>

    <div id="resultBox">
        <?php if (sizeof($va_criteria) > 0): ?>
            <div class="clearfix">
                <div class="pull-right">
                    <?php print $this->render('Results/paging_controls_html.php'); ?>
                </div>
                <?php print $this->render('Results/search_options_html.php'); ?>
            </div>
            <?php print $this->render('Results/' . $this->getVar('target') . '_results_' . $this->getVar('current_view') . '_html.php'); ?>
            <?php print $this->render('Results/paging_controls_minimal_html.php'); ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!$this->request->isAjax()): ?>
    <script>
        <?php if ($this->getVar('target') == 'ca_objects'): ?>
            // Set up the "quicklook" panel that will be triggered by links in each search result. Note that the actual
            // <div>s implementing the panel are located in views/pageFormat/pageFooter.php
            var caQuickLookPanel = caUI.initPanel({
                panelID: 'quickLookPanel',
                panelContentID: 'quickLookPanelContentArea',
                exposeBackgroundColor: '#000000',
                exposeBackgroundOpacity: 0.5,
                panelTransitionSpeed: 200
            });
        <?php endif; ?>

        var caUIBrowsePanel = caUI.initBrowsePanel({
            useStaticDiv: true,
            facetUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacet'); ?>',
            addCriteriaUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'addCriteria'); ?>',
            singleFacetValues: <?php print json_encode($this->getVar('single_facet_values')); ?>
        });
    </script>
<?php endif; ?>
