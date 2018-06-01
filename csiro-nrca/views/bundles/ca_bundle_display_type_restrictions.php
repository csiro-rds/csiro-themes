<?php
$va_errors = $this->getVar('errors');
?>
<div id="<?php print $this->getVar('placement_code') . $this->getVar('id_prefix'); ?>" class="component component-bundle component-bundle-display-type-restrictions">
    <div class="bundleContainer">
        <div class="item-list">
            <?php if (is_array($va_errors) && sizeof($va_errors)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert-danger">
                            <span class="glyphicon glyphicon-exclamation-sign"></span>
                            <?php print _t('Errors:'); ?>
                            <ul>
                                <?php foreach ($va_errors as $vs_error): ?>
                                    <li><?php print $vs_error->getErrorDescription(); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?php print $this->getVar('type_restrictions'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
