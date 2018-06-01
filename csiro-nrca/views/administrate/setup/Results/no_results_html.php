<?php
$va_suggestions = caGetSearchInstance($this->getVar('t_subject')->tableNum())->suggest($this->getVar('search'), array('returnAsLink' => true, 'request' => $this->request));
?>
<div class="component component-no-results">
    <h2><?php print $this->getVar('search') ? _t("Your search found no %1", $this->getVar('mode_type_plural')) : _t("Please enter a search"); ?></h2>
    <?php if (!empty($va_suggestions)): ?>
        <?php print _t("Did you mean: %1?", join(', ', $va_suggestions)); ?>
    <?php else: ?>
        <i><?php print _t('No suggestions available'); ?></i>
    <?php endif; ?>
</div>
