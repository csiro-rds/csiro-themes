<?php
$va_download_list = $this->getVar('download_list');
$va_tables = $this->getVar('tables');
$va_labels_by_table_num = $this->getVar("labels_by_table_num");
$vs_group_by = $this->getVar("download_list_group_by");
$vs_sort_icons = ' <span class="glyphicon glyphicon-menu-up"></span><span class="glyphicon glyphicon-menu-down"></span>';
?>
<div class="logs logs-download">
    <div class="well">
        <?php print caFormTag($this->request, 'Index', 'downloadLogSearch') ?>
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <label for="search-downloads" class="input-group-addon">
                        <?php print _t('Show from'); ?>
                    </label>
                    <input name="search" id="search-downloads" placeholder="<?php print _t('Enter a date range eg. %1 or %2 - %3', date('Y'), date('Y') - 1, date('Y')); ?>" value="<?php print $this->getVar('download_list_search'); ?>" class="form-control"/>
                    <label class="input-group-addon" for="group_by">
                        <?php print _t('Group By'); ?>
                    </label>
                    <?php print caHTMLSelect('group_by', array(_t('Downloads') => "download", _t('Record') => "record"), array('class' => 'form-control', 'id' => 'group_by'), array('value' => $vs_group_by)) ?>
                    <span class="input-group-btn">
                            <button class="btn btn-primary">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <label class="input-group-addon" for="filter">
                        <?php print _t('Filter'); ?>
                    </label>
                    <input id="filter" value="" placeholder="<?php print _t('Filter the change log'); ?>" onkeyup="$('#caDownloadList').caFilterTable(this.value); return false;" class="form-control"/>
                </div>
            </div>
        </div>
        <?php print '</form>'; ?>
    </div>
    <table id="caDownloadList" class="table table-striped">
        <?php switch ($vs_group_by): ?>
<?php case "record": // Strange indentation because of warning on http://php.net/manual/en/control-structures.alternative-syntax.php (stops fatal error)?>
                <thead>
                <tr>
                    <th class="list-header-unsorted">
                        <?php print _t('Item') . $vs_sort_icons; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Record Type') . $vs_sort_icons; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Num Downloads') . $vs_sort_icons; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Num Users') . $vs_sort_icons; ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if (sizeof($va_download_list)): ?>
                    <?php foreach ($va_download_list as $va_download): ?>
                        <tr>
                            <td>
                                <?php print caEditorLink($this->request, $va_labels_by_table_num[$va_download['info']['table_num']][$va_download['info']['row_id']], '', $va_tables[$va_download['info']['table_num']]['name'], $va_download['info']['row_id'], array()); ?>
                            </td>
                            <td>
                                <?php print $va_tables[$va_download['info']['table_num']]['displayname']; ?>
                            </td>
                            <td>
                                <?php print $va_download['num_downloads']; ?>
                            </td>
                            <td>
                                <?php print (sizeof($va_download['num_logged_in_users'])) ? sizeof($va_download['num_logged_in_users']) . " (logged in)" : ""; ?>
                                <?php print ($va_download['num_anon_users']) ? $va_download['num_anon_users'] . " (anonymous)" : ""; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='9'>
                            <div align="center">
                                <?php print (trim($this->getVar('search_list_search'))) ? _t('No searches found') : _t('Enter a date to display searches from above'); ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <?php break; ?>
            <?php case "download": ?>
            <?php default: ?>
                <thead>
                <tr>
                    <th class="list-header-unsorted">
                        <?php print _t('Date/time') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Record Type') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Item') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('User') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Userclass') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('IP') . $vs_sort_icons;; ?>
                    </th>
                    <th class="list-header-unsorted">
                        <?php print _t('Source') . $vs_sort_icons;; ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if (sizeof($va_download_list)): ?>
                    <?php foreach ($va_download_list as $va_download): ?>
                        <tr>
                            <td>
                                <?php print $va_download['log_datetime']; ?>
                            </td>
                            <td>
                                <?php print $va_tables[$va_download['table_num']]['displayname']; ?>
                            </td>
                            <td>
                                <?php print caEditorLink($this->request, $va_labels_by_table_num[$va_download['table_num']][$va_download['row_id']], '', $va_tables[$va_download['table_num']]['name'], $va_download['row_id'], array()); ?>
                            </td>
                            <td>
                                <?php print $va_download['user_id'] ? $va_download['user_name'] : "anonymous"; ?>
                            </td>
                            <td>
                                <?php print $va_download['userclass']; ?>
                            </td>
                            <td>
                                <?php print $va_download['ip_addr']; ?>
                            </td>
                            <td>
                                <?php print $va_download['download_source']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='9'>
                            <div align="center">
                                <?php print (trim($this->getVar('search_list_search'))) ? _t('No searches found') : _t('Enter a date to display searches from above'); ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <?php break; ?>
            <?php endswitch; ?>
    </table>
</div>
<script>
    (function ($) {
        'use strict';

        $(function () {
            $('#caDownloadList').tablesorter({
                cssAsc: 'list-header-sorted-desc',
                cssDesc: 'list-header-sorted-asc',
                cssHeader: 'list-header-unsorted',
                widgets: ['cookie']
            });
        });
    }(jQuery));
</script>
