<?php
require_once($this->request->getDirectoryPathForThemeFile('helpers/themeHelpers.php'));

$t_subject = $this->getVar('t_subject');
$va_restrict_to_types = $this->getVar('restrict_to_types');
$vs_field_name_prefix = $this->getVar('field_name_prefix');
$vs_n = $this->getVar('n');

$o_dm = Datamodel::load();
$vs_type_name_singular = preg_replace('/\s+/', '', ucwords($o_dm->getTableProperty($t_subject->tableNum(), 'NAME_SINGULAR')));
$vs_quick_add_name = $vs_type_name_singular . 'QuickAdd';
$vs_editor_name = $vs_type_name_singular . 'Editor';
$vs_module_path = ($this->getVar('moduleDirectory') ?: 'editor') . '/' . preg_replace('/\s+/', '_', $o_dm->getTableProperty($t_subject->tableNum(), 'NAME_PLURAL'));
$vs_form_name = $vs_type_name_singular . 'QuickAddForm';

$va_bundle_list = array();
$va_form_elements = $t_subject->getBundleFormHTMLForScreen(
    $this->getVar('screen'),
    array(
        'request' => $this->request,
        'restrictToTypes' => array($t_subject->get('type_id')),
        'formName' => $vs_form_name . $vs_field_name_prefix . $vs_n,
        'forceLabelForNew' => $this->getVar('forceLabel')
    ),
    $va_bundle_list
);

$vs_type_list_element = $t_subject->getTypeListAsHTMLFormElement(
    'change_type_id',
    array(
        'id' => "{$vs_form_name}TypeID{$vs_field_name_prefix}{$vs_n}",
        'onchange' => "window.caQuickAddFormHandler.switchForm();"
    ),
    array(
        'value' => $t_subject->get('type_id'),
        'restrictToTypes' => $va_restrict_to_types
    )
);
?>

<div class="modal-content">
    <form action="#" class="quickAddSectionForm" name="<?php print $vs_form_name; ?>" method="POST" enctype="multipart/form-data" id="<?php print $vs_form_name . $vs_field_name_prefix . $vs_n; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span class="glyphicon glyphicon-remove"></span>
            </button>
            <h4 class="modal-title">Quick Add</h4>
        </div>
        <div class="modal-body">
            <div class="quickAddErrorContainer" id="<?php print $vs_form_name; ?>Errors<?php print $vs_field_name_prefix . $vs_n; ?>"></div>
            <div class="quickAddSectionBox" id="<?php print $vs_form_name; ?>Container<?php print $vs_field_name_prefix . $vs_n; ?>">
                <div class="bundles-container">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Type of <?php print ucwords($o_dm->getTableProperty($t_subject->tableNum(), 'NAME_SINGULAR')); ?></label>
                            <?php print $vs_type_list_element; ?>
                        </div>
                    </div>
                    <?php foreach (groupFormElementsByBundle($va_bundle_list, $va_form_elements) as $va_group): ?>
                        <div class="row">
                            <?php print join("\n", $va_group); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="_formName" value="<?php print $vs_form_name . $vs_field_name_prefix . $vs_n; ?>"/>
                <input type="hidden" name="q" value="<?php print htmlspecialchars(caUcFirstUTF8Safe($this->getVar('q')), ENT_QUOTES, 'UTF-8'); ?>"/>
                <input type="hidden" name="screen" value="<?php print htmlspecialchars($this->getVar('screen')); ?>"/>
                <input type="hidden" name="types" value="<?php print htmlspecialchars(is_array($va_restrict_to_types) ? join(',', $va_restrict_to_types) : ''); ?>"/>
            </div>
        </div>
        <div class="modal-footer">
            <div class="pull-left hidden">
                <i class="fa fa-cog fa-spin"></i>
                Loading
            </div>
            <?php if ($t_subject->isSaveable($this->request)): ?>
                <div class="btn-group pull-right">
                    <button type="button" id="<?php print $vs_form_name; ?>TypeID<?php print $vs_field_name_prefix . $vs_n; ?>Add" class="btn btn-success" onclick="window.caQuickAddFormHandler.save(event);">
                        <span class="glyphicon glyphicon-plus"></span>
                        Add <?php print $t_subject->getTypeName() ?>
                    </button>
                    <button type="button" id="<?php print $vs_form_name; ?>TypeID<?php print $vs_field_name_prefix . $vs_n; ?>Cancel" class="btn btn-default" onclick="$('#' + window.formId).parents('div.modal').modal('hide');">
                        <span class="glyphicon glyphicon-remove"></span>
                        Cancel
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    (function ($) {
        'use strict';

        $(function () {
            var headerText = '<?php print addslashes('Quick add ' . $t_subject->getTypeName()); ?>';
            var saveText = '<?php print addslashes('Created ' . $t_subject->getTypeName() . ': %1'); ?>';

            window.formId = '<?php print $vs_form_name . $vs_field_name_prefix . $vs_n; ?>';
            window.caQuickAddFormHandler = caUI.initQuickAddFormHandler({
                formID: window.formId,
                formErrorsPanelID: '<?php print $vs_form_name; ?>Errors<?php print $vs_field_name_prefix . $vs_n; ?>',
                formTypeSelectID: '<?php print $vs_form_name; ?>TypeID<?php print $vs_field_name_prefix . $vs_n; ?>',
                formUrl: '<?php print caNavUrl($this->request, $vs_module_path, $vs_quick_add_name, 'Form'); ?>',
                fileUploadUrl: '<?php print caNavUrl($this->request, $vs_module_path, $vs_editor_name, "UploadFiles"); ?>',
                saveUrl: '<?php print caNavUrl($this->request, $vs_module_path, $vs_quick_add_name, "Save"); ?>',
                headerText: headerText,
                saveText: saveText,
                busyIndicator: '<?php print addslashes(caBusyIndicatorIcon($this->request)); ?>',
                progressClassName: 'progressClassName',
                parentSelector: '.modal-content',
                onSave: function (response) {
                    var $form = $("#" + window.formId);
                    var $dialog = $form.parents('div.modal-dialog');
                    if (response.status === 0) {
                        var relationbundle = $dialog.data('relationbundle');
                        $('#' + $dialog.data('autocompleteInputID')).val(response.display);
                        $('#' + $dialog.data('autocompleteItemIDID')).val(response.id);
                        $('#' + $dialog.data('autocompleteTypeIDID')).val(response.type_id);

                        if (relationbundle) {
                            relationbundle.select(null, response);
                        }

                        $form.parents('div.modal').modal('hide');
                        caUI.addNotification('<?php print __NOTIFICATION_TYPE_INFO__; ?>', saveText.replace('%1', response.display));
                    } else {
                        var $errorsContainer = $('#<?php print $vs_form_name; ?>Errors<?php print $vs_field_name_prefix . $vs_n; ?>');
                        $.each(response.errors, function (error) {
                            caUI.addNotification('<?php print __NOTIFICATION_TYPE_ERROR__; ?>', error, $errorsContainer);
                        });
                    }
                }
            });
        });
    }(jQuery));
</script>
