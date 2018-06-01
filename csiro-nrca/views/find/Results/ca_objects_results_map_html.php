<?php
$vo_result = $this->getVar('result');
$vn_num_hits = $this->getVar('num_hits');

$va_map_stats = array();
if ($vo_result && $this->request->config->get('ca_objects_map_attribute')) {
    // TODO FIXME hardcoded inline width and height
    $o_map = new GeographicMap(740, 450, 'map2');

    // map_stats is an array with two keys: 'points' = number of unique markers; 'items' = number of results hits than were plotted at least once on the map
    $va_map_stats = $o_map->mapFrom(
        $vo_result,
        $this->request->config->get('ca_objects_map_attribute'),
        array(
            'ajaxContentUrl' => caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getMapItemInfo'),
            'request' => $this->request,
            'checkAccess' => $this->getVar('access_values')
        )
    );
}

AssetLoadManager::register("maps");
?>

<div class="component component-results-map">
    <?php if ($vo_result && $this->request->config->get('ca_objects_map_attribute')): ?>
        <?php if ($va_map_stats['points'] > 0): ?>
            <?php // TODO FIXME hardcoded inline width and height ?>
            <div id="map2" style="width: 740px; height: 450px;"></div>
            <div><?php $o_map->render('HTML', array('delimiter' => "<br/>")); ?></div>
        <?php else: ?>
            <p class="text-muted"><?php print _t('It is not possible to show a map of the results because none of the items found have map coordinates.'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    (function ($) {
        'use strict';

        <?php if ($vo_result && $this->request->config->get('ca_objects_map_attribute') && $va_map_stats['points'] > 0): ?>
            $('.searchNav').html('<?php print ($va_map_stats['items'] < $vn_num_hits ?
                _t("%1 of %2 results have been mapped.  To see all results chose a different display.", $va_map_stats['items'], $vn_num_hits) :
                _t("Found %1 results.", $va_map_stats['items'])); ?>');
        <?php endif; ?>
    }(jQuery));
</script>
