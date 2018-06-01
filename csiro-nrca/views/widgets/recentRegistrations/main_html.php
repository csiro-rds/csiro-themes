<?php
$po_request = $this->getVar('request');
$va_users = $this->getVar('users_list');
$vb_any_unmoderated = array_filter($va_users, function ($va_user_info) {
    return !$va_user_info['moderated_on'];
});
?>
<div class="widget widget-recent-registrations">
    <?php if (sizeof($va_users) > 0): ?>
        <h3><?php print _t("%1 recent %2 user registrations", $this->getVar("limit"), $this->getVar("registration_type")); ?></h3>
        <form id="registrationListForm">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?php print _t('Select'); ?></th>
                    <th><?php print _t('User'); ?></th>
                    <th><?php print _t('Email'); ?></th>
                    <th><?php print _t('Registered'); ?></th>
                    <th><?php print _t('Status'); ?></th>
                    <th><?php print _t('Preferences'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($va_users as $va_user_info): ?>
                    <tr>
                        <td>
                            <?php if (!$va_user_info["active"]): ?>
                                <input type="checkbox" name="user_id[]" value="<?php print $va_user_info['user_id']; ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print $va_user_info["fname"]; ?>
                            <?php print $va_user_info["lname"]; ?>
                        </td>
                        <td>
                            <?php print $va_user_info["email"]; ?>
                        </td>
                        <td>
                            <a href="<?php print caEditorUrl($po_request, $va_user_info['table_num'], $va_user_info['row_id']); ?>">
                                <?php print $va_user_info["registered_on"]; ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($va_user_info["active"]): ?>
                                <?php print _t('Active'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print join('<br />', $va_user_info["user_preferences"]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($vb_any_unmoderated): ?>
                <div>
                    <a href="#" onclick="jQuery('#registrationListForm').attr('action', '<?php print caNavUrl($po_request, 'administrate/access', 'Users', 'Approve'); ?>').submit();" class='btn btn-success'>
                        <span class="glyphicon glyphicon-ok"></span>
                        <?php print _t('Approve'); ?>
                    </a>
                </div>
                <input type="hidden" name="mode" value="dashboard" />
            <?php endif; ?>
        </form>
    <?php else: ?>
        <?php print _t('There are no recent registrations'); ?>
    <?php endif; ?>
</div>
