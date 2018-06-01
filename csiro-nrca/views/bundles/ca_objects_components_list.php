<?php
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vs_display_template = caGetOption('displayTemplate', $va_settings, $t_subject->getAppConfig()->get('ca_objects_component_display_settings'));
$vb_read_only = isset($va_settings['readonly']) && $va_settings['readonly'];
$qr_components = $t_subject->getComponents(array( 'returnAs' => 'searchResult' ));
$vn_num_components = $qr_components ? $qr_components->numHits() : 0;
?>
<div id="<?php print $this->getVar('placement_code') . $this->getVar('id_prefix'); ?>" class="component component-bundle component-bundle-objects-components-list">
    <div class="bundleContainer">
        <div class="item-list">
            <?php if ($vn_num_components): ?>
                <div class="row">
                    <?php while ($qr_components->nextHit()): ?>
                        <div class="col-md-4">
                            <?php print $qr_components->getWithTemplate($vs_display_template); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-muted">
                    <?php print _t('No components defined'); ?>
                </div>
            <?php endif; ?>
            <div class="text-right">
                <button type="button" class="add top-right" title="<?php print _t('Add another component'); ?>" onclick="caObjectComponentPanel.showPanel('<?php print caNavUrl($this->request, '*', 'ObjectComponent', 'Form', array('parent_id' => $t_subject->getPrimaryKey())); ?>'); return false;">
                    <?php print $this->getVar('add_label') ?: _t('Add component'); ?>
                    <span class="glyphicon glyphicon-plus"></span>
                </button>
            </div>
        </div>
    </div>
</div>