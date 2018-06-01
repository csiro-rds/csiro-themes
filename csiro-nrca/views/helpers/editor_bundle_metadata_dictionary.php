<?php
$ps_id_prefix = $this->getVar('id_prefix');
?>
<div class="component component-editor-bundle-metadata-dictionary">
    <button type="button" class="caMetadataDictionaryDefinitionToggle" id="<?php print $ps_id_prefix; ?>MetadataDictionaryToggleButton" onclick="caBundleVisibilityManager.toggleDictionaryEntry('<?php print $ps_id_prefix; ?>'); return false;">
        <span class="glyphicon glyphicon-info-sign"></span>
    </button>
    <div id="<?php print $ps_id_prefix; ?>DictionaryEntry" class="caMetadataDictionaryDefinition">
        <?php print $this->getVar('definition'); ?>
    </div>
    <script>
        (function ($) {
            'use strict';

            $(function () {
                caBundleVisibilityManager.registerBundle('<?php print $ps_id_prefix; ?>');
            });
        }(jQuery));
    </script>
</div>
