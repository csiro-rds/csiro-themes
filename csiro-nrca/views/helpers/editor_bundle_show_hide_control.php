<span class="helper helper-editor-bundle-show-hide-control">
    <a href="#" onclick="caBundleVisibilityManager.toggle('<?php print $this->getVar('id_prefix'); ?>'); return false;" class="visibility pull-right">
        <span class="glyphicon glyphicon-triangle-bottom" id="<?php print $this->getVar('id_prefix'); ?>VisToggleButton"></span>
    </a>
    <span class="text-muted preview" id="<?php print $this->getVar('preview_id_prefix'); ?>_BundleContentPreview">
        <?php print $this->getVar('preview_init'); ?>
    </span>
</span>
<script>
    (function ($) {
        'use strict';

        $(function() {
            caBundleVisibilityManager.registerBundle('<?php print $this->getVar('id_prefix'); ?>', '<?php print $this->getVar('force'); ?>');
        });
    }(jQuery));
</script>
