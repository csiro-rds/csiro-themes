<?php
AssetLoadManager::register('sortableUI');

$vs_id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance 		= $this->getVar('t_instance');
$t_item 			= $this->getVar('t_item');			// object representation
$t_item_label		= $t_item->getLabelTableInstance();
$t_item_rel 		= $this->getVar('t_item_rel');
$t_subject 			= $this->getVar('t_subject');		// object
$vs_add_label 		= $this->getVar('add_label');
$va_settings 		= $this->getVar('settings');

$vb_read_only		=	(isset($va_settings['readonly']) && $va_settings['readonly']);
$vb_batch			=	$this->getVar('batch');
$vb_allow_fetching_from_urls = (bool)$this->request->getAppConfig()->get('allow_fetching_of_media_from_remote_urls');
$vb_allow_fetching_from_existing = (bool)$this->request->getAppConfig()->get($t_subject->tableName().'_allow_relationships_to_existing_representations');

// Paging
$vn_start = 0;
$vn_num_per_page = 20;
$vn_primary_id = 0;

// generate list of inital form values; the bundle Javascript call will use the template to generate the initial form
$va_rep_type_list = $t_item->getTypeList();
$va_errors = array();

$vn_rep_count = $t_subject->getRepresentationCount($va_settings);
$va_initial_values = caSanitizeArray($t_subject->getBundleFormValues($this->getVar('bundle_name'), $this->getVar('placement_code'),
$va_settings, array('start' => 0, 'limit' => $vn_num_per_page, 'request' => $this->request)), ['removeNonCharacterData' => false]);

// New relationship variables
$vs_rel_dir = ($t_item_rel->getLeftTableName() == $t_subject->tableName()) ? 'ltol' : 'rtol';
$vn_left_sub_type_id = ($t_item_rel->getLeftTableName() == $t_subject->tableName()) ? $t_subject->get('type_id') : null;
$vn_right_sub_type_id = ($t_item_rel->getRightTableName() == $t_subject->tableName()) ? $t_subject->get('type_id') : null;

$downloadAllUrl = caNavUrl($this->request, '*', '*', 'DownloadMedia', array($t_subject->primaryKey() => $t_subject->getPrimaryKey()));
$getMediaPanelUrl = function ($n) {
    return urldecode(caNavUrl($this->request, 'editor/objects',
        'ObjectEditor',
        'GetMediaOverlay',
        array('object_id' => $this->getVar('t_subject')->getPrimaryKey(),
        'representation_id' => $n)));
};

if (sizeof(caGetTypeListForUser('ca_object_representations', array('access' => __CA_BUNDLE_ACCESS_EDIT__)))  < 1) {
    $vb_read_only = true;
}

if (!in_array($vs_default_upload_type = $this->getVar('defaultRepresentationUploadType'), array('upload', 'url', 'search'))) {
    $vs_default_upload_type = 'upload';
}

foreach ($va_initial_values as $vn_representation_id => $va_rep) {
    if (is_array($va_action_errors = $this->request->getActionErrors('ca_object_representations', $vn_representation_id))) {
        foreach ($va_action_errors as $o_error) {
            $va_errors[$vn_representation_id][] = array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber());
        }
    }
    if ($va_rep['is_primary']) {
        print $vn_primary_id = $va_rep['representation_id'];
    }
}

$va_failed_inserts = array();
foreach ($this->request->getActionErrorSubSources('ca_object_representations') as $vs_error_subsource) {
    if (substr($vs_error_subsource, 0, 4) === 'new_') {
        $va_action_errors = $this->request->getActionErrors('ca_object_representations', $vs_error_subsource);
        foreach ($va_action_errors as $o_error) {
            $va_failed_inserts[] = array('icon' => '', '_errors' => array(array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber())));
        }
    }
}
?>

