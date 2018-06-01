<?php
$vs_id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
$t_subject 			= $this->getVar('t_subject');
$vn_num_multifiles = $this->getVar('representation_num_multifiles') ?: 0;
$vs_num_multifiles = '';
if($vn_num_multifiles === 1) {
    $vs_num_multifiles = _t('+ 1 additional preview');
} else if($vn_num_multifiles > 1) {
    $vs_num_multifiles = _t('+ ' . $vn_num_multifiles . ' additional previews');
}
$vb_allow_fetching_from_urls = $this->request->getAppConfig()->get('allow_fetching_of_media_from_remote_urls');
$vb_media_is_set = is_array($t_subject->getMediaInfo('media'));

$vs_download_link = urldecode(caNavUrl($this->request, '*', '*', 'DownloadMedia', array('version' => 'original', 'representation_id' => $t_subject->getPrimaryKey(), 'download' => 1)));
$vs_representation_link = urldecode(caNavUrl($this->request, '*', '*', 'GetRepresentationEditor', array('representation_id' => $t_subject->getPrimaryKey())));

print caEditorBundleShowHideControl($this->request, $vs_id_prefix);
print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $va_settings);
?>
<div id="<?php print $vs_id_prefix; ?>">
    <div class="bundleContainer">
        <div class="row">
            <div class="col-md-4">
                <div id="<?php print "{$vs_id_prefix}"; ?>_media_upload_control">
                    <?php print $t_subject->htmlFormElement('media', null, array('displayMediaVersion' => null, 'name' => "{$vs_id_prefix}_media", 'id' => "{$vs_id_prefix}_media", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations_media_display')); ?>
                    <?php if ($vb_allow_fetching_from_urls): ?>
                        <button type="button" onclick="mediaControlClick();">
                            <span class="glyphicon glyphicon-upload"></span>
                            <?php print _t('or fetch from a URL'); ?>
                        </button>
                    <?php endif; ?>
                </div>
                <?php if ($vb_allow_fetching_from_urls): ?>
                    <div id="<?php print "{$vs_id_prefix}"; ?>_media_url_control" class="hidden">
                        <?php print _t('Fetch from URL') . ':' ?>
                        <?php print caHTMLTextInput("{$vs_id_prefix}_url", array('id' => "{$vs_id_prefix}_url")); ?>
                        <button type="button" onclick="mediaControlClick();">
                            <span class="glyphicon glyphicon-upload"></span>
                            <?php print _t('or upload a file'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <?php print $this->getVar("representation_typename"); ?>
                <?php print caGetRepresentationDimensionsForDisplay($t_subject, 'original', array()); ?>
                <?php print $vs_num_multifiles; ?>
            </div>
            <div class="col-md-4">
                <?php if ($vb_media_is_set): ?>
                    <div>
                        <a href="<?print $vs_download_link ?>" class="btn btn-default">
                            <span class="glyphicon glyphicon-download"></span>
                            <?php _t('Download') ?>
                        </a>
                    </div>
                <?php endif; ?>
                <button type="button" class="btn btn-default"  onclick="representationButtonClick();">
                    <?php print $t_subject->getMediaTag('media', 'preview170'); ?>
                </button>
            </div>
        </div>
        <?php if ($vb_media_is_set): ?>
            <div id="<?php print "{$vs_id_prefix}_derivative_options_container"; ?>">
                <?php print caHTMLCheckboxInput("{$vs_id_prefix}_derivative_options_selector", array('value' => '1', 'onclick' => "$('#{$vs_id_prefix}_derivative_options').slideToggle(250);")).' '._t('Modify preview images'); ?>
                <div class="objectRepresentationMediaDisplayDerivativeOptions rounded" id="<?php print "{$vs_id_prefix}_derivative_options"; ?>">
                    <div class="objectRepresentationMediaDisplayDerivativeHeader">
                        <?php print _t("Update source") . ":"; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php print caHTMLRadioButtonInput("{$vs_id_prefix}_derivative_options_mode", array('value' => 'file', 'checked' => '1', 'id' => "{$vs_id_prefix}_derivative_options_mode_file")).' '._t('Update with uploaded media'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if($t_subject->getAnnotationType() === 'TimeBasedVideo'): ?>
                                <?php print caHTMLRadioButtonInput("{$vs_id_prefix}_derivative_options_mode", array('value' => 'timecode', 'id' => "{$vs_id_prefix}_derivative_options_mode_timecode")).' '._t('Update using frame at timecode').' '; ?>
                                <?php print caHTMLTextInput("{$vs_id_prefix}_derivative_options_mode_timecode_value", array('id' => "{$vs_id_prefix}_derivative_options_mode_timecode_value", 'class' => 'timecodeBg', 'onclick' => "jQuery('#{$vs_id_prefix}_derivative_options_mode_timecode').attr('checked', '1');"), array("width" => 30, "height" => 1)); ?>
                            <?php elseif($t_subject->getAnnotationType() === 'Document'): ?>
                                <?php print caHTMLRadioButtonInput("{$vs_id_prefix}_derivative_options_mode", array('value' => 'page', 'id' => "{$vs_id_prefix}_derivative_options_mode_page")).' '._t('Update using page #').' '; ?>
                                <?php print caHTMLTextInput("{$vs_id_prefix}_derivative_options_mode_page_value", array('id' => "{$vs_id_prefix}_derivative_options_mode_page_value", 'onclick' => "jQuery('#{$vs_id_prefix}_derivative_options_mode_page').attr('checked', '1');"), array("width" => 4, "height" => 1)); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="objectRepresentationMediaDisplayDerivativeHeader">
                                <?php print _t("Update preview versions").":"; ?>
                            </div>
                        </div>
                        <?php foreach($t_subject->getMediaVersions('media') as $vs_version): ?>
                            <?php if($t_subject->getMediaInputTypeForVersion('media', $vs_version) === 'image'): ?>
                                <div class="col-md-6">
                                    <?php print caHTMLCheckboxInput($vs_id_prefix.'_set_versions[]', array('value' => $vs_version, 'checked' => '1')); ?>
                                    <span id="<?php print '{$vs_id_prefix}_media_{$vs_version}_label'; ?>">
                                        <?php print '{$vs_version} (' . $t_subject->getMediaInfo('media', $vs_version, 'WIDTH') . 'x' . $t_subject->getMediaInfo('media', $vs_version, 'HEIGHT') . ')'; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="objectRepresentationMediaDisplayDerivativeHelpText" id="<?php print "{$vs_id_prefix}_derivative_options_help_text"; ?>">
                        <?php print _t('Use the controls above to replace existing preview images for this representation. If <em>Update with uploaded media</em> is checked then the media you have selected for upload will be used to generate the replacement previews. For PDF and video representations you may alternatively elect to generate new previews from a specific page or frame using the <em>Update using page</em> and <em>Update using frame at timecode</em> options. You can control which preview versions are generated by checking or unchecking options in the <em>Update preview versions</em> section. Note that replacement of preview images will be performed only if the master <em>Modify preview images</em> checkbox is checked. If unchecked the uploaded media will completely replace <strong>all</strong> media and previews associated with this representation.'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
print TooltipManager::getLoadHTML("{$vs_id_prefix}_media_tooltips");
?>
<script>
    var mediaControlClick, representationButtonClick;
    (function($) {
        $(function() {
            mediaControlClick = function() {
                $("#<?php print "{$vs_id_prefix}"; ?>_media_url_control").slideDown(200);
                $("#<?php print "{$vs_id_prefix}"; ?>_media_upload_control").slideUp(200);
            };
            representationButtonClick = function() {
                caMediaPanel.showPanel('<?php print $vs_representation_link ?>');
            };
        });
    })(jQuery)
</script>
