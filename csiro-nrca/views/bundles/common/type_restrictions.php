<div id="<?php print $this->getVar('placement_code') . $this->getVar('id_prefix'); ?>" class="component component-bundle component-bundle-ui-screen-type-restrictions">
    <div class="bundleContainer">
        <?php foreach ($this->getVar('errors') ?: array() as $va_error): ?>
            <div class="alert alert-danger">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                <?php print $va_error; ?>
            </div>
        <?php endforeach; ?>
        <div class="item-list">
            <?php print $this->getVar('type_restrictions'); ?>
        </div>
    </div>
</div>
