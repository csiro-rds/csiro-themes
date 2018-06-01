<?php
AssetLoadManager::register("panel");
$t_item = $this->getVar('t_item');
$vs_type_list = $this->getVar('type_list');
$t_object = new ca_objects();
?>
<div id="caCreateChildPanel" class="modal fade" data-toggle="modal">
    <div id="caCreateChildPanelContentArea" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><?php print _t('Create child record under this %1', $t_item->getProperty('NAME_SINGULAR')); ?></div>
            </div>
            <div class="modal-body">
                    <?php if ($vs_type_list): ?>
                    <div class="addChild">
                        <div class="addChildMessage"><?php print _t('Select a record type to add a child record under this one'); ?></div>
                        <?php print caFormTag($this->request, 'Edit', 'caNewChildForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                        <?php print _t('Add a %1 under this', $vs_type_list); ?>
                        <?php print caHTMLHiddenInput($t_item->primaryKey(), array('value' => '0')); ?>
                        <?php print caHTMLHiddenInput('parent_id', array('value' => $t_item->getPrimaryKey())); ?>
                        <div id="caTypeChangePanelControlButtons">
                            <button type="button" class="btn btn-default" onclick="caCreateChildPanel.hidePanel();">
                                <span class="glyphicon glyphicon-remove"></span>
                                <?php print _t('Cancel'); ?>
                            </button>
                            <button class="btn btn-success">
                                <span class="glyphicon glyphicon-ok" onclick="caCreateChildPanel.hidePanel()"></span>
                                <?php print _t('Save'); ?>
                            </button>
                        </div>
                        <?php print '</form>'; ?>
                    </div>
                <?php endif; ?>
                <?php if (($t_item->tableName() == 'ca_collections') && $this->request->config->get('ca_objects_x_collections_hierarchy_enabled')): ?>
                    <div class="addChild">
                        <?php print caFormTag($this->request, 'Edit', 'caNewChildObjectForm', 'editor/objects/ObjectEditor', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                        <?php print _t('Add a %1 under this', $vs_type_list); ?>
                        <?php print caHTMLHiddenInput('object_id', array('value' => '0')); ?>
                        <?php print caHTMLHiddenInput('collection_id', array('value' => $t_item->getPrimaryKey())); ?>
                        <div id="caTypeChangePanelControlButtons">
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-remove"></span>
                                <?php print _t('Cancel'); ?>
                            </button>
                            <button class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span>
                                <?php print _t('Save'); ?>
                            </button>
                        </div>
                        <?php print '</form>'; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    var caCreateChildPanel;
    var createChildPanelId = 'caCreateChildPanel';

    (function ($) {
        'use strict';

        $(function () {
            if (caUI.initPanel) {
                caCreateChildPanel = caUI.initPanel({
                    panelID: createChildPanelId,
                    panelContentID: createChildPanelId + "ContentArea",
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function () {
                        $('#' + createChildPanelId).modal('show');
                    },
                    onCloseCallback: function () {
                        $('#' + createChildPanelId).modal('hide');
                    }
                });
            }
        });
    }(jQuery));
</script>
