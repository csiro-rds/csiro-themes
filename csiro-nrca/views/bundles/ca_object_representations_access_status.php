<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_table_num = $this->getVar('table_num');
$t_subject = $this->getVar('t_subject');
$va_settings = $this->getVar('settings');
$va_errors = $this->getVar('errors');
$vb_batch = $this->getVar('batch');
$t_rep = new ca_object_representations();
$vs_access_status_name = '#' . $vs_id_prefix . $t_subject->tableNum() . '_ca_object_representations_access_status';

print TooltipManager::getLoadHTML('bundle_ca_object_representations_access_status');
?>

<div class="component component-bundle component-bundle-object-representations-access-status">
    <div>
        <?php print caHTMLSelect($vs_id_prefix."_batch_mode", array( _t("do not use") => "_disabled_", _t('set') => '_set_' ), array( 'id' => $vs_id_prefix.$t_subject->tableNum()."_rel_batch_mode_select", 'class' => 'form-control' )); ?>
    </div>
    <div id="<?php print $vs_id_prefix.$t_subject->tableNum(); ?>_ca_object_representations_access_status">
        <div class="bundleContainer">
            <div class="caItemList">
                <div class="labelInfo">
                    <div>
                        <?php print _t('Sets access and status values for <strong>all</strong> representations related to %1 in this batch.', $t_subject->getProperty('NAME_PLURAL')); ?>
                    </div>
                    <div>
                        <?php print $t_rep->htmlFormElement('access', null, array('classname' => '', 'id' => "{$vs_id_prefix}access", 'name' => "{$vs_id_prefix}_access", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations_access_status')); ?>
                    </div>
                    <div>
                        <?php print $t_rep->htmlFormElement('status', null, array('classname' => '', 'id' => "{$vs_id_prefix}status", 'name' => "{$vs_id_prefix}_status", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations_access_status')); ?>
                    </div>
                    <br class="clear"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        $(function () {
            $('#<?php print $vs_id_prefix . $t_subject->tableNum(); ?>_rel_batch_mode_select').change(function () {
                if (($(this).val() === '_disabled_') || ($(this).val() === '_delete_')) {
                    $('<?php print $vs_access_status_name; ?>').slideUp(250);
                } else {
                    $('<?php print $vs_access_status_name; ?>').slideDown(250);
                }
            });
            $('<?php print $vs_access_status_name; ?>').hide();
        });
    })(jQuery);
</script>
