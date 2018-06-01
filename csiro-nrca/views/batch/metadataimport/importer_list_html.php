<?php
$va_importer_list = $this->getVar('importer_list');
?>
<div class="sectionBox">
    <?php if(!$this->request->isAjax()): ?>
        <div class="list-filter">
            <label><?php _t('Filter') ?>:</label>
            <input type="text" name="filter" value="" onkeyup="$('#caItemList').caFilterTable(this.value); return false;"/>
        </div>
        <button type="button" id="caOpenImportersButton" class="btn btn-primary" onclick="caAddImporterUploadArea(true, true)">
            <span class="glyphicon glyphicon-add"></span>
            <?php print _t('Add importers'); ?>
        </button>
        <button type="button" id="caImportersButton" class="btn btn-danger" onclick="caCloseImporterUploadArea(false, true)">
            <span class="glyphicon glyphicon-remove"></span>
            <?php print _t('Close'); ?>
        </button>
        <div id="batchProcessingTableProgressGroup" style="display: none;">
            <div class="batchProcessingStatus"><span id="batchProcessingTableStatus"> </span></div>
            <div id="progressbar"></div>
        </div>

        <div id="importerUploadArea" style="display: none">
            <span><?php print _t("Drag importer worksheets here to add or update"); ?></span>
        </div>
    <?php endif; ?>
        <div id="caImporterListContainer">
            <table id="caItemList" class="table">
                <thead>
                <tr>
                    <th>
                        <?php _p('Name'); ?>
                    </th>
                    <th>
                        <?php _p('Code'); ?>
                    </th>
                    <th>
                        <?php _p('Type'); ?>
                    </th>
                    <th>
                        <?php _p('Mapping'); ?>
                    </th>
                    <th>
                        <?php _p('Last modified'); ?>
                    </th>
                    <th class="{sorter: false} list-header-nosort">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php if(sizeof($va_importer_list) == 0): ?>
                    <tr>
                        <td colspan='6'>
                            <div align="center"><?php print _t('No importers defined'); ?></div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($va_importer_list as $va_importer): ?>
                        <tr>
                            <td>
                                <?php print $va_importer['label']; ?>
                            </td>
                            <td>
                                <?php print $va_importer['importer_code']; ?>
                            </td>
                            <td>
                                <?php print $va_importer['importer_type']; ?>
                            </td>
                            <td>
                                <?php print $va_importer['worksheet'] ? caNavButton($this->request, __CA_NAV_ICON_DOWNLOAD__, _t("Download"), '', 'batch', 'MetadataImport', 'Download', array('importer_id' => $va_importer['importer_id']), array(), array('icon_position' => __CA_NAV_BUTTON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)) : '' ; ?>
                            </td>
                            <td>
                                <?php print caGetLocalizedDate($va_importer['last_modified_on'], array('dateFormat' => 'delimited')); ?>
                            </td>
                            <td class="listtableEditDelete">
                                <?php print caNavButton($this->request, __CA_NAV_ICON_GO__, _t("Import data"), '', 'batch', 'MetadataImport', 'Run', array('importer_id' => $va_importer['importer_id']), array(), array('icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)); ?>
                                <?php print caNavButton($this->request, __CA_NAV_ICON_DELETE__, _t("Delete"), '', 'batch', 'MetadataImport', 'Delete', array('importer_id' => $va_importer['importer_id']), array(), array('icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
<?php if (!$this->request->isAjax()): ?>
</div>
    <script>
        var batchCookieJar = $.cookieJar('caCookieJar');

        function caOpenImporterUploadArea(open, animate) {
            batchCookieJar.set('importerUploadAreaIsOpen', open);
            if (open) {
                $("#importerUploadArea").slideDown(animate ? 150 : 0);
                $("#caCloseImportersButton").show();
                $("#caAddImportersButton").hide();
            } else {
                $("#importerUploadArea").slideUp(animate ? 150 : 0);
                $("#caCloseImportersButton").hide();
                $("#caAddImportersButton").show();
            }
        }

        function caImporterUploadAreaIsOpen() {
            return batchCookieJar.get('importerUploadAreaIsOpen');
        }

        (function($) {
            $(function() {
                <?php if(!$this->request->isAjax()): ?>
                    $('#caItemList').caFormatListTable();
                <?php endif; ?>
                $('#progressbar').progressbar({ value: 0 });

                $('#importerUploadArea').fileupload({
                    dataType: 'json',
                    url: '<?php print caNavUrl($this->request, 'batch', 'MetadataImport', 'UploadImporters'); ?>',
                    dropZone: $('#importerUploadArea'),
                    singleFileUploads: false,
                    done: function (e, data) {
                        if (data.result.error) {
                            $("#batchProcessingTableProgressGroup").show(250);
                            $("#batchProcessingTableStatus").html(data.result.error);
                            setTimeout(function() {
                                $("#batchProcessingTableProgressGroup").hide(250);
                            }, 3000);
                        } else {
                            var msg = [];

                            if (data.result.uploadMessage) {
                                msg.push(data.result.uploadMessage);
                            }
                            if (data.result.skippedMessage) {
                                msg.push(data.result.skippedMessage);
                            }
                            $("#batchProcessingTableStatus").html(msg.join('; '));
                            setTimeout(function() {
                                $("#batchProcessingTableProgressGroup").hide(250);
                                $("#importerUploadArea").show(150);
                            }, 3000);
                        }
                        $("#caImporterListContainer").load("<?php print caNavUrl($this->request, 'batch', 'MetadataImport', 'Index'); ?>");
                    },
                    progressall: function (e, data) {
                        $("#importerUploadArea").hide(150);
                        if ($("#batchProcessingTableProgressGroup").css('display') == 'none') {
                            $("#batchProcessingTableProgressGroup").show(250);
                        }
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progressbar').progressbar("value", progress);

                        var msg = "<?php print _t("Progress: "); ?>%1";
                        $("#batchProcessingTableStatus").html(msg.replace("%1", caUI.utils.formatFilesize(data.loaded) + " (" + progress + "%)"));
                    }
                });

                caOpenImporterUploadArea(batchCookieJar.get('importerUploadAreaIsOpen'), false);
            });
        })(jQuery);
    </script>
<?php endif; ?>
