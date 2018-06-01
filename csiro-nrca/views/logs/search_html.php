<?php
$va_search_list = $this->getVar('search_list');
$vs_sort_icons = ' <span class="glyphicon glyphicon-menu-up"></span><span class="glyphicon glyphicon-menu-down"></span>';
?>
<div class="logs logs-search">
    <div class="well">
        <div class="row">
            <div class="col-md-6">
                <?php print caFormTag($this->request, 'Index', 'searchLogSearch') ?>
                <div class="input-group">
                    <label for="search-log" class="input-group-addon">
                        <?php print _t('Show from'); ?>
                    </label>
                    <input name="search" id="search-log" placeholder="<?php print _t('Enter a date range eg. %1 or %2 - %3', date('Y'), date('Y') - 1, date('Y')); ?>" value="<?php print $this->getVar('search_list_search'); ?>" class="form-control"/>
                    <span class="input-group-btn">
                        <button class="btn btn-primary">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                </div>
                <?php print '</form>'; ?>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <label class="input-group-addon" for="filter">
                        <?php print _t('Filter'); ?>
                    </label>
                    <input id="filter" value="" placeholder="<?php print _t('Filter the search log'); ?>" onkeyup="$('#caItemList').caFilterTable(this.value); return false;" class="form-control"/>
                </div>
            </div>
        </div>
    </div>

    <table id="caItemList" class="table table-striped">
        <thead>
        <tr>
            <th class="list-header-unsorted">
                <?php print _t('Date/time') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Type') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Search') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Num hits') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('User') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('IP') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Source') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Exec time (sec.)') . $vs_sort_icons; ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if (sizeof($va_search_list)) : ?>
            <?php foreach ($va_search_list as $va_search) : ?>
                <tr>
                    <td>
                        <?php print date("n/d/Y@g:i:sa T", $va_search['log_datetime']); ?>
                    </td>
                    <td>
                        <?php print $va_search['table_name']; ?>
                    </td>
                    <td>
                        <?php print $va_search['search_expression']; ?>
                    </td>
                    <td>
                        <?php print $va_search['num_hits']; ?>
                    </td>
                    <td>
                        <?php print $va_search['user_name']; ?>
                    </td>
                    <td>
                        <?php print $va_search['ip_addr']; ?>
                    </td>
                    <td>
                        <?php print $va_search['search_source'] . ($va_search['form'] ? '/' . $va_search['form'] : ''); ?>
                    </td>
                    <td>
                        <?php print $va_search['execution_time']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan='9'>
                    <div class="text-center">
                        <?php print (trim($this->getVar('search_list_search'))) ? _t('No searches found') : _t('Enter a date to display searches from above'); ?>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    (function ($) {
        'use strict';

        $(function () {
            $('#caItemList').tablesorter({
                cssAsc: 'list-header-sorted-desc',
                cssDesc: 'list-header-sorted-asc',
                cssHeader: 'list-header-unsorted',
                widgets: ['cookie']
            });
        });
    }(jQuery));
</script>
