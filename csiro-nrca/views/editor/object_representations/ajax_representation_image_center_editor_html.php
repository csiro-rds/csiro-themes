<?php
$t_rep = $this->getVar('t_subject');
$vn_representation_id = $this->getVar('subject_id');

$vb_can_edit = $t_rep->isSaveable($this->request);
$vb_can_delete = $t_rep->isDeletable($this->request);

$vn_image_width = $this->getVar('image_width');
$vn_image_height = $this->getVar('image_height');

$vn_center_x_pixel = floor($vn_image_width * $this->getVar('center_x'));
$vn_center_y_pixel = floor($vn_image_height * $this->getVar('center_y'));

$t_media = new Media();
$vs_mime_type = $t_rep->getMediaInfo('media', 'original', 'MIMETYPE');
$vs_media_type = $t_media->getMimetypeTypename($vs_mime_type);
?>

<div class="caMediaOverlayControls">
    <div class="objectInfo"><?php print "{$vs_media_type}; ".caGetRepresentationDimensionsForDisplay($t_rep, 'original'); ?></div>
    <button type="button" class='close' onclick="caMediaPanel.hidePanel();" title="close">
        <span class="glyphicon glyphicon-remove"></span>
    </button>
</div>

<div class="text-center">
    <span id="caObjectRepresentationSetCenterMarker" class="centreMarker glyphicon glyphicon-screenshot"></span>
    <?php print $this->getVar('image'); ?>
</div>

<script>
    (function($) {
        'use strict';

        $(function() {
            $("#caObjectRepresentationSetCenterMarker")
                .css({top: <?php print $vn_center_y_pixel; ?> + "px", left: <?php print $vn_center_x_pixel; ?> + "px"})
                .draggable({'containment' : 'parent'});
        });
    })(jQuery);
</script>
