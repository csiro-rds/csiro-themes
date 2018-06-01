<?php
AssetLoadManager::register("directoryBrowser");
$vs_id = $this->getVar('id');
?>
<div class="component component-directory-browser">
    <div id="<?php print $vs_id; ?>directoryBrowser"></div>
    <?php print caHTMLHiddenInput($vs_id, array('value' => '', 'id' => $vs_id)); ?>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            caUI.initDirectoryBrowser('<?php print $vs_id; ?>directoryBrowser', {
                levelDataUrl: '<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'GetDirectoryLevel'); ?>',
                initDataUrl: '<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'GetDirectoryAncestorList'); ?>',
                openDirectoryIcon: '<span class="glyphicon glyphicon-menu-right"></span>',
                disabledDirectoryIcon: '<span class="glyphicon glyphicon-ban-circle"></span>',
                folderIcon: '<span class="glyphicon glyphicon-folder-open"></span>',
                fileIcon: '<span class="glyphicon glyphicon-file"></span>',
                displayFiles: true,
                allowFileSelection: false,
                initItemID: '<?php print $this->getVar('defaultPath'); ?>',
                indicator: '<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>', // TODO FIXME
                currentSelectionDisplayID: 'browseCurrentSelection',
                onSelection: function(item_id, path, name, type) {
                    if (type === 'DIR') {
                        $('#<?php print $vs_id; ?>').val(path);
                    }
                }
            });
        });
    }(jQuery));
</script>
