<?php
$po_request = $this->getVar('request');

$vn_threshold_in_seconds = ($this->getVar('threshold_in_hours') * 3600);
$vn_end_date_for_display = time();
$vn_start_date_for_display = $vn_end_date_for_display - $vn_threshold_in_seconds;

$o_tep = new TimeExpressionParser();
$o_tep->setUnixTimestamps($vn_start_date_for_display, $vn_end_date_for_display);
$vn_displayed_date_range = $o_tep->getText(array( 'timeOmit' => true ));

// $va_log_entries is a list containing lists of changes performed by a user as a unit (at a single instant in time).
// We grab the date & time, user name and other stuff out of the first entry in the list (index 0) because
// these don't vary from change to change in a unit, and the list is always guaranteed to have at least one entry
$va_log_entries = array_reverse($this->getVar('change_log')->getRecentChanges($this->getVar('table_num'), $vn_threshold_in_seconds, 1000)); // reverse to put most recent up top
?>
<div class="widget widget-recent-changes">
    <?php if (sizeof($va_log_entries) > 0): ?>
        <h3><?php print _t("Changes to <strong>%1</strong> from %2", $this->getVar('table_name_plural'), $vn_displayed_date_range); ?></h3>
        <table class="table table-striped">
        <thead>
        <tr>
            <th>Object</th>
            <th>User</th>
            <th>Date/Time</th>
            <th>Details</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($va_log_entries as $vs_log_key => $va_log_entry): ?>
                <tr>
                    <td>
                        <a href="<?php print caEditorUrl($po_request, $va_log_entry[0]['subject_table_num'], $va_log_entry[0]['subject_id']); ?>">
                            <?php print $va_log_entry[0]['subject']; ?>
                        </a>
                    </td>
                    <td>
                        <?php print $va_log_entry[0]['user_fullname']; ?>
                    </td>
                    <td>
                        <?php print $va_log_entry[0]['datetime']; ?>
                    </td>
                    <td>
                        <a href="#" id="more<?php print $vs_log_key; ?>" class="btn btn-default btn-sm show-details" onclick="jQuery('#more<?php print $vs_log_key; ?>').hide(); jQuery('#hide<?php print $vs_log_key; ?>').show(); jQuery('#changes<?php print $vs_log_key; ?>').slideDown(250); return false;">
                            <span class="glyphicon glyphicon-eye-open"></span>
                            Show
                        </a>
                        <a href="#" id="hide<?php print $vs_log_key; ?>" class="btn btn-default btn-sm hide-details" onclick="jQuery('#more<?php print $vs_log_key;?>').show(); jQuery('#hide<?php print $vs_log_key;?>').hide(); jQuery('#changes<?php print $vs_log_key; ?>').slideUp(250); return false;">
                            <span class="glyphicon glyphicon-eye-close"></span>
                            Hide
                        </a>
                    </td>
                </tr>
                <tr><!-- Dummy to force the stripes to work correctly --></tr>
                <tr>
                    <td colspan="4" id="changes<?php print $vs_log_key; ?>" class="changes-list-container">
                        <ul class="list-striped">
                            <?php foreach($va_log_entry as $va_change_list): ?>
                                <?php foreach($va_change_list['changes'] as $va_change): ?>
                                    <li>
                                        <?php
                                        switch($va_change_list['changetype']) {
                                            case 'I':
                                                print _t('Added %1 to "%2"', $va_change['description'], $va_change['label']);
                                                break;
                                            case 'U':
                                                print _t('Updated %1 to "%2"', $va_change['label'], $va_change['description']);
                                                break;
                                            case 'D':
                                                print _t('Deleted %1', $va_change['label']);
                                                break;
                                            default:
                                                print _t('Unknown change type "%1"', $va_change['changetype']);
                                        }
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    <?php else: ?>
        <?php print _t("There have been no changes to <strong>%1</strong> from %2.", $this->getVar('table_name_plural'), $vn_displayed_date_range); ?>
    <?php endif; ?>
</div>
