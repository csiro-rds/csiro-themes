<?php
$o_browse = $this->getVar('browse');
$va_criteria = $o_browse->getCriteriaWithLabels();
?>

<div class="component component-search-refine">
    <div id="searchRefineBox" class="search-box collapse">
        <?php if (sizeof($va_criteria) > 1): ?>
            <div id="searchRefineParameters">
                <h2><?php print _t('Filtering results by'); ?>:</h2>
                <?php foreach ($va_criteria as $vs_facet_name => $va_row_ids): ?>
                    <?php foreach ($va_row_ids as $vn_row_id => $vs_label): ?>
                        <?php if ($vs_facet_name !== '_search'): ?>
                            <div class="criterion">
                                <?php print $vs_label; ?>
                                <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'removeCriteria', array('facet' => $vs_facet_name, 'id' => urlencode($vn_row_id))); ?>" class="remove">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'clearCriteria'); ?>" class="btn btn-warning">
                    <span class="glyphicon glyphicon-remove"></span>
                    <?php print _t('clear all'); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="clearfix">
            <div id="searchRefineContent">
            </div>
        </div>
    </div>
</div>

<script>
    var caUIBrowsePanel;

    (function ($) {
        'use strict';

        $(function() {
            $('#searchRefineContent').load('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Facets');?>');

            <?php if (!$this->request->isAjax()): ?>
                caUIBrowsePanel = caUI.initBrowsePanel({ facetUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacet'); ?>', useExpose: false });
                $('.scrollableBrowseController').scrollable();
            <?php endif; ?>

            <?php if (sizeof($o_browse->getInfoForAvailableFacets()) && $this->getVar('open_refine_controls')): ?>
                // keep the refine box open; there are more criteria to refine by and you just did a refine or cleared an option
                $('#searchRefineBox').show(0);
                $('#showRefine').hide(0);
                $('#searchOptionsBox').hide(0);
                $('#showOptions').show(0);
            <?php endif; ?>
        });
    }(jQuery));
</script>
