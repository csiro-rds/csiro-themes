<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$va_settings = $this->getVar('settings');
$t_instance = $this->getVar('t_instance');
$vs_bundle_name = $this->getVar('bundle_name');
$vb_batch = $this->getVar('batch');
$vs_media = $this->getVar('display_media');

$vb_for_access_screen = caGetOption('forACLAccessScreen', $va_settings, false);
$vb_show_inherit_checkbox = ($vs_bundle_name == 'access') && (bool)$t_instance->getAppConfig()->get($t_instance->tableName().'_allow_access_inheritance') && $t_instance->hasField('access_inherit_from_parent') && ($t_instance->get('parent_id') > 0);

$vn_media_width = $vs_media ? 3 : 0;
$vn_inherit_width = $vb_show_inherit_checkbox ? 2 : 0;
$vn_element_width = 12 - $vn_media_width - $vn_inherit_width;

// fetch data for bundle preview
$vs_bundle_preview = $t_instance->get($vs_bundle_name, array('convertCodesToDisplayText' => true));
if (is_array($vs_bundle_preview)) {
    $vs_bundle_preview = '';
}

$va_errors = $this->getVar('errors');
?>
<div class="component component-bundle component-bundle-intrinsic">
    <?php if ($vb_batch): ?>
        <?php print caBatchEditorIntrinsicModeControl($t_instance, $vs_id_prefix); ?>
    <?php endif; ?>

    <?php print caEditorBundleMetadataDictionary($this->request, "intrinsic_{$vs_bundle_name}", $va_settings); ?>

    <div class="bundleContainer" id="<?php print $vs_id_prefix; ?>">
        <?php if ($vb_for_access_screen): ?>
            <label><?php print $t_instance->getFieldInfo($vs_bundle_name, 'LABEL'); ?></label>
        <?php endif; ?>
        <div class="item-list clearfix">
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
                <div class="col-md-<?php print $vn_element_width; ?>">
                    <div class="form-group">
                        <?php print $this->getVar('form_element'); ?>
                    </div>
                </div>
                <?php if ($vs_media): ?>
                    <div class="col-md-<?php print $vn_media_width; ?>">
                        <div class="form-group">
                            <?php print $vs_media; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($vb_show_inherit_checkbox): ?>
                    <div class="col-md-<?php print $vn_inherit_width; ?>">
                        <div class="form-group">
                            <?php print caHTMLCheckboxInput($vs_id_prefix.'access_inherit_from_parent', array('value' => 1, 'id' => $vs_id_prefix.'access_inherit_from_parent'), array()); ?>
                            <?php print _t('Inherit from parent?'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ((!$vb_batch && !$vb_for_access_screen) || $vb_show_inherit_checkbox): ?>
    <script>
        (function ($) {
            'use strict';

            $(function () {
                <?php if (!$vb_batch && !$vb_for_access_screen): ?>
                    $('#<?php print $vs_id_prefix; ?>_BundleContentPreview').text(<?php print caEscapeForBundlePreview($vs_bundle_preview); ?>);
                <?php endif; ?>
                <?php if ($vb_show_inherit_checkbox): ?>
                    $('#<?php print $vs_id_prefix; ?>access_inherit_from_parent').bind('click', function(e) {
                        $('#<?php print $vs_id_prefix; ?>access').prop('disabled', $(this).prop('checked'));
                    }).prop('checked', <?php print json_encode((bool)$t_instance->get('access_inherit_from_parent')); ?>);

                    if ($('#<?php print $vs_id_prefix; ?>access_inherit_from_parent').prop('checked')) {
                        $('#<?php print $vs_id_prefix; ?>access').prop('disabled', true);
                    }
                <?php endif; ?>
            });
        }(jQuery));
    </script>
<?php endif; ?>
