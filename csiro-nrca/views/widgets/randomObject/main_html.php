<div class="widget widget-random-object text-center">
    <?php if ($this->getVar('object_id')): ?>
        <a href="<?php print caEditorUrl($this->getVar('request'), 'ca_objects', $this->getVar('object_id')); ?>">
            <?php print $this->getVar('image'); ?>
            <br/>
            <?php print $this->getVar('label'); ?>
        </a>
    <?php else: ?>
        <em><?php print _t('No object to display.'); ?></em>
    <?php endif; ?>
</div>
