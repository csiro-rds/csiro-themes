<?php
$vo_result = $this->getVar('result');
$vs_view = $vo_result && $vo_result->numHits() > 0 ? $this->getVar('current_view') : 'no_results';

$this->setVar('dontShowPages', false);
?>

<div class="component component-related-list">
    <?php print $this->render('Search/search_controls_html.php'); ?>

    <div id="resultBox">
        <?php if ($vo_result): ?>
            <div class="clearfix">
                <div class="pull-left">
                    <?php print $this->render('Results/search_options_related_list_html.php'); ?>
                </div>
                <div class="pull-right">
                    <?php print $this->render('Results/related_list_paging_controls_html.php'); ?>
                </div>
            </div>

            <div class="sectionBox">
                <?php print $this->render($vs_view === 'no_results' ? 'Results/no_results_html.php' : 'Results/related_list_results_list_html.php'); ?>
            </div>

            <?php print $this->render('Results/related_list_paging_controls_html.php'); ?>
        <?php endif; ?>
    </div>
</div>
