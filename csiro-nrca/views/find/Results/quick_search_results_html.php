<?php
$o_search_config = caGetSearchConfig();
$o_dm = Datamodel::load();
$ps_search = $this->getVar('search');
$va_searches = $this->getVar('searches');
$vs_visibility = (sizeof($va_searches) == 1) ? 'block' : 'none';
$vn_num_result_lists_to_display = 0;
?>
<div class="clearfix">
    <h1 class="pull-left"><?php print _t("Top %1 results for <em>%2</em>", $this->getVar('maxNumberResults'), $this->getVar('search')); ?></h1>
    <div class="pull-right form-inline">
        <?php print caFormTag($this->request, 'Index', 'QuickSearchSortForm'); ?>
            <?php print _t('Sort by'); ?>:
            <?php print caHTMLSelect('sort', array(_t('name') => 'name', _t('relevance') => 'relevance', _t('idno') => 'idno'), array('onchange' => 'jQuery("#QuickSearchSortForm").submit();', 'class' => 'form-control'), array('value' => $this->getVar('sort'))); ?>
        </form>
    </div>
</div>
<div id="quick-search-accordion" class="panel-group">
    <?php foreach($va_searches as $vs_target => $va_info): ?>
        <?php
        $va_table = explode('/', $vs_target);
        $vs_table = $va_table[0];
        $vs_type = (isset($va_table[1])) ? $va_table[1] : null;
        $o_res = $this->getVar($vs_target.'_results');
        $vs_target_id = str_replace("/", "-", $vs_target);
        ?>
        <div class="panel panel-default">
            <?php if ($o_res->numHits() >= 1): ?>
                <div class="panel-heading clearfix">
                    <div class="panel-title pull-left">
                        <a href="#<?php print $vs_target_id; ?>_results" data-toggle="collapse" data-parent="#quick-search-accordion">
                            <?php print $va_info['displayname'];?>
                            (<?php print $o_res->numHits(); ?>)
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </a>
                    </div>
                    <div class="pull-right">
                        <?php print caNavLink($this->request, '<span class="glyphicon glyphicon-menu-hamburger"></span> '._t("Full Results"), null, $va_info['searchModule'], $va_info['searchController'], $va_info['searchAction'], array("search" => caEscapeSearchForURL($ps_search), 'type_id' => $vs_type ? $vs_type : '*')); ?>
                    </div>
                </div>
                <div id="<?php print $vs_target_id; ?>_results" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <?php
                            $t_instance = $o_dm->getInstanceByTableName($vs_table, true);
                            $va_type_list = $t_instance->getTypeList();
                            $vb_show_labels = !(($vs_table === 'ca_objects') && ($t_instance->getAppConfig()->get('ca_objects_dont_use_labels')));
                            ?>
                            <?php while($o_res->nextHit()): ?>
                                <?php
                                $vs_type = $t_instance->getTypeCode((int)$o_res->get($vs_table.'.type_id'));
                                $vs_template = $o_search_config->get($vs_table.'_'.$vs_type.'_quicksearch_result_display_template') ?: $o_search_config->get($vs_table.'_quicksearch_result_display_template');
                                ?>
                                <li>
                                    <?php if ($vs_template): ?>
                                        <?php print $o_res->getWithTemplate($vs_template); ?>
                                    <?php else: ?>
                                        <?php
                                        $vs_label = $vb_show_labels ? $o_res->get($vs_table.'.preferred_labels') : trim($o_res->get($va_info['displayidno']));
                                        $vs_idno_display = trim($o_res->get($va_info['displayidno']));
                                        $vs_idno_display = $vb_show_labels && $vs_idno_display ? '('.$vs_idno_display.')' : '';
                                        $vn_type_id = trim($o_res->get($vs_table.'.type_id'));
                                        $vs_type_display = ($vn_type_id && $va_type_list[$vn_type_id]) ? ' ['.$va_type_list[$vn_type_id]['name_singular'].']' : '';
                                        ?>
                                        <?php print caEditorLink($this->request, $vs_label, null, $vs_table, $o_res->get($va_info['primary_key'])); ?>
                                        <?php print $vs_idno_display; ?>
                                        <?php print $vs_type_display; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="panel-heading clearfix">
                    <div class="panel-title pull-left">
                        <?php print $va_info['displayname']; ?>
                        (<?php print $o_res->numHits(); ?>)
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
