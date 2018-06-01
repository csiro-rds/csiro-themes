<?php
$va_elements = $this->getVar('elements');
?>
<div class="component component-bundle component-bundle-attributes-elements-only">
    <?php foreach($va_elements as $vn_container_id => $va_element_list): ?>
        <?php if ($vn_container_id !== '_locale_id'): ?>
            <?php foreach($va_element_list as $vs_element): ?>
                <div>
                    <?php print preg_replace("!{{[\d]+}}!", "", str_replace("textarea", "textentry", $vs_element)); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (isset($va_elements['_locale_id'])): ?>
        <div>
            <?php if ($va_elements['_locale_id']['hidden']): ?>
                <?php print $va_elements['_locale_id']['element']; ?>
            <?php else: ?>
                <?php print _t('Locale'); ?>
                <?php print $va_elements['_locale_id']['element']; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
