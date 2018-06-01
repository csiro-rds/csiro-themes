<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$vb_read_only = isset($va_settings['readonly']) && $va_settings['readonly'];
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-object-deaccession">
    <div class="bundleContainer">
        <div class="item-list">
            <div>
                <?php if ($vb_read_only): ?>
                    <?php print _t('Deaccessioned: %1', ((bool)$t_subject->get('is_deaccessioned')) ? _t('Yes') : _t('No')); ?>
                <?php else: ?>
                    <?php print $t_subject->htmlFormElement('is_deaccessioned', '^ELEMENT '._t('Deaccessioned?'), array('name' => "{$vs_id_prefix}is_deaccessioned", 'id' => "{$vs_id_prefix}IsDeaccessioned", 'onclick' => "$('#{$vs_id_prefix}DeaccessionContainer')[$('#{$vs_id_prefix}IsDeaccessioned').is(':checked') ? 'slideDown' : 'slideUp'](250); return true;")); ?>
                <?php endif; ?>
            </div>
            <div id="<?php print $vs_id_prefix; ?>DeaccessionContainer" class="<?php print ($t_subject->get('is_deaccessioned') ? '' : 'hidden'); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $t_subject->htmlFormElement('deaccession_date', '<label>^EXTRA^LABEL</label>^ELEMENT', array('name' => "{$vs_id_prefix}deaccession_date", 'id' => "{$vs_id_prefix}DeaccessionDate", 'readonly' => $vb_read_only)); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php print $t_subject->htmlFormElement('deaccession_type_id', '<label>^EXTRA'._t('Type').'</label>^ELEMENT', array('name' => "{$vs_id_prefix}deaccession_type_id", 'id' => "{$vs_id_prefix}DeaccessionTypeID", 'readonly' => $vb_read_only)); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php print $t_subject->htmlFormElement('deaccession_notes', "<label>^EXTRA"._t('Notes')."</label>^ELEMENT", array('name' => "{$vs_id_prefix}deaccession_notes", 'id' => "{$vs_id_prefix}DeaccessionNotes", 'readonly' => $vb_read_only)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$vb_read_only): ?>
    <script>
        (function ($) {
            $(function () {
                $('#<?php print $vs_id_prefix; ?>DeaccessionDate').datepicker({ constrainInput: false });
            });
        }(jQuery));
    </script>
<?php endif; ?>

