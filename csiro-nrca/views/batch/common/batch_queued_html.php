<?php
AssetLoadManager::register("sortableUI");
$batch_type = $this->getVar('batchType');
$vb_get_email = (bool)$this->request->getParameter('send_email_when_done', pInteger);
$vb_get_sms = (bool)$this->request->getParameter('send_sms_when_done', pInteger);
$vs_sms_email_text = '';
if($vb_get_email || $vb_get_sms) {
    $vs_sms_email_text = _t('You will receive an ' . $vb_get_email ? 'email' : '' . $vb_get_email && $vb_get_sms ? ' and an ' : '' .
    $vb_get_sms ? 'SMS' : '' . 'text message when the %1 processing is complete.', $batch_type);
}
?>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php print _t('%1 for background processing', $batch_type); ?></h3>
    </div>
    <div class="panel-body">
        <?php print $vs_sms_email_text; ?>
    </div>
</div>
