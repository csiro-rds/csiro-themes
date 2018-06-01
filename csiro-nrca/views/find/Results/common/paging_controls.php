<?php
$vo_result = $this->getVar('result');
$vn_current_page = intval($this->getVar('page'));
$vn_num_pages = intval($this->getVar('num_pages'));
$va_previous_link_params = array('page' => $vn_current_page - 1);
$va_next_link_params = array('page' => $vn_current_page + 1);
$va_jump_to_params = array();
$vn_type_id = intval($this->getVar('type_id'));

if ($vn_type_id) {
    $va_previous_link_params['type_id'] = $vn_type_id;
    $va_next_link_params['type_id'] = $vn_type_id;
    $va_jump_to_params['type_id'] = $vn_type_id;
}


// for related list bundle
$vs_ids = $this->request->getParameter('ids', pString);
if ($vs_ids) {
    $va_previous_link_params['ids'] = $vs_ids;
    $va_previous_link_params['interstitialPrefix'] = $this->request->getParameter('interstitialPrefix', pString);
    $va_previous_link_params['relatedRelTable'] = $this->request->getParameter('relatedRelTable', pString);
    $va_previous_link_params['relatedTable'] = $this->request->getParameter('relatedTable', pString);
    $va_previous_link_params['primaryTable'] = $this->request->getParameter('primaryTable', pString);
    $va_previous_link_params['primaryID'] = $this->request->getParameter('primaryID', pInteger);

    $va_next_link_params['ids'] = $vs_ids;
    $va_next_link_params['interstitialPrefix'] = $this->request->getParameter('interstitialPrefix', pString);
    $va_next_link_params['relatedRelTable'] = $this->request->getParameter('relatedRelTable', pString);
    $va_next_link_params['relatedTable'] = $this->request->getParameter('relatedTable', pString);
    $va_next_link_params['primaryTable'] = $this->request->getParameter('primaryTable', pString);
    $va_next_link_params['primaryID'] = $this->request->getParameter('primaryID', pInteger);
}

$vs_previous_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_previous_link_params);
$vs_next_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_next_link_params);
$vs_jump_to_base_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), $va_jump_to_params) . '/page/';
?>

<div class="component component-paging-controls">
    <?php if ($vn_num_pages > 1 && !$this->getVar('dontShowPages')): ?>
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" onclick="jQuery('#resultBox').load('<?php print $vs_previous_url ?>');" class="btn btn-default" <?php print ($vn_current_page <= 1 ? 'disabled="disabled"' : ''); ?>>
                    <span class="glyphicon glyphicon-step-backward"></span>
                    <?php print _t("Previous"); ?>
                </button>
            </div>
            <select onchange="jQuery('#resultBox').load('<?php print $vs_jump_to_base_url ?>' + jQuery(this).val());">
                <?php foreach (range(1, $vn_num_pages) as $vn_page): ?>
                    <option value="<?php print $vn_page; ?>" <?php print ($vn_page === $vn_current_page ? 'selected="selected"' : ''); ?>>
                        <?php print _t('Page %1 of %2', $vn_page, $vn_num_pages); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="input-group-btn">
                <button type="button" onclick="jQuery('#resultBox').load('<?php print $vs_next_url ?>');" class="btn btn-default" <?php print ($vn_current_page >= $vn_num_pages ? 'disabled="disabled"' : ''); ?>>
                    <?php print _t("Next"); ?>
                    <span class="glyphicon glyphicon-step-forward"></span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>
