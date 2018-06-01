<?php
$t_importer = $this->getVar('t_importer');
$va_last_settings = $this->getVar('last_settings');
$vs_importer_list = ca_data_importers::getImporterListAsHTMLFormElement('importer_id', null, array('id' => 'caImporterList', 'onchange' => 'caSetBatchMetadataImportFormState(true);'), array('value' => $t_importer->getPrimaryKey()));
$vs_inputFormat = ca_data_importers::getInputFormatListAsHTMLFormElement('inputFormat', array('id' => 'caInputFormatList', 'onchange' => 'caSetBatchMetadataImportFormState(true);'));
$vs_file_browser_radio_checked = caGetOption('fileInput', $va_last_settings, 'file') === 'file' ? 'checked' : '';
$vs_file_import_radio_checked = caGetOption('fileInput', $va_last_settings, 'file') === 'import' ? 'checked' : '';
$vs_dry_run = $va_last_settings['dryRun'] == 1 ? 'checked' : '';
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php print _t('Execute data import?'); ?></h3>
    </div>
    <div class="panel-body text-right">
        <div class="btn-group">
            <a class="btn btn-danger" href="<?php print caNavUrl($this->request, 'batch', 'MetadataImport', 'Index', array()) ?>">
                <span class="glyphicon glyphicon-ban-circle"></span>
                <?php print _t('Cancel'); ?>
            </a>
            <button type="button" id="caBatchMetadataImportFormButton" class="btn btn-primary" onclick="caShowConfirmBatchExecutionPanel()">
                <span class="glyphicon glyphicon-ok"></span>
                <?php print _t('OK'); ?>
            </button>
        </div>
    </div>
</div>

<div class="sectionBox">
    <?php print caFormTag($this->request, 'ImportData/'.$this->request->getActionExtra(), 'caBatchMetadataImportForm', null, 'POST', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true)); ?>
		<div class='bundleLabel'>
			<span class="formLabelText"><?php print _t('Importer'); ?></span>
			<div class="bundleContainer">
				<div class="caLabelList" >
					<p>
                        <?php print $vs_importer_list ?>
                    </p>
				</div>
			</div>
		</div>
		<div class="bundleLabel">
			<span class="formLabelText"><?php print _t('Data format'); ?></span>
			<div class="bundleContainer">
				<div class="caLabelList">
                    <?php print $vs_inputFormat; ?>
                    <label id="caImportAllDatasetsContainer">
                        <input type="checkbox" id="caImportAllDatasets" value="1">
                        <?php print _t('Import all data sets'); ?>
                    </label>
				</div>
			</div>
		</div>
		<div class="bundleLabel" id="caSourceFileContainer">
			<label>
                <?php print _t('Data file'); ?>
            </label>
			<div class="bundleContainer">
				<div class="caLabelList">
                    <table class="table">
                        <tr>
                            <td>
                                <input type="radio" id="caFileInputRadio" value="file" onclick="caSetBatchMetadataImportFormState()" <?php print $vs_file_browser_radio_checked; ?>>
                            </td>
                            <td>
                                <?php print _t('Data format'); ?>
                                <span id="caFileInputContainer">
                                    <input type="file" name="sourceFile" id="caSourceFile"/>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" id="caFileBrowserRadio" value="import" onclick="caSetBatchMetadataImportFormState()" <?php print $vs_file_import_radio_checked; ?>>
                            </td>
                            <td>
                                <?php print _t('From a file'); ?>
                                <span id="caFileInputContainer">
                                    <input type="file" name="sourceFile" id="caSourceFile">
                                </span>
                            </td>
                            <td>
                                <?php print _t('From the import directory'); ?>
                                <div id="caFileBrowserContainer">
                                    <?php print $this->getVar('file_browser'); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
				</div>
			</div>
		</div>
		<div class='bundleLabel' id="caSourceUrlContainer">
			<span><?php print _t('Data URL'); ?></span>
			<div class="bundleContainer">
				<div class="caLabelList">
					<p>
                        <?php print caHTMLTextInput('sourceUrl', array('id' => 'caSourceUrl', 'class' => 'urlBg'), array()); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="bundleLabel" id="caSourceTextContainer">
			<span class="formLabelText"><?php print _t('Data as text'); ?></span>
			<div class="bundleContainer">
				<div class="caLabelList">
					<p>
                        <?php print caHTMLTextInput('sourceText', array('id' => 'caSourceText'), array('width' => '600px', 'height' => 3)); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="bundleLabel">
			<span class="formLabelText">
                <?php print _t('Log level'); ?>
            </span>
			<div class="bundleContainer">
				<div class="caLabelList">
					<p>
                        <?php print caHTMLSelect('logLevel', caGetLogLevels(), array('id' => 'caLogLevel'), array('value' => $va_last_settings['logLevel'])); ?>
					</p>
				</div>
			</div>
		</div>
		<div class='bundleLabel'>
			<span class="formLabelText"><?php print _t('Testing options'); ?></span>
			<div class="bundleContainer">
				<div class="caLabelList" >
					<p class="formLabelPlain">
                        <input type="checkbox" id="caDryRun" name="dryRun" <?php print $vs_dry_run; ?>
                        <label>
                            <?php print _t('Dry run'); ?>
                        </label>
                    </p>
				</div>
			</div>
		</div>
        <?php print $this->render("metadataimport/confirm_html.php"); ?>
    <?php print '</form>'; ?>
