<?php
$t_subject = $this->getVar('t_subject');
$vn_row = $this->getVar('row');
$vn_col = $this->getVar('col');

$va_bundle_list = array();
$va_form_elements = $t_subject->getBundleFormHTMLForScreen(
    null,
    array(
        'request' => $this->request,
        'formName' => 'complex',
        'bundles' => $this->getVar('bundles'),
        'dontAllowBundleShowHide' => true
    ),
    $va_bundle_list
);
?>

<div class="ajax-results ajax-results-editable-complex-data-form">
    <?php print caFormTag($this->request, '#', 'caEditableResultsComplexDataForm', null, 'POST', 'multipart/form-data', null, array('disableUnsavedChangesWarning' => true, 'disableSubmit' => true)); ?>
        <div id="errors hidden"></div>
        <div class="btn-group">
            <button type="button" class="btn btn-default" onclick="jQuery('#caEditableResultsComplexDataForm').parent().parent().data('panel').hidePanel();">
                <span class="glyphicon glyphicon-remove"></span>
                <?php print _t('Cancel'); ?>
            </button>
            <button type="button" class="btn btn-success" onclick="caEditableResultsComplexDataFormHandler.save(event);">
                <span class="glyphicon glyphicon-ok"></span>
                <?php print _t('Save'); ?>
            </button>
        </div>
        <div class="section-box">
            <?php foreach (groupFormElementsByBundle($va_bundle_list, $va_form_elements) as $va_group): ?>
                <div class="row">
                    <?php print join("\n", $va_group); ?>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="id" value="<?php print $t_subject->getPrimaryKey(); ?>" />
            <input type="hidden" name="bundle" value="<?php print $this->getVar('bundle'); ?>" />
            <input type="hidden" name="row" value="<?php print $vn_row; ?>" />
            <input type="hidden" name="col" value="<?php print $vn_col; ?>" />
        </div>
    </form>
</div>

<script>
    var caEditableResultsComplexDataFormHandler;

    (function ($) {
        'use strict';

        $(function () {
            caEditableResultsComplexDataFormHandler = caUI.initQuickAddFormHandler({
                formID: 'caEditableResultsComplexDataForm',
                formErrorsPanelID: 'caEditableResultsComplexDataFormErrors',
                formTypeSelectID: null,
                formUrl: '<?php print caNavUrl($this->request, '*', '*', 'resultsComplexDataEditor'); ?>',
                fileUploadUrl: '<?php print caNavUrl($this->request, "*", "*", "saveResultsEditorFiles"); ?>',
                saveUrl: '<?php print caNavUrl($this->request, "*", "*", "saveResultsEditorData"); ?>',
                headerText: '<?php print addslashes(_t('Edit %1', $t_subject->getTypeName())); ?>',
                saveText: '<?php print addslashes(_t('Updated %1 ', $t_subject->getTypeName())); ?> <em>%1</em>',
                busyIndicator: '<?php print addslashes(caBusyIndicatorIcon($this->request)); ?>',
                onSave: function(resp) {
                    if (resp.status === 0) {
                        $(".component-results-editable .results-editor-content")
                            .data('handsontable')
                            .setDataAtCell(<?php print (int)$vn_row; ?>, <?php print (int)$vn_col; ?>, resp.display, 'external');

                        if ($("#caEditableResultsComplexDataForm") && $("#caEditableResultsComplexDataForm").parent() && $("#caEditableResultsComplexDataForm").parent().parent() && $("#caEditableResultsComplexDataForm").parent().parent().data("panel")) {
                            $("#caEditableResultsComplexDataForm").parent().parent().data("panel").hidePanel();
                        }

                        $('.results-editor-status').html('Saved changes').show();
                        setTimeout(function() {
                            $('.results-editor-status').fadeOut(500);
                        }, 5000);
                    } else {
                        caEditableResultsComplexDataFormHandler.setErrors(resp.errors);
                        $("#caEditableResultsComplexDataForm input[name=form_timestamp]").val(resp.time);
                    }
                }
            });
        });
    }(jQuery));
</script>
