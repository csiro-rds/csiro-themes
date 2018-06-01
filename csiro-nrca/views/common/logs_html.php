<?php
$o_log = new ApplicationChangeLog();
$va_log = $o_log->getChangeLogForRow($this->getVar('t_subject'), array( 'user_id' => $this->request->getUserID() ));
$vn_id = $this->getVar('subject_id');
?>
<div class="logs">
    <div class="well">
        <div class="input-group">
            <label for="filter" class="input-group-addon"><?php print _t('Filter'); ?></label>
            <input name="filter" id="filter" value="" placeholder="<?php print _t('Filter the change log'); ?>" onkeyup="jQuery('#caLog').caFilterTable(this.value); return false;" class="form-control" />
        </div>
    </div>
    <h1>Change Log</h1>
    <?php if ($va_log): ?>
        <table class="table table-striped" id="<?php print $vn_id; ?>">
            <thead>
            <tr>
                <th class="list-header-unsorted">
                    <?php print _t('Date'); ?>
                </th>
                <th class="list-header-unsorted">
                    <?php print _t('User'); ?>
                </th>
                <th class="list-header-unsorted">
                    <?php print _t('Changes'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($va_log) as $vn_unit_id => $va_log_entries): ?>
                <?php
                $vs_user_name = trim($va_log_entries[0]['user_fullname']);
                $vs_user_email = trim($va_log_entries[0]['user_email']);
                ?>
                <tr>
                    <td>
                        <?php print $va_log_entries[0]['datetime']; ?>
                    </td>
                    <td>
                        <?php if ($vs_user_email): ?>
                            <a href="mailto:<?php print $vs_user_email; ?>">
                                <?php print $vs_user_name; ?>
                            </a>
                        <?php else: ?>
                            <?php print ($vs_user_name ?: '-'); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <?php foreach ($va_log_entries as $va_log_entry): ?>
                                <?php foreach ($va_log_entry['changes'] as $va_change): ?>
                                    <li>
                                        <span class="text-muted">
                                            <?php print $va_log_entry['changetype_display']; ?>
                                            <?php print $va_change['label']; ?>:
                                        </span>
                                        <?php print $va_change['description']; ?>
                                        <?php if (isset($va_change['rel_typename'])): ?>
                                            (<?php print $va_change['rel_typename']; ?>)
                                        <?php endif; ?>
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
        <p><?php print _t('No change log available'); ?></p>
    <?php endif; ?>
</div>

<script>
    (function ($) {
        'use strict';

        $(function () {
            $("#<?php print $vn_id; ?>").caFormatListTable();
        });
    }(jQuery));
</script>
