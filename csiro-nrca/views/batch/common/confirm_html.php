<?php
AssetLoadManager::register("panel");

$vb_queue_enabled = (bool)$this->request->config->get('queue_enabled');
$va_last_settings = $this->getVar('batch_mediaimport_last_settings');
$vs_email = trim($this->request->user->get('email'));
$vs_sms = trim($this->request->user->get('sms_number'));
$vb_enable_sms = (bool)$this->request->config->get('enable_sms_notifications');

$vs_checked_send_mail = $va_last_settings['sendMail'] ? 'checked' : '';
$vb_checked_send_sms = $va_last_settings['sendSMS'] ? 'checked' : '';
$vs_checked_run_background = isset($va_last_settings['runInBackground']) && $va_last_settings['runInBackground'] ? 'checked' : '';
?>

<div id="caConfirmBatchExecutionPanel" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php print $this->getVar('confirm_title'); ?></h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <span class="glyphicon glyphicon-warning-sign"></span>
                    <?php print _t($this->getVar('confirm_message')); ?>
                </div>
                <div class="row">
                    <?php if ($vb_queue_enabled): ?>
                        <div class="col-md-12">
                            <input type="checkbox" id="caRunInBackground" value="1" <?php print $vs_checked_run_background ?>>
                            <?php print _t('Process in background'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($vs_email): ?>
                        <div class="col-md-12">
                            <input type="checkbox" id="caSendEmailWhenDone" value="1" <?php print $vs_checked_send_mail ?>>
                            <label for="caSendEmailWhenDone" class="control-label">
                                <?php print _t('Send email to '); ?><strong><?php print $vs_email ?></strong><?php print _t(' when done'); ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <?php if ($vs_sms && $vb_enable_sms): ?>
                        <div class="col-md-12">
                            <input type="checkbox" id="caSendSMSWhenDone" value="1" <?php print $vb_checked_send_sms; ?>>
                            <label for="caSendSMSWhenDone" class="control-label">
                                <?php print _t('Send SMS to ') ?><strong><?php print $vs_sms ?></strong><?php print _t(' when done'); ?>
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox" id="caSendEmailWhenDone" value="1" <?php print $vs_checked_send_mail ?>>
                            <label for="caSendEmailWhenDone" class="control-label">
                                <?php print _t('Send Email to ') ?><strong><?php print $vs_email ?></strong><?php print _t(' when done'); ?>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="btn-group">
                    <button type="button" id="BatchExecutionButton" class="btn btn-danger" onclick="caConfirmBatchExecutionPanel.hidePanel()">
                        <span class="glyphicon glyphicon-ban-circle"></span>
                        <?php print _t('Cancel') ?>
                    </button>
                    <button type="button" id="BatchExecutionButton" class="btn btn-primary" onclick="caExecuteBatch()">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?php print _t('Execute') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var caConfirmBatchExecutionPanel, caExecuteBatch;
    (function($) {
        'use strict';

        $(function() {
            var batchPanelId = 'caConfirmBatchExecutionPanel';
            if (caUI.initPanel) {
                caConfirmBatchExecutionPanel = caUI.initPanel({
                    panelID: batchPanelId,
                    panelContentID: 'ContentArea',
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function () {
                        $('#' + batchPanelId).modal('show');
                    },
                    onCloseCallback: function () {
                        $('#' + batchPanelId).modal('hide');
                    }
                });
            }
            caExecuteBatch = function() {
                $("#caBatch<?php print $this->request->getController(); ?>Form").submit();
            }
        });
    })(jQuery);
</script>
