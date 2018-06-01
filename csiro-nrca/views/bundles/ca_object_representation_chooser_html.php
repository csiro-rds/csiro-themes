<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_subject = $this->getVar('t_subject');
$t_object = $this->getVar('t_object');
$va_settings = $this->getVar('settings');
$va_reps = $t_object->getRepresentations();
$vb_read_only = (bool)caGetOption('readonly', $va_settings, false);
$vs_element_code = $this->getVar('element_code');
$va_selected_rep_ids = $t_subject->get($x=$t_subject->tableName().".".$vs_element_code, array('returnAsArray' => true, 'idsOnly' => true));
if (!is_array($va_selected_rep_ids)) {
    $va_selected_rep_ids = array();
}
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-buncle component-bundle-object-representation-chooser">
    <?php if ($vs_element_code): ?>
        <?php foreach($va_reps as $va_rep): ?>
            <?php $va_attributes = array('value' => $va_rep['representation_id']); ?>
            <?php if (in_array($va_rep['representation_id'], $va_selected_rep_ids)): ?>
                <?php $va_attributes['checked'] = 1; ?>
            <?php endif; ?>
            <div>
                <?php print $va_rep['tags']['preview170']; ?>
                <?php if (!$vb_read_only): ?>
                    <?php print caHTMLCheckboxInput("{$vs_id_prefix}[]", $va_attributes); ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php print _t("No metadata element is configured"); ?>
    <?php endif; ?>
</div>
