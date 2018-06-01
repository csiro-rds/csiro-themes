<?php
$vo_result_context = $this->getVar('result_context');
$vo_result = $this->getVar('result');
$va_current_sort = caGetSortForDisplay($vo_result->getResultTableName(), $vo_result_context->getCurrentSort());
?>
<?php if (is_array($va_current_sort) && (sizeof($va_current_sort) > 0)): ?>
    <div>
        <label for="currentSort"><?php print _t("Current sort"); ?></label>
        <input value="<?php print join(', ', $va_current_sort); ?>" readonly="readonly" disabled="disabled" id="currentSort" class="form-control disabled" />
    </div>
<?php endif; ?>
