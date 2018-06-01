<?php
$po_request = $this->getVar('request');
$va_settings = $this->getVar('settings');
$vs_widget_id = $this->getVar('widget_id');
$va_login_list = $this->getVar('login_list');
?>
<table class="table table-striped widget widget-recent-changes">
    <thead>
    <tr>
        <th class="text-nowrap"><?php print _t('Date/time');?></th>
        <th class="text-nowrap"><?php print _t('User');?></th>
        <th class="text-nowrap"><?php print _t('IP address');?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($va_login_list as $vn_i => $va_login): ?>
        <tr>
            <td><?php print date("n/d/y, g:iA T", $va_login['date_time']); ?></td>
            <td><?php print $va_login['fname']; ?> <?php print $va_login['lname']; ?> (<?php print $va_login['username']; ?>)</td>
            <td><?php print $va_login['ip']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
