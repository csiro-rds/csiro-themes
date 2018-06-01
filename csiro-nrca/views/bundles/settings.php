<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vs_form = $this->getVar('t_subject')->getHTMLSettingForm(array('id' => $this->getVar('id_prefix'), 'placement_code' => $this->getVar('placement_code')))
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-settings">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, array()); ?>
    <div class="bundleContainer">
        <div class="item-list">
            <?php print $vs_form ?: _t('No settings'); ?>
        </div>
    </div>
</div>
