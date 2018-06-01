<?php
$vs_id_prefix = $this->getVar('id_prefix');
$va_settings = $this->getVar('settings') ?: array();
$t_acl = new ca_acl();
$t_acl->set('access', (int)$this->getVar('initialValue'));
?>
<div id="<?php print $vs_id_prefix; ?>_world" class="component component-bundle component-bundle-acl-world">
    <div class="bundleContainer">
        <div id="<?php print $vs_id_prefix; ?>_World" class="row">
            <div class="col-md-12">
                <label><?php print _t('Everyone'); ?></label>
            </div>
        </div>
        <div id="<?php print $vs_id_prefix; ?>_World" class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?php print $t_acl->htmlFormElement('access', '^ELEMENT', array('name' => $vs_id_prefix.'_access_world', 'id' => $vs_id_prefix.'_access_world')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
