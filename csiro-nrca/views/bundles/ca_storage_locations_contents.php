<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$qr_result = $this->getVar('qr_result');
?>
<div id="<?php print $vs_id_prefix.$t_subject->tableNum().'_rel'; ?>">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.$t_subject->tableNum().'_rel', $va_settings); ?>
    <div class="bundleContainer">
        <div class="item-list">
            <?php if ($qr_result && $qr_result->numHits() > 0): ?>
                <?php while ($qr_result->nextHit()): ?>
                    <div class="well well-sm <?php print ($va_settings['list_format'] === 'list' ? 'listRel' : 'roundedRel'); ?>" style="<?php print (isset($va_settings['colorItem']) && $va_settings['colorItem'] ? "background-color: #{$va_settings['colorItem']};" : ''); ?>">
                        <?php print $qr_result->getWithTemplate($va_settings['displayTemplate']); ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="labelInfo"><table><tr><td><?php print _t('Location is empty'); ?></td></tr></table></div>
            <?php endif; ?>
        </div>
    </div>
</div>
