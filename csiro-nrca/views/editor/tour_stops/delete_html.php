<div class="component component-delete">
    <?php if (!$this->getVar('confirmed')): ?>
        <?php print caDeleteWarningBox($this->request, $this->getVar('t_subject'), $this->getVar('subject_name'), 'editor/tour_stops', 'TourStopEditor', 'Edit/'.$this->request->getActionExtra(), array('stop_id' => $this->getVar('subject_id'))); ?>
    <?php endif; ?>
</div>
