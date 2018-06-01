<?php
/**
  * Make sure the classnames match related_list.php
*/
$va_display_list = $this->getVar('display_list');
$vo_result = $this->getVar('result');
$vs_current_sort = $this->getVar('current_sort');
$vs_current_sort_dir = $this->getVar('current_sort_direction');
$vs_default_action = $this->getVar('default_action');
$va_rel_id_typenames = $this->getVar('relationIdTypeNames');
$va_rel_id_index = $this->getVar('relationIDsToRelatedIDs');
$vs_interstitial_prefix = $this->getVar('interstitialPrefix');

$va_sort_directions = array(
    '' => array( 'class' => 'list-header-unsorted', 'next' => 'asc', 'icon' => '' ),
    'asc' => array( 'class' => 'list-header-sorted-asc', 'next' => 'desc', 'icon' => 'menu-up' ),
    'desc' => array( 'class' => 'list-header-sorted-desc', 'next' => '', 'icon' => 'menu-down' ),
    false => array( 'class' => 'list-header-nosort' )
);

$va_base_template_opts = array(
    'resolveLinksUsing' => $this->getVar('primaryTable'),
    'primaryIDs' => array (
        $this->getVar('primaryTable') => array($this->getVar('primaryID'))
    )
);
?>

<div id="scrollingResults" class="component component-results-list component-results-list-related">
    <form id="caFindResultsForm<?php print $vs_interstitial_prefix; ?>">
        <table id="<?php print $vs_interstitial_prefix; ?>RelatedList" class="table table-bordered table-striped results">
            <thead>
                <tr>
                    <th class="list-header-nosort"></th>
                    <th class="list-header-nosort">
                        <?php print ($vs_default_action === 'Edit' ? _t('Edit') : _t('View')); ?>
                    </th>
                    <th class="list-header-nosort"></th>
                    <?php foreach ($va_display_list as $vn_i => $va_display_item): ?>
                        <?php
                        $vb_is_sortable = $va_display_item['is_sortable'];
                        $vb_is_sort = $vb_is_sortable && $this->getVar('current_sort') === $va_display_item['bundle_sort'];
                        $va_sort_direction = $va_sort_directions[$vb_is_sortable ? ($vb_is_sort ? $this->getVar('current_sort_direction') : '') : false];
                        ?>
                        <th class="<?php print $va_sort_direction['class']; ?>">
                            <span id="listHeader<?php print $vn_i; ?>">
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
                        <?php TooltipManager::add('#listHeader' . $vn_i, $vb_is_sortable ? _t($vb_is_sort ? 'Currently sorting by %1' : 'Sort by %1', $va_display_item['display']) : $va_display_item['display']); ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($vn_item = 0; $vn_item < $this->getVar('current_items_per_page') && $vo_result->nextHit(); ++$vn_item): ?>
                    <?php
                    $vn_relation_id = key($va_rel_id_index);
                    next($va_rel_id_index);
                    ?>
                    <tr id="<?php print $vs_interstitial_prefix . $vn_relation_id; ?>">
                        <td>
                            <button type="button" class="btn btn-default btn-sm edit-interstitial listRelEditButton">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                            <button type="button" class="btn btn-default btn-sm remove listRelDeleteButton">
                                <span class="glyphicon glyphicon-remove"
                            </button>
                        </td>
                        <td>
                            <a href="<?php print caEditorUrl($this->request, $this->getVar('relatedTable'), $vo_result->get($this->getVar('relatedInstance')->primaryKey())); ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </td>
                        <td>
                            <?php print $va_rel_id_typenames[$vn_relation_id]; ?>
                        </td>
                        <?php foreach ($va_display_list as $vn_placement_id => $va_info): ?>
                            <td>
                                <?php if ($va_info['settings']['format']): ?>
                                    <?php print caProcessTemplateForIDs($va_info['settings']['format'], $this->getVar('relatedRelTable'), array($vn_relation_id), array_merge($va_info, $va_base_template_opts)); ?>
                                <?php else: ?>
                                    <?php print $this->getVar('t_display')->getDisplayValue($vo_result, $vn_placement_id, array_merge(array('request' => $this->request), is_array($va_info['settings']) ? $va_info['settings'] : array())); ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
            <?php if (is_array($va_bottom_line = $this->getVar('bottom_line'))): ?>
                <tfoot>
                    <tr>
                        <td colspan="2" class="listtableTotals">
                            <?php print _t('Totals'); ?>
                        </td>
                        <?php foreach ($va_bottom_line as $vs_bottom_line_value): ?>
                            <td>
                                <?php print $vs_bottom_line_value; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </form>
</div>

<?php if ($vs_current_sort == '_user'): ?>
    <script>
        (function ($) {
            'use strict';

            // if using user defined sorting, make tbody drag+droppable
            $(function () {
                var $tbody = $('#<?php print $vs_interstitial_prefix; ?>RelatedList tbody');

                $tbody.sortable({
                    update: function() {
                        var ids = [];
                        $tbody.find('tr').each(function() {
                            ids.push($(this).attr('id').replace('<?php print $vs_interstitial_prefix; ?>', ''));
                        });

                        $.get(
                            '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'SaveUserSort'); ?>',
                            {
                                ids: ids,
                                related_rel_table: '<?php print $this->getVar('relatedRelTable'); ?>'
                            }
                        );
                    }
                }).disableSelection();
            });
        }(jQuery));
    </script>
<?php endif; ?>
