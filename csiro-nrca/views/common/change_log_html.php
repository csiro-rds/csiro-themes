<?php
$va_change_log_list = $this->getVar('change_log_list');
$vn_filter_table = $this->getVar('filter_table');
$vs_filter_change_type = $this->getVar('filter_change_type');
$vs_sort_icons = ' <span class="glyphicon glyphicon-menu-up"></span><span class="glyphicon glyphicon-menu-down"></span>';
$vs_change_log_search = trim($this->getVar('change_log_search'));


?>
<div class="logs logs-my-change">
    <div class="well">
        <?php print caFormTag($this->request, 'Index', 'changeLogSearch'); ?>
        <div class="row">
            <div class="col-md-9">
                <div class="input-group">
                    <label for="change_log_search" class="input-group-addon">
                        <?php print _t('Show from'); ?>
                    </label>
                    <input name="change_log_search" id="change_log_search" placeholder="<?php print _t('Enter a date range eg. %1 or %2 - %3', date('Y'), date('Y') - 1, date('Y')); ?>" value="<?php print $vs_change_log_search ?>"
                           class="form-control"/>
                    <label class="input-group-addon" for="filter_change_type">
                        <?php print _t('Change Type'); ?>
                    </label>
                    <?php print caHTMLSelect('filter_change_type', [_t('--Any--') => '', _t('Added') => 'I', _t('Edited') => 'U', _t('Deleted') => 'D'], ['class' => 'form-control', 'id' => 'filter_change_type'], ['value' => $vs_filter_change_type]); ?>
                    <label class="input-group-addon" for="filter_table">
                        <?php print _t('Record type'); ?>
                    </label>
                    <?php print caHTMLSelect('filter_table', array_merge([_t('--Any--') => ''], caGetPrimaryTablesForHTMLSelect()), ['class' => 'form-control', 'id' => 'filter_table'], ['value' => $vn_filter_table]); ?>
                    <span class="input-group-btn">
                    <button class="btn btn-primary">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <label class="input-group-addon" for="filter">
                        <?php print _t('Filter'); ?>
                    </label>
                    <input id="filter" value="" placeholder="<?php print _t('Filter the change log'); ?>" onkeyup="$('#caChangeLogList').caFilterTable(this.value); return false;" class="form-control"/>
                </div>
            </div>
        </div>
        <?php print "</form>"; ?>
    </div>

    <table id="caChangeLogList" class="table table-striped">
        <thead>
        <tr>
            <th class="list-header-unsorted">
                <?php print _t('Date/time') . $vs_sort_icons; ?>

            </th>
            <th class="list-header-unsorted">
                <?php print _t('Change type') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Record type') . $vs_sort_icons; ?>
            </th>
            <th class="list-header-unsorted">
                <?php print _t('Changed item') . $vs_sort_icons; ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if (sizeof($va_change_log_list)): ?>
            <?php foreach ($va_change_log_list as $vs_log_key => $va_log_entry) : ?>
                <?php
                // $va_log_entry is a list of changes performed by a user as a unit (at a single instant in time)
                // We grab the date & time, user name and other stuff out of the first entry in the list (index 0) because
                // these don't vary from change to change in a unit, and the list is always guaranteed to have at least one entry
                //
                $this->setVar('log_entry', $va_log_entry);
                ?>
                <tr>
                    <td>
                        <?php print $va_log_entry[0]['datetime']; ?>
                    </td>
                    <td>
                        <?php print $va_log_entry[0]['changetype_display']; ?>
                    </td>
                    <td>
                        <?php print Datamodel::load()->getInstance($va_log_entry[0]['subject_table_num'], true)->getProperty('NAME_PLURAL'); ?>
                    </td>
                    <td>

                        <div class="btn-group">
                            <a class="btn btn-primary" role="button" href='<?php print caEditorUrl($this->request, $va_log_entry[0]['subject_table_num'], $va_log_entry[0]['subject_id']) ?>'><?php print $va_log_entry[0]['subject'] ?> <i class="glyphicon glyphicon-edit"></i></a>
                            <a tabindex="0" role="button" class="btn btn-default" data-placement="left" data-trigger="focus" title="<?php print _t('Changes on %1', $va_log_entry[0]['datetime']); ?>" data-toggle="popover" data-html="true" data-container="body" data-content="<?php print $this->render($this->request->getDirectoryPathForThemeFile('views/common/change_log_entry_html.php')); ?>" ><?php print _t('More Info')?> <i class="glyphicon glyphicon-info-sign"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan='5'>
                    <div align="center">
                        <?php print $vs_change_log_search ? _t('No log entries found') : _t('Enter a date to display change log from above'); ?>
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
            $('#caChangeLogList').tablesorter({
                cssAsc: 'list-header-sorted-desc',
                cssDesc: 'list-header-sorted-asc',
                cssHeader: 'list-header-unsorted',
                widgets: ['cookie']
            });
            $('[data-toggle="popover"]').popover();
        });
    }(jQuery));
</script>
