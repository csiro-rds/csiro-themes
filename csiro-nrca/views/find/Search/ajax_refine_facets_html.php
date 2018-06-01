<?php
$va_available_facets = $this->getVar('browse')->getInfoForAvailableFacets();
?>
<div class="component component-ajax-refine-facets">
    <?php if (sizeof($va_available_facets)): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php print _t('Filter results by'); ?></h2>
            </div>
            <div class="panel-body">
                <?php foreach ($va_available_facets as $vs_facet_code => $va_facet_info): ?>
                    <button type="button" class="btn btn-default" onclick="caUIBrowsePanel.showBrowsePanel('<?php print $vs_facet_code;?>');">
                        <?php print $va_facet_info['label_plural'];?>
                    </button>
                <?php endforeach; ?>

                <div id="splashBrowsePanel">
                    <div id="splashBrowsePanelContent"></div>
                </div>
            </div>
            <div class="panel-footer clearfix">
                <button type="button" class="btn btn-default pull-right" data-toggle="collapse" data-target="#searchRefineBox">
                    <span class="glyphicon glyphicon-collapse-up"></span>
                    <?php print _t('Hide'); ?>
                </button>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted"><?php _t('No applicable filters'); ?></p>
    <?php endif; ?>
</div>
