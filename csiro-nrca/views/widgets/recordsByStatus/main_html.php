<?php
$va_item_list = $this->getVar('item_list');
?>
<div class="widget widget-records-by-status">
    <?php if (sizeof($va_item_list) > 0): ?>
        <h3><?php print _t("Showing %1 %2 with status %3", sizeof($va_item_list), $this->getVar('table_display'), $this->getVar('status_display')); ?></h3>
        <ul>
            <?php foreach($va_item_list as $vn_id => $va_record): ?>
                <li>
                    <a href="<?php print caEditorUrl($this->getVar('request'), $this->getVar('table_num'), $vn_id); ?>">
                        <?php print ($va_record["display"] ?: '['._t("BLANK").']'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php print _t('No records to display.'); ?>
    <?php endif; ?>
</div>