<div id="<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>" class="component component-bundle component-bundle-object-representations">
    <textarea class='representation-template hidden'>
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="representation repeating-item">
            <div class="elements-container removable">
                <div class="row">
                    <div class="col-md-3">
                        <div class="caObjectRepresentationListItemImageThumb">
                            <a href="#" onclick="caMediaPanel.showPanel('<?php print $getMediaPanelUrl('{n}'); ?>');">{icon}</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div id="{fieldNamePrefix}rep_info_parent{n}" class="image-properties col-md-10">
                                <div id='{fieldNamePrefix}rep_info_ro{n}' class="panel panel-default collapse in">
                                    <div class="panel-heading clearfix">
                                        <div class="pull-right">
                                            <span class='label label-info'>{is_primary_display}</span>
                                        </div>
                                        <h2 class="panel-title">{rep_label}</h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class='col-md-6'>
                                                <div class="inline-header col-md-4"><?php print _t('File name'); ?></div>
                                                <div class="inline-item col-md-8" id="{fieldNamePrefix}filename_display_{n}">
                                                    {filename}
                                                </div>
                                                <div class="inline-header  col-md-4"><?php print _t('Format'); ?></div>
                                                <div class="inline-item col-md-8">{type}</div>
                                                <div class="inline-header col-md-4"><?php print _t('Dimensions'); ?></div>
                                                <div class="inline-item col-md-8">{dimensions}; {num_multifiles}</div>
                                                <?php TooltipManager::add("#{$vs_id_prefix}_filename_display_{n}", _t('File name: %1', "{{filename}}"), 'bundle_ca_object_representations'); ?>
                                            </div>
                                            <div class='col-md-6'>
                                                <div class="inline-header col-md-4"><?php print _t('Type'); ?></div>
                                                <div class="inline-item col-md-8">{rep_type}</div>
                                                <div class="inline-header col-md-4"><?php print _t('Access'); ?></div>
                                                <div class="inline-item col-md-8">{access_display}</div>
                                                <div class="inline-header col-md-4"><?php print _t('Status'); ?></div>
                                                <div class="inline-item col-md-8">{status_display}</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id='{fieldNamePrefix}is_primary_indicator_{n}' class="collapse is-primary alert alert-info">
                                                    <span class="glyphicon glyphicon-info-sign"></span>
                                                    <?php print _t('Will be primary after save'); ?>
                                                </div>
                                                <input type="hidden" name="{fieldNamePrefix}is_primary_{n}" id="{fieldNamePrefix}is_primary_{n}" class="{fieldNamePrefix}is_primary" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!$vb_read_only): ?>
                                    <div id="{fieldNamePrefix}detail_editor_{n}" class="panel panel-default collapse">
                                        <div class="panel-heading">
                                            <h2 class="panel-title"><?php print _t('Quick Edit'); ?></h2>
                                        </div>
                                        <div class="panel-body">
                                            <div class="caObjectRepresentationDetailEditorElement">
                                                <?php print $t_item_label->htmlFormElement('name', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                                     'id' => "{fieldNamePrefix}rep_label_{n}", 'name' => "{fieldNamePrefix}rep_label_{n}", "value" => "{rep_label}", 'no_tooltips' => false,
                                                     'tooltip_namespace' => 'bundle_ca_object_representations', 'textAreaTagName' => 'textentry')); ?>
                                            </div>
                                            <div class="caObjectRepresentationDetailEditorElement clearfix">
                                                <?php print $t_item->htmlFormElement('idno', null, array('classname' => 'caObjectRepresentationDetailEditorElement', 'id' => "{fieldNamePrefix}idno_{n}",
                                                    'name' => "{fieldNamePrefix}idno_{n}", "value" => "{idno}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                                            </div>
                                            <div class="caObjectRepresentationDetailEditorElement">
                                                <?php print $t_item->htmlFormElement('access', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                                    'id' => "{fieldNamePrefix}access_{n}", 'name' => "{fieldNamePrefix}access_{n}", "value" => "{access}", 'no_tooltips' => false,
                                                    'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                                            </div>
                                            <div class="caObjectRepresentationDetailEditorElement clearfix">
                                                <?php print $t_item->htmlFormElement('status', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                                    'id' => "{fieldNamePrefix}status_{n}", 'name' => "{fieldNamePrefix}status_{n}", "value" => "{status}", 'no_tooltips' => false,
                                                    'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                                            </div>
                                            <label><?php print _t('Update media'); ?></label>
                                            <div id="{fieldNamePrefix}upload_options{n}">
                                                <?php if ($vb_allow_fetching_from_urls): ?>
                                                    <div>
                                                        <?php print caHTMLRadioButtonInput('{fieldNamePrefix}upload_type{n}',
                                                                array('id' => '{fieldNamePrefix}upload_type_upload{n}', 'class' => '{fieldNamePrefix}upload_type{n}', 'value' => 'upload'),
                                                                array('checked' => ($vs_default_upload_type == 'upload') ? 1 : 0)).' '._t('using upload'); ?>
                                                        <?php print caHTMLRadioButtonInput('{fieldNamePrefix}upload_type{n}',
                                                                array('id' => '{fieldNamePrefix}upload_type_url{n}', 'class' => '{fieldNamePrefix}upload_type{n}', 'value' => 'url'),
                                                            array('checked' => ($vs_default_upload_type == 'url') ? 1 : 0)).' '._t('from URL'); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="hidden" name="{fieldNamePrefix}upload_type_upload{n}" value="upload"/>
                                                <?php endif; ?>
                                                <div>
                                                    <?php print $t_item->htmlFormElement('media', '^ELEMENT', array('name' => "{fieldNamePrefix}media_{n}",
                                                        'id' => "{fieldNamePrefix}media_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations', 'class' => 'uploadInput')); ?>
                                                    <?php if ($vb_allow_fetching_from_urls): ?>
                                                        <?php print caHTMLTextInput("{fieldNamePrefix}media_url_{n}",
                                                            array('id' => '{fieldNamePrefix}media_url_{n}', 'class' => 'urlBg uploadInput')); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <script>
                                                (function($) {
                                                    $(function(){
                                                        $("#{fieldNamePrefix}upload_options{n} .uploadInput").hide();
                                                        $("#{fieldNamePrefix}upload_type_upload{n}").click(function() {
                                                            $("#{fieldNamePrefix}media_{n}").show();
                                                            $("#{fieldNamePrefix}media_url_{n}").hide();
                                                            $("#{fieldNamePrefix}autocomplete{n}").hide();
                                                        });
                                                        $("#{fieldNamePrefix}upload_type_url{n}").click(function() {
                                                            $("#{fieldNamePrefix}media_{n}").hide();
                                                            $("#{fieldNamePrefix}media_url_{n}").show();
                                                            $("#{fieldNamePrefix}autocomplete{n}").hide();
                                                        });
                                                        $("input.{fieldNamePrefix}upload_type{n}:checked").click();
                                                    });
                                                })(jQuery);
                                            </script>
                                        </div>
                                        <div class="panel-footer text-right">
                                            <button class="btn btn-success">
                                                <span class="glyphicon glyphicon-ok" title="<?php print _t('Save') ?>"></span>
                                                <?php print _t('Save') ?>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="image-options col-md-2">
                                <div class="btn-group-vertical">
                                    <button type="button" class="btn btn-default" onclick='caSetRepresentationAsPrimary("{n}");'>
                                        <span class="glyphicon glyphicon-ok"></span>
                                        <?php print _t('Make primary'); ?>
                                    </button>
                                    <?php if (!$vb_read_only): ?>
                                        <button type="button" id="{fieldNamePrefix}change_{n}" class="btn btn-default" data-toggle="collapse" data-target="#{fieldNamePrefix}detail_editor_{n}" data-parent="#{fieldNamePrefix}rep_info_parent{n}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                            <?php print _t('Quick Edit'); ?>
                                        </button>
                                        <a class="btn btn-default" href="<?php print urldecode(caNavUrl($this->request,
                                                'editor/object_representations',
                                                'ObjectRepresentationEditor',
                                                'Edit',
                                                array('representation_id' => "{n}")
                                            )) ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                            <?php print _t('Edit'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($this->request->getUser()->canDoAction('can_download_ca_object_representations')): ?>
                                        <a class="btn btn-default" href="<?php print urldecode(caNavUrl($this->request,
                                            '*',
                                            '*',
                                            'DownloadMedia',
                                            array('version' => 'original',
                                                'representation_id' => '{n}',
                                                $t_subject->primaryKey() => $t_subject->getPrimaryKey(),
                                                'download' => 1))) ?>">
                                            <span class="glyphicon glyphicon-download"></span>
                                            <?php print _t('Download') ?>
                                        </a>
                                    <?php endif; ?>
                                    <button id="{fieldNamePrefix}edit_annotations_button_{n}" class="btn btn-default caAnnoEditorLaunchButton annotationTypeClip{annotation_type}">
                                        <span id="{fieldNamePrefix}edit_annotations_{n}" class="glyphicon glyphicon-time"></span>
                                        <?php print _t('Annotations') ?>
                                    </button>
                                    <button id="caSetImageCenterLaunchButton{n}" class="btn btn-default caSetImageCenterLaunchButton annotationTypeSetCenter{annotation_type}">
                                        <span class="glyphicon glyphicon-screenshot"></span>
                                        <?php print _t('Set center'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="{fieldNamePrefix}media_replication_container_{n}" class="hidden">
                            <div class="caRepresentationMediaReplicationButton">
                                <a href="#" id="{fieldNamePrefix}caRepresentationMediaReplicationButton_{n}"
                                   onclick="caToggleDisplayMediaReplication('{fieldNamePrefix}media_replication{n}',
                                        '{fieldNamePrefix}caRepresentationMediaReplicationButton_{n}', '{n}');
                                        return false;"
                                   class="caRepresentationMediaReplicationButton">
                                    <span>
                                        <?php /*todo: use glpyhicon-list?*/ _t('Replication'); ?>
                                    </span>
                                </a>
                            </div>
                            <div>
                                <div id="{fieldNamePrefix}media_replication{n}" class="caRepresentationMediaReplication">
                                    <?php print caBusyIndicatorIcon($this->request).' '._t('Loading'); ?>
                                </div>
                            </div>
                        </div>
                        <div id="{fieldNamePrefix}media_metadata_container_{n}" class="mediaMetadata">
                            <button type="button" class="btn btn-default"
                                    onclick="caToggleDisplayObjectRepresentationMetadata('{fieldNamePrefix}media_metadata_{n}');">
                                <span class="glyphicon glyphicon-info-sign"></span>
                                <?php print _t('Media metadata'); ?>
                                <span class="caret"></span>
                            </button>
                            <div id="{fieldNamePrefix}media_metadata_{n}" class="well well-sm hidden-initial">
                                <div class="metadata-item">{fetched}</div>
                                <div class="metadata-item">{md5}</div>
                                <div class="metadata-item">{metadata}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="remove" title="remove">
                <?php print _t('Remove') ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
        </div>

        <script>
            (function($) {
                'use strict';

                $(function() {
                    $('.caAnnoEditorLaunchButton').click(function() {
                        caAnnoEditor<?php print $vs_id_prefix; ?>.showPanel('<?php print urldecode(
                            caNavUrl($this->request,
                                'editor/object_representations',
                                'ObjectRepresentationEditor',
                                'GetAnnotationEditor',
                                array('representation_id' => '{n}'))); ?>'
                        );
                        return false;
                    });
                    $('#caSetImageCenterLaunchButton{n}').click(function() {
                        caImageCenterEditor<?php print $vs_id_prefix; ?>.showPanel('<?php print urldecode(
                            caNavUrl(
                                $this->request,
                                'editor/object_representations',
                                'ObjectRepresentationEditor',
                                'GetImageCenterEditor',
                                array('representation_id' => '{n}'))); ?>',
                            caSetImageCenterForSave<?php print $vs_id_prefix; ?>,
                            true,
                            {},
                            {'id': '{n}'}
                        );
                        return false;
                    });

                    if (caMediaReplicationMimeTypes.indexOf('{mimetype}') !== -1) {
                        $("#{fieldNamePrefix}media_replication_container_{n}").css("display", "block");
                    }
                });
            }(jQuery));
        </script>

        <?php print TooltipManager::getLoadHTML('bundle_ca_object_representations'); ?>
        <!-- image center coordinates -->
        <input type="hidden" name="<?php print $vs_id_prefix; ?>_center_x_{n}" id="<?php print $vs_id_prefix; ?>_center_x_{n}" value="{center_x}"/>
        <input type="hidden" name="<?php print $vs_id_prefix; ?>_center_y_{n}" id="<?php print $vs_id_prefix; ?>_center_y_{n}" value="{center_y}"/>
    </textarea>

    <textarea class='representation-new-item-template hidden'>
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="representation repeating-item">
            <div class="elements-container removable">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">
                            <?php print ($t_item_rel->hasField('type_id')) ? _t('Add representation with relationship type %1', $t_item_rel->getRelationshipTypesAsHTMLSelect($vs_rel_dir, $vn_left_sub_type_id, $vn_right_sub_type_id, array('name' => '{fieldNamePrefix}type_id_{n}'), $va_settings)) : _t('Add representation'); ?>
                        </h2>
                    </div>
                    <div class="panel-body">
                        <span class="formLabelError">{error}</span>
                        <div id="{fieldNamePrefix}detail_editor_{n}">
                            <div class="caObjectRepresentationDetailEditorElement">
                                <?php print $t_item_label->htmlFormElement('name', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                    'id' => "{fieldNamePrefix}rep_label_{n}", 'name' => "{fieldNamePrefix}rep_label_{n}", "value" => "{rep_label}", 'no_tooltips' => false,
                                    'tooltip_namespace' => 'bundle_ca_object_representations', 'textAreaTagName' => 'textentry')); ?>
                            </div>
                            <div class="caObjectRepresentationDetailEditorElement">
                                <?php print $t_item->htmlFormElement('type_id', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                    'id' => "{fieldNamePrefix}rep_type_id_{n}", 'name' => "{fieldNamePrefix}rep_type_id_{n}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                            </div>
                            <div class="caObjectRepresentationDetailEditorElement">
                                <?php print $t_item->htmlFormElement('access', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                    'id' => "{fieldNamePrefix}access_{n}", 'name' => "{fieldNamePrefix}access_{n}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                            </div>
                            <div class="caObjectRepresentationDetailEditorElement">
                                <?php print $t_item->htmlFormElement('status', null, array('classname' => 'caObjectRepresentationDetailEditorElement',
                                    'id' => "{fieldNamePrefix}status_{n}", 'name' => "{fieldNamePrefix}status_{n}", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
                            </div>
                        </div>
                        <div id="{fieldNamePrefix}upload_options{n}">
                            <?php if ($vb_allow_fetching_from_urls || $vb_allow_fetching_from_existing): ?>
                                <div>
                                    <?php print caHTMLRadioButtonInput('{fieldNamePrefix}upload_type{n}',
                                            array('id' => '{fieldNamePrefix}upload_type_upload{n}', 'class' => '{fieldNamePrefix}upload_type{n}', 'value' => 'upload'),
                                            array('checked' => ($vs_default_upload_type === 'upload') ? 1 : 0)); ?>
                                    <label for="{fieldNamePrefix}upload_type_upload{n}"><?php print _t('upload'); ?></label>
                                    <?php if ($vb_allow_fetching_from_urls): ?>
                                        <?php print caHTMLRadioButtonInput('{fieldNamePrefix}upload_type{n}',
                                                array('id' => '{fieldNamePrefix}upload_type_url{n}', 'class' => '{fieldNamePrefix}upload_type{n}', 'value' => 'url'),
                                                array('checked' => ($vs_default_upload_type === 'url') ? 1 : 0)); ?>
                                        <label for="{fieldNamePrefix}upload_type_url{n}"><?php print _t('from URL'); ?></label>
                                    <?php endif; ?>
                                    <?php if ($vb_allow_fetching_from_existing): ?>
                                        <?php print caHTMLRadioButtonInput('{fieldNamePrefix}upload_type{n}',
                                                array('id' => '{fieldNamePrefix}upload_type_search{n}', 'class' => '{fieldNamePrefix}upload_type{n}', 'value' => 'search'),
                                                array('checked' => ($vs_default_upload_type === 'search') ? 1 : 0)); ?>
                                        <label for="{fieldNamePrefix}upload_type_search{n}"><?php print _t('using existing'); ?></label>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="{fieldNamePrefix}upload_type_upload{n}" value="upload"/>
                            <?php endif; ?>
                            <div>
                                <?php print $t_item->htmlFormElement('media', '^ELEMENT', array('name' => "{fieldNamePrefix}media_{n}", 'id' => "{fieldNamePrefix}media_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations', 'class' => 'uploadInput')); ?>
                                <?php if ($vb_allow_fetching_from_urls): ?>
                                    <?php print caHTMLTextInput("{fieldNamePrefix}media_url_{n}", array('id' => '{fieldNamePrefix}media_url_{n}', 'class' => 'urlBg uploadInput')); ?>
                                <?php endif; ?>
                                <?php if ($vb_allow_fetching_from_existing): ?>
                                    <?php print caHTMLTextInput('{fieldNamePrefix}autocomplete{n}', array('value' => '{{label}}', 'id' => '{fieldNamePrefix}autocomplete{n}', 'class' => 'lookupBg uploadInput')); ?>
                                    <?php if ($t_item_rel && $t_item_rel->hasField('type_id')): ?>
                                        <select name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" class="hidden"></select>
                                    <?php endif; ?>
                                    <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="remove" title="<?php print _t('Delete'); ?>">
                <?php _t('Delete'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
        </div>

        <script>
            (function($) {
                'use strict';

                $(function() {
                    $("#{fieldNamePrefix}upload_options{n} .uploadInput").hide();
                    $("#{fieldNamePrefix}upload_type_upload{n}").click(function() {
                        $("#{fieldNamePrefix}media_{n}").show();
                        $("#{fieldNamePrefix}media_url_{n}").hide();
                        $("#{fieldNamePrefix}autocomplete{n}").hide();
                        $('#{fieldNamePrefix}detail_editor_{n}').collapse('show');
                    });
                    $("#{fieldNamePrefix}upload_type_url{n}").click(function() {
                        $("#{fieldNamePrefix}media_{n}").hide();
                        $("#{fieldNamePrefix}media_url_{n}").show();
                        $("#{fieldNamePrefix}autocomplete{n}").hide();
                        $('#{fieldNamePrefix}detail_editor_{n}').collapse('show');
                    });
                    $("#{fieldNamePrefix}upload_type_search{n}").click(function() {
                        $("#{fieldNamePrefix}media_{n}").hide();
                        $("#{fieldNamePrefix}media_url_{n}").hide();
                        $("#{fieldNamePrefix}autocomplete{n}").show();
                        $('#{fieldNamePrefix}detail_editor_{n}').collapse('hide');
                    });
                    $("input.{fieldNamePrefix}upload_type{n}:checked").click();
                });
            }(jQuery));
        </script>
    </textarea>

    <?php if ($vb_batch): ?>
        <?php print caBatchEditorRelationshipModeControl($t_item, $vs_id_prefix); ?>
    <?php endif; ?>
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix . $t_item->tableNum() . '_rel', $va_settings); ?>

    <div class="bundleContainer">
        <div class="item-list"></div>
        <div class="btn-group">
            <?php if (!$vb_read_only): ?>
                <button type="button" class="caAddItemButton btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span>
                    <span><?php print $vs_add_label ?: _t("Add Representation") ?></span>
                </button>
            <?php endif; ?>
            <a class="btn btn-default" href="<?php print $downloadAllUrl ?>">
                <span class="glyphicon glyphicon-download"></span>
                <?php print _t('Download All') ?>
            </a>
        </div>
        <input type="hidden" id="<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList" name="<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList" value=""/>
    </div>

    <div id="caAnnoEditor<?php print $vs_id_prefix; ?>" class="modal fade" data-keyboard="true" data-toggle="modal">
        <div id="caAnnoEditor<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
    </div>

    <div id="caImageCenterEditor<?php print $vs_id_prefix; ?>" class="modal fade" data-toggle="modal" data-keyboard="true">
        <div id="caImageCenterEditor<?php print $vs_id_prefix; ?>ContentArea" class="modal-dialog modal-lg"></div>
    </div>
</div>

<script>
    var caToggleDisplayObjectRepresentationMetadata, caToggleDisplayMediaReplication, caOpenRepresentationDetailEditor,
        caCloseRepresentationDetailEditor, caSetRepresentationAsPrimary, caAnnoEditor<?php print $vs_id_prefix; ?>,
        caImageCenterEditor<?php print $vs_id_prefix; ?>, caRelationBundle<?php print $vs_id_prefix; ?>,
        caSetImageCenterForSave<?php print $vs_id_prefix; ?>, caMediaReplicationMimeTypes;

    caMediaReplicationMimeTypes = <?php print json_encode(MediaReplicator::getMediaReplicationMimeTypes()); ?>;

    (function ($) {
        'use strict';

        $(function () {
            caToggleDisplayObjectRepresentationMetadata = function (media_metadata_id) {
                $('#' + media_metadata_id).slideToggle(300);
                return false;
            };

            caToggleDisplayMediaReplication = function (media_replication_id, media_replication_button_id, n) {
                media_replication.slideToggle(300, function() {
                    if (media_replication.css('display') == 'block') {
                        media_replication.load('<?php print caNavUrl($this->request, $this->request->getModulePath(),
                            $this->request->getController(), 'MediaReplicationControls', array('representation_id' => '')); ?>' + n);
                    }
                });
                return false;
            };

            caOpenRepresentationDetailEditor = function (id) {
                $('#<?php print $vs_id_prefix; ?>_detail_editor_' + id).slideDown(250);
                $('#<?php print $vs_id_prefix; ?>_rep_info_ro' + id).slideUp(250);
            };

            caCloseRepresentationDetailEditor = function (id) {
                $('#<?php print $vs_id_prefix; ?>_detail_editor_' + id).slideUp(250);
                $('#<?php print $vs_id_prefix; ?>_rep_info_ro' + id).slideDown(250);
                $('#<?php print $vs_id_prefix; ?>_change_indicator_' + id).show();
            };

            caSetRepresentationAsPrimary = function (id) {
                $('.<?php print $vs_id_prefix; ?>_is_primary').val('');
                $('#<?php print $vs_id_prefix; ?>_is_primary_' + id).val('1');
                $('.is-primary').collapse('hide');
                if (id !== '<?php print (int)$vn_primary_id; ?>') {
                    $('#<?php print $vs_id_prefix; ?>_is_primary_indicator_' + id).collapse('show');
                }
            };

            caSetImageCenterForSave<?php print $vs_id_prefix; ?> = function(data) {
                var id = data['id'];
                $('#<?php print $vs_id_prefix; ?>_change_indicator_' + id).show();

                var center_x = parseInt($('#caObjectRepresentationSetCenterMarker').css('left'), 10) / parseInt($('#caImageCenterEditorImage').width(), 10);
                var center_y = parseInt($('#caObjectRepresentationSetCenterMarker').css('top'), 10) / parseInt($('#caImageCenterEditorImage').height(), 10);
                $('#<?php print $vs_id_prefix; ?>_center_x_' + id).val(center_x);
                $('#<?php print $vs_id_prefix; ?>_center_y_' + id).val(center_y);
            };
            caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: ['_display', 'status', 'access', 'access_display', 'is_primary', 'is_primary_display', 'media', 'locale_id', 'icon', 'type', 'dimensions', 'filename', 'num_multifiles', 'metadata', 'rep_type_id', 'type_id', 'typename', 'fetched', 'label', 'rep_label', 'idno', 'id', 'fetched_from','mimetype', 'center_x', 'center_y'],
                initialValues: <?php print json_encode($va_initial_values); ?>,
                initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                errors: <?php print json_encode($va_errors); ?>,
                forceNewValues: <?php print json_encode($va_failed_inserts); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                templateClassName: 'representation-new-item-template',
                initialValueTemplateClassName: 'representation-template',
                itemListClassName: 'item-list',
                itemClassName: 'representation',
                addButtonClassName: 'caAddItemButton',
                deleteButtonClassName: 'remove',
                showOnNewIDList: ['<?php print $vs_id_prefix; ?>_media_'],
                hideOnNewIDList: [
                    '<?php print $vs_id_prefix; ?>_edit_',
                    '<?php print $vs_id_prefix; ?>_download_',
                    '<?php print $vs_id_prefix; ?>_media_metadata_container_',
                    '<?php print $vs_id_prefix; ?>_edit_annotations_',
                    '<?php print $vs_id_prefix; ?>_edit_image_center_'
                ],
                enableOnNewIDList: [],
                showEmptyFormsOnLoad: 1,
                readonly: <?php print json_encode($vb_read_only); ?>,
                isSortable: <?php print json_encode(!$vb_read_only); ?>,
                listSortOrderID: '<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList',
                defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>,

                relationshipTypes: <?php print json_encode($this->getVar('relationship_types_by_sub_type')); ?>,
                autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'ObjectRepresentation', 'Get'); ?>',
                autocompleteInputID: '<?php print $vs_id_prefix; ?>autocomplete',

                extraParams: { exact: 1 },

                minRepeats: <?php print caGetOption('minRelationshipsPerRow', $va_settings, 0); ?>,
                maxRepeats: <?php print caGetOption('maxRelationshipsPerRow', $va_settings, 65535); ?>,

                totalValueCount: <?php print (int)$vn_rep_count; ?>,
                partialLoadUrl: '<?php print caNavUrl($this->request, '*', '*', 'loadBundles', array($t_subject->primaryKey() => $t_subject->getPrimaryKey(), 'placement_id' => $va_settings['placement_id'], 'bundle' => 'ca_object_representations')); ?>',
                loadSize: <?php print $vn_num_per_page; ?>,
                partialLoadMessage: '<?php print addslashes(_t('Load next %')); ?>',
                partialLoadIndicator: '<?php print addslashes(caBusyIndicatorIcon($this->request)); ?>',
                onPartialLoad: function(d) {
                    // Hide annotation editor links for non-timebased media
                    $(".caAnnoEditorLaunchButton").hide();
                    $(".annotationTypeClipTimeBasedVideo, .annotationTypeClipTimeBasedAudio").show();
                }
            });
            <?php if (caUI.initPanel): ?>
                caAnnoEditor<?php print $vs_id_prefix; ?> = caUI.initPanel({
                    panelID: "caAnnoEditor<?php print $vs_id_prefix; ?>",
                    panelContentID: "caAnnoEditor<?php print $vs_id_prefix; ?>ContentArea",
                    useExpose: false,
                    initialFadeIn: false,
                    onOpenCallback: function () {
                        $('#caAnnoEditor<?php print $vs_id_prefix; ?>').modal('show');
                    },
                    onCloseCallback: function () {
                        $('#caAnnoEditor<?php print $vs_id_prefix; ?>').modal('hide');
                    }
                });

                caImageCenterEditor<?php print $vs_id_prefix; ?> = caUI.initPanel({
                    panelID: "caImageCenterEditor<?php print $vs_id_prefix; ?>",
                    panelContentID: "caImageCenterEditor<?php print $vs_id_prefix; ?>ContentArea",
                    useExpose: false,
                    initialFadeIn: false,
                    onOpenCallback: function () {
                        $('#caImageCenterEditor<?php print $vs_id_prefix; ?>').modal('show');
                    },
                    onCloseCallback: function () {
                        $('#caImageCenterEditor<?php print $vs_id_prefix; ?>').modal('hide');
                    }
                });
            <?php endif; ?>

            // Hide annotation editor links for non-timebased media
            $(".caAnnoEditorLaunchButton").hide();
            $(".annotationTypeClipTimeBasedVideo, .annotationTypeClipTimeBasedAudio").show();

            $(".caSetImageCenterLaunchButton").hide();
            $(".annotationTypeSetCenterImage").show();
        });
    }(jQuery));
</script>
