<?php
$va_item_list = $this->getVar('item_list');
?>
<div class="widget widget-recently-created">
    <?php if (sizeof($va_item_list) > 0): ?>
        <h3><?php print _t('%1 recently created %2', sizeof($va_item_list), $this->getVar('table_display')); ?></h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php print _t('Record'); ?></th>
                <th><?php print _t('Created'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($va_item_list as $vn_id => $va_record): ?>
                <tr>
                    <td>
                        <a href="<?php print caEditorUrl($this->getVar('request'), $this->getVar('table_num'), $vn_id); ?>">
                            <?php print (strlen($va_record["display"]) > 0 ? $va_record["display"] : '['._t("BLANK").']'); ?>
                            <?php if ($this->getVar('idno_display')): ?>
                                <?php if (strlen($va_record['idno']) > 0): ?>
                                    [<?php print $va_record["idno"]; ?>]
                                <?php endif; ?>
                                <?php if (strlen($va_record['idno_stub']) > 0): ?>
                                    [<?php print $va_record["idno_stub"]; ?>]
                                <?php endif; ?>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td>
                        <?php print $va_record['datetime']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php print _t('No recently created %1 records found.', $this->getVar('table_display')); ?>
    <?php endif; ?>
</div>
