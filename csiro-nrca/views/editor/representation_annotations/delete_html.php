<div class="component component-delete">
    <?php if (!$this->getVar('confirmed')): ?>
        <?php print caDeleteWarningBox($this->request, $this->getVar('t_subject'), $this->getVar('subject_name'), 'editor/representation_annotations', 'RepresentationAnnotationEditor', 'Edit/'.$this->request->getActionExtra(), array('annotation_id' => $this->getVar('subject_id'))); ?>
    <?php endif; ?>
</div>
