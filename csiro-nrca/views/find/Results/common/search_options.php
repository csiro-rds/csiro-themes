<?php
$vo_result = $this->getVar('result');
$vo_result_context = $this->getVar('result_context');
$vs_table = $this->getVar('t_subject')->tableName();
$vs_current_sort = $vo_result_context->getCurrentSort();
$vs_current_sort_direction = $vo_result_context->getCurrentSortDirection();
$va_items_per_page = $this->getVar("items_per_page");
$vn_current_items_per_page = (int)$vo_result_context->getItemsPerPage();
$va_views = $this->getVar("views");
$vs_current_view = $vo_result_context->getCurrentView();
$va_display_lists = $this->getVar("display_lists");
$vs_children_display_mode = $this->getVar('children_display_mode');
$vb_compact = $this->getVar('compact');
$vs_id_prefix = $this->getVar('id_prefix');

TooltipManager::add('#showOptions', _t("Display Options"));
TooltipManager::add('#showRefine', _t("Refine Results"));
TooltipManager::add('#showTools', _t("Export Tools"));
TooltipManager::add('#showResultsEditor', _t("Edit in Spreadsheet"));
?>

<div class="component component-search-options">
    <div class="btn-group">
        <?php if ($vo_result->numHits() > 0): ?>
            <?php if (!$vb_compact): ?>
                <button type="button" id="showResultsEditor" class="btn btn-default" onclick="caResultsEditorPanel.showPanel('<?php print caNavUrl($this->request, '*', '*', 'resultsEditor'); ?>');">
                    <span class="glyphicon glyphicon-th"></span>
                    <?php print _t('Spreadsheet edit mode'); ?>
                </button>
            <?php endif; ?>

            <?php if ($this->getVar('mode') === 'search' && $this->request->user->canDoAction('can_browse_'.$vs_table) && !$this->getVar('noRefine') && !$this->getVar('noRefineControls')): ?>
                <button type="button" id="showRefine" class="btn btn-default" data-toggle="collapse" data-target="#searchRefineBox">
                    <span class="glyphicon glyphicon-filter"></span>
                    <?php if (!$vb_compact): ?>
                        <?php print _t('Refine results'); ?>
                    <?php endif; ?>
                </button>
            <?php endif; ?>

            <button type="button" id="showTools" class="btn btn-default" data-toggle="collapse" data-target="#searchToolsBox">
                <span class="glyphicon glyphicon-export"></span>
                <?php if (!$vb_compact): ?>
                    <?php print _t('Export tools'); ?>
                <?php endif; ?>
            </button>
        <?php endif; ?>

        <button type="button" id="showOptions" class="btn btn-default" data-toggle="collapse" data-target="#searchOptionsBox_<?php print $vs_id_prefix; ?>">
            <span class="glyphicon glyphicon-cog"></span>
            <?php if (!$vb_compact): ?>
                <?php print _t('Options'); ?>
            <?php endif; ?>
        </button>
    </div>

    <?php if ($vo_result->numHits() > 0): ?>
        <div class="clearfix">
            <?php print $this->render($this->getVar('search_tools_path') ?: 'Search/search_tools_html.php'); ?>

            <?php if (($this->getVar('mode') === 'search') && ($this->request->user->canDoAction('can_browse_'.$vs_table)) && !($this->getVar('noRefine'))): ?>
                <?php print $this->render('Search/search_refine_html.php'); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div id="searchOptionsBox_<?php print $vs_id_prefix; ?>" class="search-box collapse">
        <?php print caFormTag($this->request, 'Index', 'caSearchOptionsForm',  null , 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><?php print _t('Search options'); ?></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php print _t('Sort by'); ?></label>
                                <select name="sort" class="form-control">
                                    <?php if (is_array($this->getVar('sorts')) && (sizeof($this->getVar('sorts')) > 0)): ?>
                                        <?php foreach ($this->getVar("sorts") as $vs_sort => $vs_option): ?>
                                            <option value="<?php print $vs_sort; ?>" <?php print ($vs_current_sort === $vs_sort ? 'selected' : ''); ?>>
                                                <?php print $vs_option; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label><?php print _t('Direction'); ?></label>
                                <select name="direction" class="form-control">
                                    <option value="asc" <?php print ($vs_current_sort_direction === 'asc' ? 'selected' : ''); ?>>&uarr;</option>
                                    <option value="desc" <?php print ($vs_current_sort_direction === 'desc' ? 'selected' : ''); ?>>&darr;</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php print _t('Results / page'); ?></label>
                                <select name="n" class="form-control">
                                    <?php if (is_array($va_items_per_page) && sizeof($va_items_per_page) > 0): ?>
                                        <?php foreach ($va_items_per_page as $vn_n): ?>
                                            <option value="<?php print (int)$vn_n; ?>" <?php print ((int)$vn_n === $vn_current_items_per_page ? 'selected' : ''); ?>>
                                                <?php print $vn_n; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php print _t('Layout'); ?></label>
                                <select name="view" class="form-control">
                                    <?php if (is_array($va_views) && sizeof($va_views) > 0): ?>
                                        <?php foreach ($va_views as $vs_view => $vs_name): ?>
                                            <option value="<?php print $vs_view; ?>" <?php print ($vs_view === $vs_current_view ? 'selected' : ''); ?>>
                                                <?php print $vs_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php print _t('Display'); ?></label>
                                <select name="display_id" class="form-control">
                                    <?php if (is_array($va_display_lists) && sizeof($va_display_lists) > 0): ?>
                                        <?php foreach ($va_display_lists as $vn_display_id => $vs_display_name): ?>
                                            <option value="<?php print $vn_display_id; ?>" <?php print ($vn_display_id === $this->getVar("current_display_list") ? 'selected' : ''); ?>>
                                                <?php print $vs_display_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if (!$vb_compact): ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><?php print _t('Child records'); ?></label>
                                    <select name="children" class="form-control">
                                        <option value="show" <?php print ($vs_children_display_mode === 'show' ? 'selected' : ''); ?>><?php print _t('show'); ?></option>
                                        <option value="hide" <?php print ($vs_children_display_mode === 'hide' ? 'selected' : ''); ?>><?php print _t('hide'); ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="panel-footer clearfix">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#searchOptionsBox">
                            <span class="glyphicon glyphicon-collapse-up"></span>
                            <?php print _t('Hide'); ?>
                        </button>
                        <button class="btn btn-success">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php print _t('Apply'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
