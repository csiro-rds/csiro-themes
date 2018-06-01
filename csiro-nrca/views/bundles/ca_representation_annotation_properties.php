<?php
$va_errors = $this->getVar('errors');
?>
<div id="<?php print $this->getVar('placement_code') . $this->getVar('id_prefix'); ?>" class="component component-bundle component-bundle-representation-annotation-properties">
    <div class="bundleContainer">
        <div class="item-list">
            <?php if (is_array($va_errors) && sizeof($va_errors)): ?>
                <div class="alert-danger">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    <?php print _t('Errors:'); ?>
                    <ul>
                        <?php foreach ($va_errors as $vs_error): ?>
                            <li><?php print $vs_error->getErrorDescription(); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php print $this->getVar('form_element'); ?>
        </div>
    </div>
</div>
