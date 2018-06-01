<?php
$va_instances = $this->getVar('instances');
?>
<table class="table table-striped widget widget-count">
    <thead>
    <tr>
        <th><?php print _t('Table'); ?></th>
        <th><?php print _t('Count'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->getVar('counts') as $vs_table => $vn_count): ?>
        <tr>
            <td><?php print _t(ucwords($va_instances[$vs_table]->getProperty('NAME_PLURAL'))); ?></td>
            <td><?php print $vn_count; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