</div>
<?php print $vs_control_box; ?>

<script>
    var caShowConfirmBatchExecutionPanel, caSetBatchMetadataImportFormState, caDataReaderInfo, caImporterInfo;
    (function($) {
        'use strict';

        $(function() {
            caShowConfirmBatchExecutionPanel = function() {
                var msg = '<?php print addslashes(_t("You are about to import data using the <em>%1</em> importer")); ?>';
                msg = msg.replace("%1", $("#caImporterList option:selected").text())

                caConfirmBatchExecutionPanel.showPanel();
                $('#caConfirmBatchExecutionPanelAlertText').html(msg);
            };

            $(document).bind('drop dragover', function (e) {
                e.preventDefault();
            });

            caDataReaderInfo = <?php print json_encode(ca_data_importers::getInfoForAvailableInputFormats()); ?>;
            caImporterInfo = <?php print json_encode(ca_data_importers::getImporters(null, ['dontIncludeWorksheet' => true])); ?>;
            caSetBatchMetadataImportFormState = function(dontAnimate) {
                var info;
                var currentFormat = $("#caInputFormatList").val();
                var animationDuration = dontAnimate ? 0 : 150;
                // Set format list
                var relevantFormats = [];
                var curImporterID = $("#caImporterList").val();
                if (caImporterInfo[curImporterID]) {
                    relevantFormats = caImporterInfo[curImporterID]['settings']['inputFormats'];
                }

                var opts = [];
                var formatInfo = {};
                for(var reader in caDataReaderInfo) {
                    for(var i in relevantFormats) {
                        if(relevantFormats.hasOwnProperty(i)) {
                            if ($.inArray(relevantFormats[i].toLowerCase(), caDataReaderInfo[reader]['formats']) > -1) {
                                formatInfo[relevantFormats[i].toLowerCase()] = caDataReaderInfo[reader];
                                opts.push("<option value='" + relevantFormats[i] + "'>" + caDataReaderInfo[reader]['displayName']+ "</option>");
                                break;
                            }
                        }
                    }
                }

                $("#caInputFormatList").html(opts.join("\n")).val(currentFormat);

                currentFormat = $("#caInputFormatList").val();
                if(!currentFormat) { currentFormat = relevantFormats[0]; $("#caInputFormatList").val(currentFormat); }

                // Set visibility of source input field based upon format
                if (info = formatInfo[currentFormat.toLowerCase()]) {
                    switch(info['inputType']) {
                        case 0:
                        default:
                            // file
                            $('#caSourceUrlContainer').hide(animationDuration);
                            $('#caSourceUrl').prop('disabled', true);
                            $('#caSourceFileContainer').show(animationDuration);
                            $('#caSourceFile').prop('disabled', false);
                            $('#caSourceTextContainer').hide(animationDuration);
                            $('#caSourceText').prop('disabled', true);
                            break;
                        case 1:
                            // url
                            $('#caSourceUrlContainer').show(animationDuration);
                            $('#caSourceUrl').prop('disabled', false);
                            $('#caSourceFileContainer').hide(animationDuration);
                            $('#caSourceFile').prop('disabled', true);
                            $('#caSourceTextContainer').hide(animationDuration);
                            $('#caSourceText').prop('disabled', true);
                            break;
                        case 2:
                            // text
                            $('#caSourceUrlContainer').hide(animationDuration);
                            $('#caSourceUrl').prop('disabled', true);
                            $('#caSourceFileContainer').hide(animationDuration);
                            $('#caSourceFile').prop('disabled', true);
                            $('#caSourceTextContainer').show(animationDuration);
                            $('#caSourceText').prop('disabled', false);
                            break;
                    }

                    if (info['hasMultipleDatasets']) {
                        $('#caImportAllDatasetsContainer').show(animationDuration);
                    } else {
                        $('#caImportAllDatasetsContainer').hide(animationDuration);
                    }
                }

                if ($("#caFileInputRadio").is(":checked")) {
                    $("#caFileInputContainer").show(animationDuration);
                    $("#caFileBrowserContainer").hide(animationDuration);
                } else {
                    $("#caFileInputContainer").hide(animationDuration);
                    $("#caFileBrowserContainer").show(animationDuration);
                }
            };
            caSetBatchMetadataImportFormState(true);
        });
    })(jQuery);
</script>
