<?php
$vn_num_items_total = $this->getVar('num_items_total');
$vn_num_items_rendered = $this->getVar('num_items_rendered');
$t_item = $this->getVar('t_item');
?>
<?php print $this->getVar('viz_html'); ?>
<div class="caMediaOverlayControls">
    <?php print ($vn_num_items_rendered == 1) ? _t("Displaying %1 of %2 %3", $vn_num_items_rendered, $vn_num_items_total, $t_item->getProperty('NAME_SINGULAR')) : _t("Displaying %1 of %2 %3", $vn_num_items_rendered, $vn_num_items_total, $t_item->getProperty('NAME_PLURAL')); ?>
    <div class="close">
        <a href="#" onclick="caMediaPanel.hidePanel(); return false;" title="close"></a>
    </div>
</div>
