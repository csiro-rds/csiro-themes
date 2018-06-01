<?php
$t_subject = $this->getVar('t_subject');
$va_media_props = $t_subject->getMediaInfo('media', 'original');
$vn_timecode_offset = isset($va_media_props['PROPERTIES']['timecode_offset']) ? (float)$va_media_props['PROPERTIES']['timecode_offset'] : 0;
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_item = $this->getVar('t_item');
$t_item_label = $this->getVar('t_item_label');

$vs_annotation_type = $t_subject->getAnnotationType();
$o_properties = $t_subject->getAnnotationPropertyCoderInstance($vs_annotation_type);

if ($o_properties) {
    $va_properties = $o_properties->getPropertyList();
    $vs_goto_property = $o_properties->getAnnotationGotoProperty();
    $va_prop_list = $va_init_props = array();

    if (!is_array($va_initial_values = $this->getVar('initialValues'))) {
        $va_initial_values = array();
    }

    foreach ($va_properties as $vs_property) {
        $va_prop_list[] = "'".$vs_property."'";
        $va_init_props[$vs_property] = '';
    }
}

$va_inital_values = $this->getVar('initialValues');
$va_errors = array();
$vb_has_annotation_type = method_exists($t_subject, "getAnnotationType") & $t_subject->getAnnotationType() && method_exists($t_subject, "useBundleBasedAnnotationEditor") && $t_subject->useBundleBasedAnnotationEditor();

if (sizeof($va_inital_values)) {
    foreach ($va_inital_values as $vn_annotation_id => $va_info) {
        if(is_array($va_action_errors = $this->request->getActionErrors('ca_representation_annotations', $vn_annotation_id))) {
            foreach($va_action_errors as $o_error) {
                $va_errors[$vn_annotation_id][] = array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber());
            }
        }
    }
}

$va_failed_inserts = array();
foreach($this->request->getActionErrorSubSources('ca_representation_annotations') as $vs_error_subsource) {
    if (substr($vs_error_subsource, 0, 4) === 'new_') {
        $va_action_errors = $this->request->getActionErrors('ca_representation_annotations', $vs_error_subsource);
        foreach($va_action_errors as $o_error) {
            $va_failed_inserts[] = array_merge($va_init_props, array('_errors' => array(array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber()))));
        }
    }
}
?>

<div class="bundleContainer">
    <?php if(!$vb_has_annotation_type): ?>
        <span class="heading">
            <?php print _t('Annotations are not supported for this type of media'); ?>
        </span>
    <?php else: ?>
        <?php print caEditorBundleShowHideControl($this->request, $vs_id_prefix.$t_item->tableNum().'_annotations'); ?>
        <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.$t_item->tableNum().'_annotations', $va_settings); ?>
        <?php $va_media_player_config = caGetMediaDisplayInfo('annotation_editor', $t_subject->getMediaInfo('media', $o_properties->getDisplayMediaVersion(), 'MIMETYPE')); ?>
        <div class="caAnnotationMediaPlayerContainer">
            <?php print $t_subject->getMediaTag('media', $o_properties->getDisplayMediaVersion(), array('class' => 'caAnnotationMediaPlayer', 'viewer_width' => $va_media_player_config['viewer_width'], 'viewer_height' => $va_media_player_config['viewer_height'], 'id' => 'annotation_media_player', 'poster_frame_url' => $t_subject->getMediaUrl('media', 'medium'))); ?>
        </div>
    <?php endif; ?>
</div>

