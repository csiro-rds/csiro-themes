<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_table_num = $this->getVar('table_num');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$va_history = $this->getVar('checkout_history');
$vn_checkout_count = $this->getVar('checkout_count');
$va_client_list = $this->getVar('client_list');
$vn_client_count = $this->getVar('client_count');
$vs_add_label = $this->getVar('add_label') ?: _t('Update location');
$va_checkout_status = $t_subject->getCheckoutStatus(array('returnAsArray' => true));
$vn_status = $va_checkout_status['status'];
$va_reservations = $t_subject->getCheckoutReservations();
$va_reservation_users = array_map(function($reservation) {
    return $reservation['user_name'];
}, $va_reservations);
?>
<div id="<?php print $vs_id_prefix; ?>">
	<div class="bundleContainer">
        <div id="<?php print $vs_id_prefix; ?>Container" class="editorHierarchyBrowserContainer">
            <?php //TODO: convert to bootstrap tabs ?>
            <div  id="<?php print $vs_id_prefix; ?>Tabs">
                <ul>
                    <li><a href="#<?php print $vs_id_prefix; ?>Tabs-status"><span><?php print _t('Current status'); ?></span></a></li>
                    <li><a href="#<?php print $vs_id_prefix; ?>Tabs-history"><span><?php print _t('History'); ?></span></a></li>
                </ul>
                <div id="<?php print $vs_id_prefix; ?>Tabs-status" class="hierarchyBrowseTab">
                    <?php if ($t_subject->canBeCheckedOut() && $va_checkout_status): ?>
                        <div>
                            <label><?php print _t('Status'); ?></label>
                            <?php print $va_checkout_status['status_display']; ?>
                        </div>
                        <?php if (in_array($vn_status, array(__CA_OBJECTS_CHECKOUT_STATUS_OUT__, __CA_OBJECTS_CHECKOUT_STATUS_OUT_WITH_RESERVATIONS__))): ?>
                            <div>
                                <label><?php print _t('Borrowed by'); ?></label>
                                <?php print _t('%1 on %2', $va_checkout_status['user_name'], $va_checkout_status['checkout_date']); ?>
                            </div>
                            <?php if ($va_checkout_status['due_date']): ?>
                                <div>
                                    <label><?php print _t('Due on'); ?></label>
                                    <?php print _t('%1', $va_checkout_status['due_date']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($va_checkout_status['checkout_notes']): ?>
                                <div>
                                    <label><?php print _t('Notes'); ?></label>
                                    <?php print _t('%1', $va_checkout_status['checkout_notes']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (in_array($vn_status, array(__CA_OBJECTS_CHECKOUT_STATUS_RESERVED__, __CA_OBJECTS_CHECKOUT_STATUS_OUT_WITH_RESERVATIONS__))): ?>
                            <?php print _t("<strong>Reservations:</strong> %1", $vn_num_reservations = sizeof($va_reservations)); ?>
                            <?php if ($vn_num_reservations > 0): ?>
                                <div>
                                    <label><?php print _t("Reservations"); ?></label>
                                    <?php print $vn_num_reservations; ?>
                                </div>
                                <?php if ($vn_num_reservations > 0): ?>
                                    <div>
                                        <label><?php print _t("Reserved for"); ?></label>
                                        <?php print join(", ", $va_reservation_users); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?php print _t('Cannot be checked out'); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="<?php print $vs_id_prefix; ?>Tabs-history" class="hierarchyBrowseTab caLocationHistoryTab">
                    <h2>
                        <?php print ($vn_checkout_count != 1) ? _t('Checked out %1 times', $vn_checkout_count) : _t('Checked out %1 time', $vn_checkout_count); ?>
                        <?php print ($vn_client_count != 1) ? _t('by %1 clients', $vn_client_count) : _t('by %1 client', $vn_client_count); ?>
                    </h2>
                    <table class='table table-bordered'>
                        <thead>
                        <tr>
                            <th><?php print _t('User'); ?></th>
                            <th><?php print _t('Check out'); ?></th>
                            <th><?php print _t('Check out notes'); ?></th>
                            <th><?php print _t('Due'); ?></th>
                            <th><?php print _t('Returned'); ?></th>
                            <th><?php print _t('Return notes'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($va_history as $va_event): ?>
                                <tr>
                                    <td>
                                        <?php print $va_event['user_name']; ?>
                                    </td>
                                    <td>
                                        <?php print $va_event['checkout_date']; ?>
                                    </td>
                                    <td>
                                        <?php print $va_event['checkout_notes']; ?>
                                    </td>
                                    <td>
                                        <?php print $va_event['due_date']; ?>
                                    </td>
                                    <td>
                                        <?php print $va_event['return_date']; ?>
                                    </td>
                                    <td>
                                        <?php print $va_event['return_notes']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            $("#<?php print $vs_id_prefix; ?>Tabs").tabs({ selected: 0 });
        });
    })(jQuery);
</script>
