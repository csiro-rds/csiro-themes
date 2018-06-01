<?php
$va_display_list = $this->getVar('display_list');
$vo_result = $this->getVar('result');
$va_bottom_line = $this->getVar('bottom_line');
$vs_bottom_line_totals = $this->getVar('bottom_line_totals');
$va_sort_directions = array(
    '' => array( 'class' => 'list-header-unsorted', 'next' => 'asc', 'icon' => '' ),
    'asc' => array( 'class' => 'list-header-sorted-asc', 'next' => 'desc', 'icon' => 'menu-up' ),
    'desc' => array( 'class' => 'list-header-sorted-desc', 'next' => '', 'icon' => 'menu-down' ),
    false => array( 'class' => 'list-header-nosort' )
);
?>
<div id="scrollingResults" class="component component-results-list">
    <form id="caFindResultsForm">
        <table class="table table-bordered table-striped results">
            <thead>
                <tr>
                    <th class="list-header-nosort">
                        <div class="btn btn-group">
                            <button type="button" onclick="jQuery('.add-to-set').attr('checked', true);" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-check"></span>
                            </button>
                            <button type="button" onclick="jQuery('.add-to-set').attr('checked', false);" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-unchecked"></span>
                            </button>
                            <button type="button" onclick="caToggleAddToSet();" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </div>
                    </th>
                    <th class="list-header-nosort">
                        #
                    </th>
                    <th class="list-header-nosort">
                        <?php print ($this->getVar('default_action') == 'Edit' ? _t('Edit') : _t('View')); ?>
                    </th>
                    <?php foreach ($va_display_list as $vn_header => $va_display_item): ?>
                        <?php
                        $vb_is_sortable = $va_display_item['is_sortable'];
                        $vb_is_sort = $vb_is_sortable && $this->getVar('current_sort') === $va_display_item['bundle_sort'];
                        $va_sort_direction = $va_sort_directions[$vb_is_sortable ? ($vb_is_sort ? $this->getVar('current_sort_direction') : '') : false];
                        ?>
                        <th class="<?php print $va_sort_direction['class']; ?>">
                            <span id="listHeader<?php print $vn_header; ?>">
                                <?php if ($vb_is_sortable): ?>
                                    <a href="<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Index', array( 'sort' => $va_display_item['bundle_sort'], 'direction' => $va_sort_direction['next'] )); ?>">
                                        <?php print $va_display_item['display']; ?>
                                    </a>
                                    <span class="glyphicon glyphicon-<?php print $va_sort_direction['icon']; ?>"></span>
                                <?php else: ?>
                                    <?php print $va_display_item['display']; ?>
                                <?php endif; ?>
                            </span>
                        </th>
                        <?php TooltipManager::add('#listHeader' . $vn_header, $vb_is_sortable ? _t($vb_is_sort ? 'Currently sorting by %1' : 'Sort by %1', $va_display_item['display']) : $va_display_item['display']); ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($vn_item = 0; $vn_item < $this->getVar('current_items_per_page') && $vo_result->nextHit(); ++$vn_item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="add_to_set_ids" value="<?php print (int)($vo_result->getPrimaryKey()); ?>" class="add-to-set" />
                        </td>
                        <td>
                            <div><?php print ((int)$this->getVar('start')) + $vn_item + 1; ?></div>
                        </td>
                        <td>
                            <a href="<?php print caEditorUrl($this->request, $vo_result->tableName(), $vo_result->getPrimaryKey()); ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <?php // TODO don't use an `instanceof` here ?>
                            <?php if ($vo_result instanceof ca_places && $this->getVar('mode') === 'search'): ?>
                                <a href="#" onclick="caOpenBrowserWith(<?php print $vo_result->getPrimaryKey(); ?>); return false;">
                                    <span class="glyphicon glyphicon-search"></span>
                                </a>
                            <?php endif; ?>
                        </td>
                        <?php foreach ($va_display_list as $vn_placement_id => $va_info): ?>
                            <td>
                                <?php print $this->getVar('t_display')->getDisplayValue($vo_result, $vn_placement_id, array_merge(array('request' => $this->request), is_array($va_info['settings']) ? $va_info['settings'] : array())); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <?php if (is_array($va_bottom_line)): ?>
                    <tr>
                        <td colspan="2" class="listtableTotals">
                            <?php print _t('Totals'); ?>
                        </td>
                        <?php foreach ($va_bottom_line as $vn_placement_id => $vs_bottom_line_value): ?>
                            <td>
                                <?php print $vs_bottom_line_value; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
                <?php if ($vs_bottom_line_totals): ?>
                    <tr>
                        <td colspan="<?php print sizeof($va_display_list) + 2; ?>" class="listtableAggregateTotals">
                            <?php print $vs_bottom_line_totals; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </form>
</div>
