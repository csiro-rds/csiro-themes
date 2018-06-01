<?php
AssetLoadManager::register("fileupload");

$t_instance = $this->getVar('t_instance');
$o_config = $t_instance->getAppConfig();
$t_rep = $this->getVar('t_rep');
$va_last_settings = $this->getVar('batch_mediaimport_last_settings');
$vs_checked_directory = isset($va_last_settings['includeSubDirectories']) && $va_last_settings['includeSubDirectories'] ? 'checked' : '';
$vs_reltypes = $this->getVar($t_instance->tableName().'_representation_relationship_type');
$vs_checked_add_to_set = isset($va_last_settings['setMode']) && $va_last_settings['setMode'] == 'add' ? 'checked' : '';
$vs_checked_set_mode = isset($va_last_settings['setMode']) && $va_last_settings['setMode'] == 'create' ? 'checked' : '';
$vs_checked_ca_no_set = !((isset($va_last_settings['setMode']) && (in_array($va_last_settings['setMode'], array('add', 'create'))))) ? 'checked' : '';
$vs_checked_representation_form = isset($va_last_settings['representationIdnoMode']) && ($va_last_settings['representationIdnoMode'] == 'form') ? 'checked' : '';
$vs_checked_representation_filename  = isset($va_last_settings['representationIdnoMode']) && ($va_last_settings['representationIdnoMode'] == 'filename') ? 'checked' : '';
$vs_checked_representation_filename_no_ext = isset($va_last_settings['representationIdnoMode']) && ($va_last_settings['representationIdnoMode'] == 'filename_no_ext') ? 'checked' : '';
$vs_checked_representation_directory_and_filename = isset($va_last_settings['representationIdnoMode']) && ($va_last_settings['representationIdnoMode'] == 'directory_and_filename') ? 'checked' : '';
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php print _t('Execute media import?'); ?></h3>
    </div>
    <div class="panel-body text-right">
        <div class="btn-group">
            <a id="caBatchMediaImportFormButton" class="btn btn-danger" href="<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'Index/' . $this->request->getActionExtra(), array()) ?>">
                <span class="glyphicon glyphicon-ban-circle"></span>
                <?php print _t('Cancel'); ?>
            </a>
            <button type="button" id="caBatchMediaImportFormButton" class="btn btn-primary" onclick="caShowConfirmBatchExecutionPanel()">
                <span class="glyphicon glyphicon-ok"></span>
                <?php print _t('OK'); ?>
            </button>
        </div>
    </div>
</div>

<div id="batchProcessingTableProgressGroup hidden">
    <div class="batchProcessingStatus">
        <span id="batchProcessingTableStatus"></span>
    </div>
    <div id="progressbar"></div>
