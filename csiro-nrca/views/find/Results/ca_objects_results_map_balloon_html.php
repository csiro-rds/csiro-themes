<div class="component component-results-map-balloon">
    <?php foreach($this->getVar('ids') as $vn_id): ?>
        <?php
        $t_object = new ca_objects($vn_id);
        $va_rep = $t_object->getPrimaryRepresentation(array('thumbnail'), null,  array('checkAccess' => $this->getVar('access_values')));
        ?>
        <div>
            <a href="<?php print caNavUrl($this->request, 'editor/objects', 'ObjectEditor', 'Edit', array('object_id' => $t_object->get('ca_objects.object_id'))); ?>">
                <?php print $va_rep['tags']['thumbnail']; ?>
                <strong><?php print $t_object->get('ca_objects.idno'); ?></strong>:
                <?php print $t_object->get('ca_objects.preferred_labels'); ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>
