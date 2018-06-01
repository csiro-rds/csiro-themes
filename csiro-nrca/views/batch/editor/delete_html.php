<?php
$vs_module_path = $this->request->getModulePath();
$vs_controller = $this->request->getController();
$vb_confirmed = $this->getVar('confirmed');
$vs_delete_batch_url = caNavUrl($this->request, $vs_module_path, $vs_controller, 'Delete', array('set_id' => $this->getVar('set_id')));
$vs_cancel_batch_url = caNavUrl($this->request, $vs_module_path, $vs_controller, 'Edit', array('set_id' => $this->getVar('set_id')));
$t_set = $this->getVar('t_set');
if(!$vb_confirmed) {
    AssetLoadManager::register("sortableUI");
} else {
    require_once($this->request->getDirectoryPathForThemeFile('helpers/themeHelpers.php'));
}
?>
<div class="sectionBox">
<?php if (!$vb_confirmed): ?>
    <?php print caFormTag($this->request, 'Delete', 'caDeleteForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title"><?php print _t('Delete set'); ?></h4>
            </div>
            <div class="panel-body">
                <?php print _t('Do you really want to delete all records in %1?', $t_set->getLabelForDisplay()); ?>
            </div>
            <div class="panel-footer text-right">
                <div class="btn-group">
                    <a href="<?php print $vs_cancel_batch_url ?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-ban-circle"></span>
                        <?php print _t('Cancel'); ?>
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Delete'); ?>
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" name="confirm" value="1"/>
        <input type="hidden" name="<?php print $t_set->primaryKey(); ?>" value="<?php print $t_set->getPrimaryKey(); ?>"/>
    <?php print "</form>"; ?>
<?php else: ?>
    <div id="batchResults" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="inline-panel-title">
                <?php print _t('Batch processing'); ?>
            </h3>
            <label id="titleStatus" class="label"></label>
        </div>
        <div class="panel-body">
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span>0%</span>
                </div>
            </div>
            <div id="batchProcessingAccordion" class="panel-group" role="tablist" aria-multiselectable="true"></div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-offset-4 col-md-4 text-center">
                    <div id="batchProcessingElapsedTime">
                        <span>N/A seconds</span>
                    </div>
                    <div id="batchProcessingMemoryUsage">
                        <span>0.00MB</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="countLabels">
                        <div class="text-right processed">
                            <label class="label label-info">0 processed</label>
                        </div>
                        <div class="text-right errors">
                            <label class="label label-danger">0 errors</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once($this->request->getDirectoryPathForThemeFile('views/batch/common/batchEditorResults.php')) ?>
    <script>
        var updateProgress;
        var appendResults;
        (function($) {
            var $batchResults = $('#batchResults');
            updateProgress = caUI.initBatchEditorProgressBar($batchResults.find('.progress-bar'), $batchResults.find('.panel-heading .label'), $('#countLabels'));
            appendResults = caUI.initBatchEditorResults($('#batchProcessingAccordion'));
        }(jQuery));
    </script>
<?php endif; ?>
