<?php
$vo_result = $this->getVar('result');
$vs_view = $vo_result && $vo_result->numHits() > 0 ? $this->getVar('current_view') : 'no_results';
if (array_search($vs_view, $this->getVar('allowed_views') ?: array('no_results', 'list')) === false) {
    $vs_view = $this->getVar('default_view') ?: 'list';
}
$vs_view_name = ($vs_view === 'no_results' ? $vs_view : $this->getVar('t_subject')->tableName() . '_results_' . $vs_view);
?>

<div class="component component-search-<?php print ($this->getVar('advanced') ? 'advanced' : 'basic'); ?>">
    <?php print $this->render($this->getVar('advanced') ? 'Search/search_advanced_controls_html.php' : 'Search/search_controls_html.php'); ?>

    <?php if ($this->getVar('quick_look')): ?>
        <div id="quickLookOverlay">
            <div id="quickLookOverlayContent">
            </div>
        </div>
    <?php endif; ?>

    <div id="resultBox">
        <?php if ($vo_result): ?>
            <div class="clearfix">
                <div class="pull-right">
                    <?php print $this->render('Results/paging_controls_html.php'); ?>
                </div>
                <?php print $this->render('Results/search_options_html.php'); ?>
            </div>

            <div class="sectionBox">
                <?php print $this->render('Results/' . $vs_view_name . '_html.php'); ?>
            </div>

            <div class="clearfix">
                <div class="pull-right">
                    <?php print $this->render('Results/paging_controls_minimal_html.php'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
