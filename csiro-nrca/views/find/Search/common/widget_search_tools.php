<?php
$o_dm = Datamodel::load();

$vo_result_context = $this->getVar('result_context');
$vo_result = $this->getVar('result');
$va_search_history = $this->getVar('search_history');
$vs_current_search = $this->getVar("last_search");
$vs_table_name = $this->getVar('table_name');

$va_saved_searches = $this->request->user->getSavedSearches($vs_table_name, $this->getVar('find_type'));
$vs_viz_list = $vo_result ? Visualizer::getAvailableVisualizationsAsHTMLFormElement($vo_result->tableName(), 'viz', array('id' => 'caSearchVizOpts'), array('resultContext' => $vo_result_context, 'data' => $vo_result, 'restrictToTypes' => array($vo_result_context->getTypeRestriction($vb_type_restriction_has_changed)))) : null;
$vs_search_controller = 'find/Search' . preg_replace('/\s+/', '', ucwords($o_dm->getTableProperty($o_dm->getTableNum($vs_table_name), 'NAME_PLURAL')));
?>

<div class="component component-widget component-widget-search-tools">
    <h2><?php print _t("Search %1", $this->getVar('mode_type_plural')); ?></h2>

    <?php if (is_array($va_search_history) && sizeof($va_search_history) > 0): ?>
        <?php print caFormTag($this->request, 'Index', 'caSearchHistoryForm', $vs_search_controller, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
            <label for="search-history"><?php print _t("History"); ?></label>
            <div class="input-group">
                <select name="search" id="search-history" class="form-control">
                    <?php foreach(array_reverse($va_search_history, true) as $vs_search => $va_search_info): ?>
                        <option value="<?php print htmlspecialchars($vs_search, ENT_QUOTES, 'UTF-8'); ?>" <?php print ($vs_current_search === $va_search_info['display']) ? 'selected="selected"' : ''; ?>>
                            <?php print strip_tags($va_search_info['display']); ?>
                            (<?php print $va_search_info['hits']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="input-group-btn">
                    <button class="btn btn-success">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <?php print caFormTag($this->request, 'doSavedSearch', 'caSavedSearchesForm', $this->request->getModulePath() . '/' . $this->request->getController(), 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
        <label for="saved_search_key"><?php print _t("Saved searches"); ?></label>
        <div class="input-group">
            <select name="saved_search_key" id="saved_search_key" class="savedSearchSelect form-control">
                <option value="">-</option>
                <?php foreach(array_reverse($va_saved_searches, true) as $vs_key => $va_search): ?>
                    <option value="<?php print htmlspecialchars($vs_key, ENT_QUOTES, 'UTF-8'); ?>" <?php print ($vs_current_search === $va_search['_label']) ? 'selected="selected"' : ''; ?>>
                        <?php print strip_tags($va_search['_label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="input-group-btn">
                <button class="btn btn-success">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </div>
        </div>
    </form>

    <?php if (sizeof($this->getVar("available_sets")) > 0): ?>
        <?php print caFormTag($this->request, 'Index', 'caSearchSetsForm', $vs_search_controller, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
            <label for="search-set"><?php print _t("Search by set"); ?></label>
            <div class="input-group">
                <select name="search" id="search-set" class="searchSetSelect form-control">
                    <?php foreach($this->getVar("available_sets") as $vn_set_id => $va_set): ?>
                        <?php
                        $vs_value = 'set:"' . ($va_set['set_code'] ?: $vn_set_id) . '"';
                        ?>
                        <option value='<?php print $vs_value; ?>' <?php print ($vs_current_search === $vs_value ? 'selected="selected"' : ''); ?>>
                            <?php print $va_set['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="input-group-btn">
                    <button class="btn btn-success">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($vo_result): ?>
        <?php print $this->render('Results/current_sort_html.php'); ?>

        <?php if ($vs_viz_list): ?>
            <div class="visualize">
                <label><?php print _t("Visualize"); ?>
                    <i class="glyphicon glyphicon-map-marker"></i>
                    <i class="glyphicon glyphicon-time"></i></label>
                <div class="input-group">
                    <?php print $vs_viz_list; ?>
                    <div class="input-group-btn">
                            <button onclick="caMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Viz', array()); ?>/viz/' + jQuery('#caSearchVizOpts').val());" class="btn btn-success">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php print $this->render('Search/search_sets_html.php'); ?>
    <?php endif; ?>
</div>
