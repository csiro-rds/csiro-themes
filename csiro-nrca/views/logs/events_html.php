<?php
$va_events_list = $this->getVar('events_list');
$vs_sort_icons = ' <span class="glyphicon glyphicon-menu-up"></span><span class="glyphicon glyphicon-menu-down"></span>';
?>
<div class="logs logs-events">
    <div class="well">
        <?php print caFormTag($this->request, 'Index', 'eventsLogSearch'); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-group-addon" for="search-log">
                            <?php print _t('Show from'); ?>
                        </label>
                        <input name="search" id="search-log" placeholder="<?php print _t('Enter a date range eg. %1 or %2 - %3', date('Y'), date('Y') - 1, date('Y')); ?>" value="<?php print $this->getVar('events_list_search'); ?>" class="form-control" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-group-addon" for="filter">
                            <?php print _t('Filter'); ?>
                        </label>
                        <input name="filter" id="filter" value="" placeholder="<?php print _t('Filter the change log'); ?>" onkeyup="$('#caItemList').caFilterTable(this.value); return false;" class="form-control" />
                    </div>
                </div>
            </div>
        </form>
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
                    <?php print _t('Description') . $vs_sort_icons; ?>
                </th>
                <th class="list-header-unsorted">
                    <?php print _t('Source') . $vs_sort_icons; ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (sizeof($va_events_list)): ?>
                <?php foreach($va_events_list as $va_event): ?>
                    <tr>
                        <td>
                            <?php print date("n/d/Y@g:i:sa T", $va_event['date_time']); ?>
                        </td>
                        <td>
                            <?php print $va_event['code']; ?>
                        </td>
                        <td>
                            <?php print $va_event['message']; ?>
                        </td>
                        <td>
                            <?php print $va_event['source']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">
                        <?php print (trim($this->getVar('events_list_search'))) ? _t('No events found') : _t('Enter a date to display events from above'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    (function ($) {
        'use strict';

        $(function(){
            $('#caItemList').tablesorter({
                cssAsc: 'list-header-sorted-desc',
                cssDesc: 'list-header-sorted-asc',
                cssHeader: 'list-header-unsorted',
                widgets: [ 'cookie' ]
            });
        });
    }(jQuery));
</script>
