<?php
$va_parameters = array('lot_id' => $this->getVar('subject_id'));
$vn_num_objects = $t_lot->numObjects();
if ($vn_num_objects > 0) {
    $va_parameters['warning'] = _t('There %1 %2 %3 linked to this lot. If you continue these objects will no longer be associated with a lot.', _t($vn_num_objects === 1 ? 'is' : 'are'), $vn_num_objects, _t($vn_num_objects === 1 ? 'object' : 'objects'));
}
?>
<div class="component component-delete">
    <?php if (!$this->getVar('confirmed')): ?>
        <?php print caDeleteWarningBox($this->request, $this->getVar('t_subject'), $this->getVar('subject_name'), 'editor/object_lots', 'ObjectLotEditor', 'Edit/'.$this->request->getActionExtra(), $va_parameters); ?>
    <?php endif; ?>
</div>
