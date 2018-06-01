<?php
AssetLoadManager::register("panel");
$t_item = $this->getVar('t_item');
$vb_queue_enabled = (bool)$this->request->config->get('queue_enabled');
$vs_change_type_html = _t('Change type from %1 to %2',
    $t_item->getTypeName(),
    $t_item->getTypeListAsHTMLFormElement('new_type_id',
        array('id' => 'caChangeTypeFormTypeID'),
        array('childrenOfCurrentTypeOnly' => false,
            'directChildrenOnly' => false,
            'returnHierarchyLevels' => true,
            'access' => __CA_BUNDLE_ACCESS_EDIT__)
    ));
$vs_change_type_warning = _t(
    '<strong>Warning:</strong> changing the %1 type will cause information in all fields not applicable to the new type to be discarded. This action cannot be undone.',
$t_item->getProperty('NAME_SINGULAR')
);
$va_opts = array('id' => 'caRunBatchInBackground', 'value' => 1);
$vs_email = trim($this->request->user->get('email'));
$vs_sms = trim($this->request->user->get('sms_number'));
$va_opts = array('id' => 'caSendSMSWhenDone', 'value' => 1);
if (isset($va_last_settings['runInBackground']) && $va_last_settings['runInBackground']) {
    $va_opts['checked'] = 1;
}
if (isset($va_last_settings['sendMail']) && $va_last_settings['sendMail']) {
    $va_opts['checked'] = 1;
}
if (isset($va_last_settings['sendSMS']) && $va_last_settings['sendSMS']) {
    $va_opts['checked'] = 1;
}

?>

<div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal">
    <div id="caRelationQuickAddPanel<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
</div>
<div id="caTypeChangePanel" class="modal fade" data-toggle="modal" role="dialog">
	<div id="caTypeChangePanelContentArea" class="modal-dialog modal-lg">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span class="glyphicon glyphicon-remove"></span>
            </button>
            <h4 class="modal-title">
                <?php print _t('Change %1 type', $t_item->getProperty('NAME_SINGULAR')); ?>
            </h4>
        </div>
        <div class="modal-body">
            <?php print caFormTag($this->request, 'ChangeType', 'caChangeTypeForm', null, $ps_method='post', 'multipart/form-data', '_top', array()); ?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign"></span>
                <?php print $vs_change_type_warning; ?>
            </div>
            <div class="alert alert-info">
                <span class="glyphicon glyphicon-info-sign"></span>
                <?php print $vs_change_type_html; ?>
            </div>
            <div class="row">
                <?php if ($vb_queue_enabled): ?>
                    <div class="col-md-2">
                        <?php print caHTMLCheckboxInput('run_in_background', $va_opts); ?>
                    </div>
                    <div class="col-md-2">
                        <?php print _t('Process in background'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($vs_email): ?>
                    <div class="col-md-2">
                        <?php $va_opts = array('id' => 'caSendEmailWhenDone', 'value' => 1); ?>
                        <?php if (isset($va_last_settings['sendMail']) && $va_last_settings['sendMail']): ?>
                            $va_opts['checked'] = 1;
                        <?php endif; ?>
                        <?php print caHTMLCheckboxInput('send_email_when_done', $va_opts); ?>
                    </div>
                    <div class="col-md-2">
                        <?php print _t('Send email to '); ?>
                        <strong><?php print $vs_email; ?></strong>
                        <?php print _t(' when done'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($vs_sms && (bool)$this->request->config->get('enable_sms_notifications')): ?>
                    <div class="col-md-2">
                        <?php print caHTMLCheckboxInput('send_sms_when_done', array('id' => 'caSendSMSWhenDone', $va_opts)); ?>
                    </div>
                    <div class="col-md-2">
                        <?php print _t('Send SMS to '); ?>
                        <strong><?php print $vs_sms; ?></strong>
                        <?php print _t(' when done'); ?>
                    </div>
                <?php endif; ?>
                <div class="clearfix">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" id="caChangeTypeForm">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php print _t('Save'); ?>
                        </button>
                    </div>
                    <div class="pull-left">
                        <button type="button" class="btn btn-danger" onclick="caTypeChangePanel.hidePanel();">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php print _t('Cancel'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php print caHTMLHiddenInput('set_id', array('value' => $this->getVar('set_id'))); ?>
            </form>
        </div>
	</div>
</div>
<script>
    var caTypeChangePanel;
    (function ($) {
        'use strict';

        $(function () {
            if (caUI.initPanel) {
                caTypeChangePanel = caUI.initPanel({
                    panelID: "caTypeChangePanel", /* DOM ID of the <div> enclosing the panel */
                    panelContentID: "caTypeChangePanelContentArea", /* DOM ID of the content area <div> in the panel */
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function () {
                        $('#caTypeChangePanel').modal('show');
                    },
                    onCloseCallback: function () {
                        $('#caTypeChangePanel').modal('hide');
                    }
                });
            }
        });
    }(jQuery));
</script>
