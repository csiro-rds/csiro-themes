<?php
$va_log_entry = $this->getVar('log_entry');
?>
<small>
<ul>
    <?php foreach ($va_log_entry as $va_change_list): ?>
        <?php foreach ($va_change_list['changes'] as $va_change): ?>
            <li>
                <?php
                switch ($va_change_list['changetype']) {
                    case 'I':        // insert (aka add)
                        print _t('Added %1 to \'%2\'', $va_change['description'], $va_change['label']);
                        break;
                    case 'U':    // update
                        print _t('Updated %1 to \'%2\'', $va_change['label'], $va_change['description']);
                        break;
                    case 'D':    // delete
                        print _t('Deleted %1', $va_change['label']);
                        break;
                    default:        // unknown type - should not happen
                        print _t('Unknown change type \'%1\'', $va_change['changetype']);

                }
                ?>
            </li>
        <?php endforeach; ?>
    <?php endforeach; ?>
</ul>
</small>
