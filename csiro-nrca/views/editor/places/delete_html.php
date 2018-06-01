<div class="component component-delete">
    <?php if (!$this->getVar('confirmed')): ?>
        <?php print caDeleteWarningBox($this->request, $this->getVar('t_subject'), $this->getVar('subject_name'), 'editor/places', 'PlaceEditor', 'Edit/'.$this->request->getActionExtra(), array('place_id' => $this->getVar('subject_id'))); ?>
    <?php endif; ?>
</div>