<?php if($vb_has_annotation_type): ?>
    <div id="<?php print $vs_id_prefix.$t_item->tableNum().'_annotations'; ?>">
        <textarea class="caItemTemplate hidden">
            <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="annotationItem">
                <span class="formLabelError">{error}</span>
                <div class="representationAnnotationListItem row">
                    <div class="col-md-6">
                        <?php if(is_array($va_properties) && (sizeof($va_properties) > 0)): ?>
                            <div class="representationAnnotationListItem row">
                                <?php foreach($va_properties as $vs_property): ?>
                                    <div>
                                        <?php print $o_properties->htmlFormElement($vs_property, array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}".$vs_property."_{n}",  'name' => "{fieldNamePrefix}".$vs_property."_{n}", "value" => "{{".$vs_property."}}")); ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($vs_goto_property): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-default" onclick="mediaPlayerOnclick()" class="button" id="{fieldNamePrefix}gotoButton_{n}">
                                                <span class="glyphicon glyphicon-play"
                                                <?php print _t('Play Clip'); ?>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-2">
                        <?php print $t_item_label->htmlFormElement('name', null, array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}label_{n}", 'name' => "{fieldNamePrefix}label_{n}", "value" => "{{label}}", 'no_tooltips' => false, 'width' => 35,'textAreaTagName' => 'textentry')); ?>
                    </div>
					<div class="col-md-2">
                        <button type="button" class="btn btn-default" onclick="$('#{fieldNamePrefix}moreOptions_{n}').slideToggle(250); return false;">
                            <span class="glyphicon glyphicon-list"
                            <?php print _t('More'); ?>
                        </button>
                    </div>
					<div class="col-md-2">
						<button type="button" class="remove caDeleteItemButton">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
					</div>
				</div>
			</div>
			<div class="hidden" id="{fieldNamePrefix}moreOptions_{n}">
				<div class="representationAnnotationListItem row">
                    <div class="col-md-4">
                        <?php print $t_item_label->htmlFormElement('locale_id', null, array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}locale_id_{n}", 'name' => "{fieldNamePrefix}locale_id_{n}", "value" => "", 'no_tooltips' => false, 'WHERE' => array('(dont_use_for_cataloguing = 0)'))); ?>
                    </div>
                    <div class="col-md-4">
                        <?php print $t_item->htmlFormElement('status', null, array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}status_{n}", 'name' => "{fieldNamePrefix}status_{n}", "value" => "", 'no_tooltips' => false)); ?>
                    </div>
                    <div class="col-md-4">
                        <?php print $t_item->htmlFormElement('access', null, array('classname' => 'labelLocale', 'id' => "{fieldNamePrefix}access_{n}", 'name' => "{fieldNamePrefix}access_{n}", "value" => "", 'no_tooltips' => false)); ?>
                    </div>
                    <div class="col-md-4">
                        <a class="btn btn-default" href="<?php print urldecode(caNavUrl($this->request, 'editor/representation_annotations', 'RepresentationAnnotationEditor', 'Edit', array('annotation_id' => "{n}"))); ?>">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                    </div>
				</div>
			</div>
            <script>
                var mediaPlayerOnclick;
                (function($) {
                    $(function(){
                        mediaPlayerOnclick = function() {
                            var mediaPlayer = $('#annotation_media_player');
                            if (!mediaPlayer.data('hasBeenPlayed')) {
                                mediaPlayer[0].player.play();
                                mediaPlayer.data('hasBeenPlayed', true);
                            }
                            mediaPlayer[0].player.setCurrentTime(
                                (parseFloat({{startTimecode_raw}}) >= 0 ? parseFloat({{startTimecode_raw}}) : 0) +
                                <?php print $vn_timecode_offset; ?>
                            )
                        }
                    });
                })(jQuery);
            </script>
        </textarea>
        <div class="bundleContainer">
            <button type="btn btn-default labelInfo caAddItemButton">
                <span class="glyphicon glyphicon-plus"></span>
                <?php print _t("Add annotation"); ?>
            </button>
            <div class="caItemList"></div>
        </div>
    </div>

    <script>
        (function($) {
            $(function() {
                caUI.initBundle('#<?php print $vs_id_prefix.$t_item->tableNum() . '_annotations'; ?>', {
                    fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                    templateValues: ['status', 'access', 'locale_id', 'label', <?php print join(',', $va_prop_list); ?>],
                    initialValues: <?php print json_encode($va_inital_values); ?>,
                    initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                    sortInitialValuesBy: 'startTimecode_raw',
                    errors: <?php print json_encode($va_errors); ?>,
                    forceNewValues: <?php print json_encode($va_failed_inserts); ?>,
                    itemID: '<?php print $vs_id_prefix; ?>Item_',
                    templateClassName: 'caItemTemplate',
                    itemListClassName: 'caItemList',
                    addButtonClassName: 'caAddItemButton',
                    deleteButtonClassName: 'caDeleteItemButton',
                    showEmptyFormsOnLoad: 1,
                    showOnNewIDList: [],
                    hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_gotoButton_', '<?php print $vs_id_prefix; ?>_edit_'],
                    addMode: 'prepend',
                    incrementLocalesForNewBundles: false,
                    defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
                });
            })
        })(jQuery);
    </script>
<?php endif; ?>
