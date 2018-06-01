<?php
$va_notification_list = $this->getVar('notification_list');
$vs_mark_as_read_url = caNavUrl($this->getVar('request'), 'manage', 'Notifications', 'markAsRead');
?>
<div class="widget widget-notifications">
    <?php if (!is_array($va_notification_list) || !sizeof($va_notification_list)): ?>
        <?php print _t("You have no new notifications"); ?>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php print _t('Date/time'); ?></th>
                <th><?php print _t('Message'); ?></th>
                <th><?php print _t('Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($va_notification_list as $vn_notification_id => $va_notification): ?>
                <tr>
                    <td>
                        <?php print date("n/d/y, g:iA", $va_notification['datetime']); ?>
                    </td>
                    <td id="notificationWidgetMessage<?php print $vn_notification_id; ?>">
                        <?php print $va_notification['message']; ?>
                    </td>
                    <td>
                        <a href="#" onclick="jQuery.get('<?php print $vs_mark_as_read_url; ?>', { subject_id: '<?php print $va_notification['subject_id']; ?>' }); jQuery('#notificationWidgetMessage<?php print $vn_notification_id; ?>').parent().hide();">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php print _t("Read"); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
