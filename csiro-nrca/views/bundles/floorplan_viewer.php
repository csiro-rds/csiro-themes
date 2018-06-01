<div id="caMediaOverlayContent" class="component component-floorplan-viewer">
	<?php print $this->getVar('viewer'); ?>
</div>	

<div class="caMediaOverlayControls">
    <a href="#" onclick="caMediaPanel.hidePanel(); return false;" title="close" class="close pull-right">
        <?php print caNavIcon(__CA_NAV_ICON_CLOSE__, "18px", [], ['color' => 'white']); ?>
    </a>
    <?php print _t('Editing floor plan for <em>%1</em>', $this->getVar('target_name')); ?>
</div>
