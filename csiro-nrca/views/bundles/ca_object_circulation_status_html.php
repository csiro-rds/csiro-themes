<?php
$t_subject = $this->getVar('t_subject');
$vs_id_prefix = $this->getVar('id_prefix');
$va_checkout_status = $t_subject->getCheckoutStatus(array('returnAsArray' => true));
$va_reservations = $t_subject->getCheckoutReservations();
$vn_num_reservations = sizeof($va_reservations);
$va_reservation_users = array_map(function($reservation) {
    return $reservation['user_name'];
}, $va_reservations);
?>

<div class="bundleContainer" id="<?php print $vs_id_prefix; ?>">
	<div class="caItemList">
		<div class="labelInfo">
            <div class="pull-left">
                <?php print $t_subject->htmlFormElement('circulation_status_id', null, ['name' => $this->getVar('placement_code') . $vs_id_prefix.'ca_object_circulation_status']); ?>
            </div>
            <?php if ($t_subject->canBeCheckedOut() && $va_checkout_status): ?>
                <div>
                    <label><?php print _t('Status'); ?></label>
                    <?php print $va_checkout_status['status_display']; ?>
                </div>
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
                <?php if (in_array($vn_status, array(__CA_OBJECTS_CHECKOUT_STATUS_RESERVED__, __CA_OBJECTS_CHECKOUT_STATUS_OUT_WITH_RESERVATIONS__))): ?>
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
            <?php else: ?>
                <div class="alert alert-info">
                    <?php print _t('Cannot be checked out') ?>
                </div>
            <?php endif; ?>
		</div>
	</div>
</div>
