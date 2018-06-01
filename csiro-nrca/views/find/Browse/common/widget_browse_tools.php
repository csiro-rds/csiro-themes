<?php
$vo_result_context = $this->getVar('result_context');
$vo_result = $this->getVar('result');
$vs_type = $this->getVar('mode_type_plural');
$vs_extra_template_path = $this->getVar('extra_template_path');
?>
<div class="component component-browse-tools <?php print preg_replace('/\s+/', '-', strtolower($vs_type)); ?>">
    <h2><?php print _t('Browse %1', ucwords($this->getVar('mode_type_plural'))); ?></h2>
    <?php if ($vo_result): ?>
        <?php print $this->render('Results/current_sort_html.php'); ?>
        <?php if ($vs_extra_template_path): ?>
            <?php print $this->render($vs_extra_template_path); ?>
        <?php endif; ?>
        <?php print $this->render('Search/search_sets_html.php'); ?>
    <?php else: ?>
        <?php print _t('No results'); ?>
    <?php endif; ?>
</div>