</div>
<div class="sectionBox">
    <?php print caFormTag($this->request, 'Save/'.$this->request->getActionExtra(), 'caBatchMediaImportForm', null, 'POST', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true)); ?>
        <?php print caHTMLHiddenInput('import_target', array('value' => $this->getVar('import_target'))); ?>
        <div class='bundleLabel'>
            <span class="formLabelText"><?php print _t('Import target'); ?></span>
            <div class="bundleContainer">
                <div class="caLabelList">
                    <p>
                        <?php print $this->getVar('import_target'); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bundleLabel">
            <span class="formLabelText"><?php print _t('Directory to import'); ?></span>
            <div class="bundleContainer">
                <div class="caLabelList" >
                    <div id="directoryBrowser" class="directoryBrowser">
                        <!-- Content for directory browser is dynamically inserted here by ca.hierbrowser -->
                    </div>
                    <?php print caHTMLHiddenInput('directory', array('value' => '', 'id' => 'caDirectoryValue')); ?>
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="include_subdirectories" <?php print $vs_checked_directory; ?>>
                        <?php print _t('Include all sub-directories'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="delete_media_on_import" <?php print $vs_checked_directory; ?>>
                        <?php print _t('Delete media after import'); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class='bundleLabel'>
            <span class="formLabelText"><?php print _t('Import mode'); ?></span>
                <div class="bundleContainer">
                    <div class="caLabelList">
                        <p>
                            <?php print $this->getVar('import_mode'); ?>
                        </p>
                    </div>
                </div>
        </div>

        <div class="bundleLabel">
            <span class="formLabelText"><?php print _t('Type'); ?></span>
            <div class="bundleContainer">
                <div class="caLabelList">
                    <table class="table">
                        <tr>
                            <td>
                                <?php print _t('Type used for newly created %1', caGetTableDisplayName($t_instance->tableName(), false))."<br/>\n".$this->getVar($t_instance->tableName().'_type_list')."\n"; ?>
                            </td>
                            <td>
                                <?php print _t('Type used for newly created object representations')."<br/>\n".$this->getVar('ca_object_representations_type_list')."</div>\n"; ?>
                            </td>
                            <?php if($vs_reltypes): ?>
                                <td>
                                    <?php print _t('Type used for relationship'); ?>
                                    <br/>
                                    <?php print $vs_reltypes; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="bundleLabel">
            <span class="formLabelText"><?php print _t('Set'); ?></span>
            <div class="bundleContainer">
                <div class="caLabelList" id="caMediaImportSetControls">
                    <table class="table">
                        <?php if (is_array($this->getVar('available_sets')) && sizeof($this->getVar('available_sets'))): ?>
                            <tr>
                                <td>
                                    <input type="radio" id="caAddToSet" value="add" <?php print $vs_checked_add_to_set; ?>>
                                </td>
                                <td>
                                    <?php print _t('Add imported media to set %1', caHTMLSelect('set_id', $this->getVar('available_sets'), array('id' => 'caAddToSetID', 'class' => 'searchSetsSelect', 'width' => '300px'), array('value' => null, 'width' => '170px'))); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>
                                <input type="radio" id="caCreateSet" value="create" <?php print $vs_checked_set_mode; ?>>
                            </td>
                            <td>
                                <label for="caSetCreateName"><?php print _t('Create set with imported media'); ?></label>
                                <input type="text" id="caSetCreateName" name="set_create_name">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" id="caNoSet" name="set_mode" value="none" <?php print $vs_checked_ca_no_set; ?>>
                            </td>
                            <td>
                                <?php print _t('Do not associate imported media with a set'); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="bundleLabel">
            <span class="formLabelText"><?php print _t('%1 identifier', ucfirst(caGetTableDisplayName($t_instance->tableName(), false))); ?></span>
            <div class="bundleContainer">
                <div class="caLabelList" id="caMediaImportIdnoControls">
                    <table class="table">
                        <tr>
                            <td>
                                <input type="radio" id="caIdnoFormMode" name="idno_mode" value="form" <?php print $vs_checked_representation_form; ?>>
                            </td>
                            <td id="caIdnoFormModeForm">
                                <?php print _t('Set %1 identifier to %2', caGetTableDisplayName($t_instance->tableName(), false),  $t_instance->htmlFormElement('idno', '^ELEMENT', array('request' => $this->request))); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" id="caIdnoFilenameMode" name="idno_mode" value="filename" <?php print $vs_checked_representation_filename; ?>>
                            </td>
                            <td><?php print _t('Set %1 identifier to file name', caGetTableDisplayName($t_instance->tableName(), false)); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" id="caIdnoFilenameNoExtMode" name="idno_mode" value="filename_no_ext" <?php print $vs_checked_representation_filename_no_ext; ?>>
                            </td>
                            <td>
                                <?php print _t('Set %1 identifier to file name without extension', caGetTableDisplayName($t_instance->tableName(), false)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" id="caIdnoDirectoryAndFilenameMode" name="idno_mode" value="directory_and_filename" <?php print $vs_checked_representation_directory_and_filename; ?>>
                            </td>
                            <td>
                                <?php print _t('Set %1 identifier to directory and file name', caGetTableDisplayName($t_instance->tableName(), false)); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="bundleLabel">
            <span class="formLabelText">
                <?php print (($this->getVar('ca_object_representations_mapping_list_count') > 1) || ($this->getVar($t_instance->tableName().'_mapping_list_count') > 1)) ? _t('Status, access &amp; metadata extraction') : _t('Status &amp; access'); ?>
            </span>
            <div class="bundleContainer">
                <div class="caLabelList" >
                    <table class="table">
                        <tr>
                            <td>
                                <?php print _t('Set %1 status to<br/>%2', caGetTableDisplayName($t_instance->tableName(), false), $t_instance->htmlFormElement('status', '', array('name' => $t_instance->tableName().'_status'))); ?>
                                <br/>
                                <?php print _t('Set %1 access to<br/>%2', caGetTableDisplayName($t_instance->tableName(), false), $t_instance->htmlFormElement('access', '', array('name' => $t_instance->tableName().'_access'))); ?>
                                <?php if ($this->getVar($t_instance->tableName().'_mapping_list_count') > 1): ?>
                                    <br/>
                                    <?php print _t('Extract embedded metadata into %1 using mapping<br/>%2', caGetTableDisplayName($t_instance->tableName(), false), $this->getVar($t_instance->tableName().'_mapping_list')); ?> ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php print _t('Set representation status to<br/>%1', $t_rep->htmlFormElement('status', '', array('name' => 'ca_object_representations_status'))); ?>
                                <br/>
                                <?php print _t('Set representation access to<br/>%1', $t_rep->htmlFormElement('access', '', array('name' => 'ca_object_representations_access'))); ?>

                                <?php if ($this->getVar('ca_object_representations_mapping_list_count') > 1): ?>
                                    <br/>
                                    <?php print _t('Extract embedded metadata into representation using mapping<br/>%1', $this->getVar('ca_object_representations_mapping_list')); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="bundleLabel">
            <span id="caBatchMediaAdvancedHeader" class="formLabelText">
                <a href="#" id="caBatchMediaAdvancedHeaderText"><?php print _t("Show advanced options"); ?> &gt;</a>
            </span>
        </div>
        <div id="caBatchMediaAdvancedContent" style="display: none">
            <div class='bundleLabel'>
                <span class="formLabelText"><?php print _t('Matching'); ?></span>
                <div class="bundleContainer">
                    <div class="caLabelList" >
                        <table class="table">
                            <tr>
                                <td>
                                    <?php print $this->getVar('match_mode'); ?>
                                    <br/>
                                    <?php print _t('where identifier %1 value', $this->getVar('match_type')); ?>
                                </td>
                                <td>
                                    <?php print _t("Limit to types"); ?>
                                    <br/>
                                    <?php print $this->getVar($t_instance->tableName().'_limit_to_types_list'); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bundleLabel">
                <span>
                    <?php print _t('%1 identifier', ucfirst(caGetTableDisplayName('ca_object_representations', false))); ?>
                </span>
                <div class="bundleContainer">
                    <div class="caLabelList" id="caMediaImportRepresentationIdnoControls">
                        <table class="table">
                            <tr>
                                <td>
                                    <input type="radio" id="caRepresentationIdnoFormMode" name="representation_idno_mode" value="form" <?php print $vs_checked_representation_form; ?>>
                                </td>
                                <td id="caRepresentationIdnoFormModeForm">
                                    <?php print _t('Set %1 identifier to %2', caGetTableDisplayName('ca_object_representations', false) , $t_rep->htmlFormElement('idno', '^ELEMENT', array('request' => $this->request))); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="caRepresentationIdnoFilenameMode" name="representation_idno_mode" value="filename" <?php print $vs_checked_representation_filename; ?>>
                                </td>
                                <td>
                                    <?php print _t('Set %1 identifier to file name', caGetTableDisplayName('ca_object_representations', false)); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="caRepresentationIdnoFilenameNoExtMode" name="representation_idno_mode" value="filename_no_ext" <?php print $vs_checked_representation_filename_no_ext; ?>>
                                </td>
                                <td>
                                    <?php print _t('Set %1 identifier to file name without extension', caGetTableDisplayName('ca_object_representations', false)); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="caRepresentationIdnoDirectoryAndFilenameMode" name="representation_idno_mode" value="directory_and_filename" <?php print $vs_checked_representation_directory_and_filename; ?>>
                                </td>
                                <td>
                                    <?php print _t('Set %1 identifier to directory and file name', caGetTableDisplayName('ca_object_representations', false)); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class='bundleLabel'>
                <span class="formLabelText"><?php print _t('Relationships'); ?></span>
                    <div class="bundleContainer">
                        <div class="caLabelList">
                            <p class="bundleDisplayPlacementEditorHelpText">
                                <?php print _t('Relationships will be created by matching the identifier extracted from the media file name with identifiers in related records.'); ?>
                            </p>
                            <div>
                                <table class="table">
                                    <?php foreach(array('ca_entities', 'ca_places', 'ca_occurrences', 'ca_collections') as $vs_rel_table): ?>
                                        <?php $t_rel_table = $t_instance->getAppDatamodel()->getInstanceByTableName($vs_rel_table) ?>
                                        <?php if (!$o_config->get("{$vs_rel_table}_disable") && $t_rel_table): ?>
                                            <?php $t_rel = ca_relationship_types::getRelationshipTypeInstance($t_instance->tableName(), $vs_rel_table); ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td>
                                                <?php print caHTMLCheckboxInput('create_relationship_for[]', array('value' => $vs_rel_table,  'id' => "caCreateRelationshipForMedia{$vs_rel_table}", 'onclick' => "$('#caRelationshipTypeIdFor{$vs_rel_table}').prop('disabled', !$('#caCreateRelationshipForMedia{$vs_rel_table}').prop('checked'))"), array('dontConvertAttributeQuotesToEntities' => true)); ?>
                                                <?php print _t("to %1 with relationship type", $t_rel_table->getProperty('NAME_SINGULAR')); ?>
                                            </td>
                                            <td>
                                                <?php print $t_rel->getRelationshipTypesAsHTMLSelect('ltor', null, null, array('name' => "relationship_type_id_for_{$vs_rel_table}", 'id' => "caRelationshipTypeIdFor{$vs_rel_table}", 'disabled' => 1)); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
            <div class='bundleLabel'>
                <span class="formLabelText"><?php print _t('Skip files'); ?></span>
                    <div class="bundleContainer">
                        <div class="caLabelList" >
                            <p class="bundleDisplayPlacementEditorHelpText">
                                <?php print _t('List names of files you wish to skip during import below, one per line. You may use asterisks ("*") as wildcards to make partial matches. Values enclosed in "/" characters will be treated as '); ?>
                                <a href="http://www.pcre.org/pcre.txt" target="_new"><?php print _t('Perl-compatible regular expressions'); ?></a>
                            </p>
                            <p>
                                <?php print caHTMLTextInput('skip_file_list', array('value' => $va_last_settings['skipFileList'],  'id' => "caSkipFilesList"), array('width' => '700px', 'height' => '100px')); ?>
                            </p>
                        </div>
                    </div>
            </div>
            <div class='bundleLabel'>
                <span class="formLabelText"><?php print _t('Miscellaneous'); ?></span>
                <div class="bundleContainer">
                    <div class="caLabelList" >
                        <p>
                            <?php print caHTMLCheckboxInput('allow_duplicate_media', array('value' => 1,  'id' => 'caAllowDuplicateMedia', 'checked' => $va_last_settings['allowDuplicateMedia']), array()); ?>
                            <?php print " "._t('Allow duplicate media?'); ?>
                        </p>
                        <p>
                            <?php print _t('Log level'); ?>
                            <br/>
                            <?php print caHTMLSelect('log_level', caGetLogLevels(), array('id' => 'caLogLevel'), array('value' => $va_last_settings['logLevel'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php print $this->render("mediaimport/confirm_html.php"); ?>
        <?php print $vs_control_box; ?>
    <?php print '</form>'; ?>
</div>
<script>
    var caShowConfirmBatchExecutionPanel, oDirBrowser;
    (function($) {
        'use strict';
        $(function() {
            caShowConfirmBatchExecutionPanel = function() {
                var msg = '<?php print addslashes(_t("You are about to import files from <em>%1</em>")); ?>';
                msg = msg.replace("%1", $('#caDirectoryValue').val());
                caConfirmBatchExecutionPanel.showPanel();
                $('#caConfirmBatchExecutionPanelAlertText').html(msg);
            };

            $(document).bind('drop dragover', function (e) {
                e.preventDefault();
            });

            $("#caIdnoFormMode").click(function() {
                $("#caIdnoFormModeForm input").prop('disabled', false);
            });
            $("#caIdnoFilenameMode").click(function() {
                $("#caIdnoFormModeForm input").prop('disabled', true);
            });
            $("#caIdnoFilenameNoExtMode").click(function() {
                $("#caIdnoFormModeForm input").prop('disabled', true);
            });
            $("#caIdnoDirectoryAndFilenameMode").click(function() {
                $("#caIdnoFormModeForm input").prop('disabled', true);
            });

            $("#caMediaImportIdnoControls").find("input:checked").click();

            $("#caRepresentationIdnoFormMode").click(function() {
                $("#caRepresentationIdnoFormModeForm input").prop('disabled', false);
            });
            $("#caRepresentationIdnoFilenameMode").click(function() {
                $("#caRepresentationIdnoFormModeForm input").prop('disabled', true);
            });
            $("#caRepresentationIdnoFilenameNoExtMode").click(function() {
                $("#caRepresentationIdnoFormModeForm input").prop('disabled', true);
            });
            $("#caRepresentationIdnoDirectoryAndFilenameMode").click(function() {
                $("#caRepresentationIdnoFormModeForm input").prop('disabled', true);
            });

            $("#caMediaImportRepresentationIdnoControls").find("input:checked").click();

            $("#caAddToSet").click(function() {
                $("#caAddToSetID").prop('disabled', false);
                $("#caSetCreateName").prop('disabled', true);
            });
            $("#caCreateSet").click(function() {
                $("#caAddToSetID").prop('disabled', true);
                $("#caSetCreateName").prop('disabled', false);
            });
            $("#caNoSet").click(function() {
                $("#caAddToSetID").prop('disabled', true);
                $("#caSetCreateName").prop('disabled', true);
            });

            $("#caMediaImportSetControls").find("input:checked").click();

            oDirBrowser = caUI.initDirectoryBrowser('directoryBrowser', {
                levelDataUrl: '<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'GetDirectoryLevel'); ?>',
                initDataUrl: '<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'GetDirectoryAncestorList'); ?>',
                openDirectoryIcon: "<?php print caNavIcon(__CA_NAV_ICON_RIGHT_ARROW__, 1); ?>",
                disabledDirectoryIcon: "<?php print caNavIcon(__CA_NAV_ICON_DOT__, 1, array('class' => 'disabled')); ?>",
                folderIcon: "<?php print caNavIcon(__CA_NAV_ICON_FOLDER__, 1); ?>",
                fileIcon: "<?php print caNavIcon(__CA_NAV_ICON_FILE__, 1); ?>",
                displayFiles: true,
                allowFileSelection: false,
                uploadProgressMessage: "<?php print addslashes(_t("Upload progress: %1")); ?>",
                uploadProgressID: "batchProcessingTableProgressGroup",
                uploadProgressBarID: "progressbar",
                uploadProgressStatusID: "batchProcessingTableStatus",
                allowDragAndDropUpload: <?php print is_writable($this->request->config->get('batch_media_import_root_directory')) ? "true" : "false"; ?>,
                dragAndDropUploadUrl: "<?php print caNavUrl($this->request, 'batch', 'MediaImport', 'UploadFiles'); ?>",

                initItemID: '<?php print addslashes($va_last_settings['importFromDirectory']); ?>',
                indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>",
                currentSelectionDisplayID: 'browseCurrentSelection',
                onSelection: function(item_id, path, name, type) {
                    if (type == 'DIR') { $('#caDirectoryValue').val(path); }
                }
            });

            $('#progressbar').progressbar({ value: 0 });

            $("#caBatchMediaAdvancedHeader").click(function(e) {
                e.preventDefault();
                var $content = $("#caBatchMediaAdvancedContent");
                $content.slideToggle(500, function () {
                    $("#caBatchMediaAdvancedHeaderText").text(function () {
                        return $content.is(":visible") ? "< <?php print _t("Hide advanced options"); ?>" : "<?php print _t("Show advanced options"); ?> >";
                    });
                });
                // scroll down so that you can actually see the advanced section after expanding
                $('html, body').animate({
                    scrollTop: 1000
                }, 1000);
            });
        });
    })(jQuery);
</script>
