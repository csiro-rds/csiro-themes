<?php
$va_elements = $this->getVar('elements');
$va_element_ids = $this->getVar('element_ids');
$vs_element_set_label = $this->getVar('element_set_label');
?>
<div class="component component-bundle component-bundle-search-form-attributes">
    <?php foreach ($va_elements as $vn_container_id => $va_element_list): ?>
        <?php if ($vn_container_id !== '_locale_id'): ?>
            <?php foreach($va_element_list as $vs_element): ?>
                <div class="form-group">
                    <?php print $vs_element; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (isset($va_elements['_locale_id'])): ?>
        <?php if ($va_elements['_locale_id']['hidden']): ?>
            <?php print $va_elements['_locale_id']['element']; ?>
        <?php else: ?>
            <div class="formLabel">
                <label><?php print _t('Locale '); ?></label>
                <?php print $va_elements['_locale_id']['element']; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
