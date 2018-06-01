<?php
$t_item = $this->getVar('t_item');
$vs_typename = $t_item->getTypeName();
AssetLoadManager::register("panel");
?>

<div id="caTypeChangePanel" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php print caFormTag($this->request, 'ChangeType', 'caChangeTypeForm', null, $ps_method='post', 'multipart/form-data', '_top', array()); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <h3 class="modal-title">
                        <?php print _t('Change %1 type', $t_item->getProperty('NAME_SINGULAR')); ?>
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <p>
                            <span class="glyphicon glyphicon-warning-sign"></span>
                            <?php print _t('<strong>Warning</strong>'); ?>
                        </p>
                        <p>
                            <?php print _t('Changing the %1 type will cause information in all fields not applicable to the new type to be discarded. This action cannot be undone.', $t_item->getProperty('NAME_SINGULAR')); ?>
                        </p>
                    </div>
                    <label>
                        <?php print _t('Change type to:'); ?>
                    </label>
                    <?php print $t_item->getTypeListAsHTMLFormElement('type_id', array('id' => 'caChangeTypeFormTypeID'), array('omitItemsWithID' => array($t_item->getTypeID()), 'childrenOfCurrentTypeOnly' => false, 'directChildrenOnly' => false, 'returnHierarchyLevels' => true, 'access' => __CA_BUNDLE_ACCESS_EDIT__)); ?>
                </div>
                <div class="modal-footer">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php print _t('Cancel'); ?>
                        </button>
                        <button class="btn btn-success">
                            <span class="glyphicon glyphicon-save"></span>
                            <?php print _t('Save'); ?>
                        </button>
                    </div>
                    <?php print caHTMLHiddenInput($t_item->primaryKey(), array('value' => $t_item->getPrimaryKey())); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var caTypeChangePanel;

    (function ($) {
        'use strict';

        $(function() {
            if (caUI.initPanel) {
                caTypeChangePanel = caUI.initPanel({
                    panelID: "caTypeChangePanel",
                    panelContentID: "caTypeChangePanelContentArea",
                    useExpose: false,
                    automaticFade: false,
                    onOpenCallback: function() {
                        $('#caTypeChangePanel').modal('show');
                    },
                    onCloseCallback: function() {
                        $('#caTypeChangePanel').modal('hide');
                    }
                });
            }
        });
    }(jQuery));
</script>
