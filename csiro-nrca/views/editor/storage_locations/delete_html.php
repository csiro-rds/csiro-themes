<div class="component component-delete">
    <?php if (!$this->getVar('confirmed')): ?>
        <?php print caDeleteWarningBox($this->request, $this->getVar('t_subject'), $this->getVar('subject_name'), 'editor/storage_locations', 'StorageLocationEditor', 'Edit/'.$this->request->getActionExtra(), array('location_id' => $this->getVar('subject_id'))); ?>
    <?php endif; ?>
</div>
