<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_fields = $this->getVar('t_page')->getHTMLFormElements([ 'tagnamePrefix' => $vs_id_prefix ]);
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-site-pages-content">
    <div class="bundleContainer">
        <div class="item-list">
            <?php if (is_array($va_fields)): ?>
                <?php foreach($va_fields as $vs_field => $va_element_info): ?>
                    <div>
                        <?php print $va_element_info['element_with_label']; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
