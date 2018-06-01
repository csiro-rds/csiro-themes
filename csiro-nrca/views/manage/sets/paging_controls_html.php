<?php
// TODO This file still needs some work
$vo_result = $this->getVar('result');
$va_previous_link_params = array('page' => $this->getVar('page') - 1);
$va_next_link_params = array('page' => $this->getVar('page') + 1);
$va_jump_to_params = array();
$vn_type_id = intval($this->getVar('type_id'));

if ($vn_type_id) {
    $va_previous_link_params['type_id'] = $vn_type_id;
    $va_next_link_params['type_id'] = $vn_type_id;
    $va_jump_to_params['type_id'] = $vn_type_id;
}

$previousUrl = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_previous_link_params);
$nextUrl = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_next_link_params);
$jumpToBaseUrl = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_jump_to_params) . '/page/';
?>
<div class="searchNav">
    <?php if ($this->getVar('num_pages') > 1 && !$this->getVar('dontShowPages')): ?>
        <div class="nav">
            <?php if ($this->getVar('page') > 1): ?>
                <a href="#" onclick="jQuery('#resultBox').load('<?php print $previousUrl ?>'); return false;" class="button">
                    <span class="glyphicon glyphicon-step-backward"></span>
                    <?php print _t("Previous"); ?>
                </a>
            <?php endif; ?>
            <?php print _t("Page") . $this->getVar('page') . '/' . $this->getVar('num_pages'); ?>
            <?php if ($this->getVar('page') < $this->getVar('num_pages')): ?>
                <a href="#" onclick="jQuery('#resultBox').load('<?php print $nextUrl ?>'); return false;" class="button">
                    <?php print _t("Next"); ?>
                    <span class="glyphicon glyphicon-step-forward"></span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="input-group">
        <input name="page" id="jumpToPageNum" class="form-control" placeholder="<?php print _t('Jump to Page'); ?>" />
        <div class="input-group-btn">
            <button onclick="jQuery('#resultBox').load('<?php print $vs_jump_to_base_url ?>' + jQuery('#jumpToPageNum').val());" class="btn btn-success">
                <span class="glyphicon glyphicon-menu-right"></span>
            </button>
        </div>
    </div>
    <?php if ($vo_result): ?>
        <?php print _t('Your %1 found', $this->getVar('mode_name')); ?>
        <?php print $vo_result->numHits(); ?>
        <?php print $this->getVar(($vo_result->numHits() == 1) ? 'mode_type_singular' : 'mode_type_plural'); ?>
    <?php endif; ?>
</div>
