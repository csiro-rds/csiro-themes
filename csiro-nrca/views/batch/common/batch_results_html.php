<?php
AssetLoadManager::register("sortableUI");

$vs_action_extra = !!$this->request->getActionExtra() ? '/' . $this->request->getActionExtra() : '';

$vs_new_batch_url = caNavUrl($this->request,
    'batch',
    $this->request->getController(),
    'Edit' . $vs_action_extra,
    array('set_id' => $this->getVar('set_id')));
?>
<?php error_log(json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))) ?>
<?php require_once($this->request->getDirectoryPathForThemeFile('helpers/themeHelpers.php')); ?>

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
            <div class="col-md-4">
                <a href="<?php print $vs_new_batch_url ?>" class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php print _t('Perform another %1', $this->getVar('batchType')); ?>
                </a>
                <?php if($vs_edit_batch_url): ?>
                    <a href="<?php print $vs_edit_batch_url ?>" class="btn btn-secondary">
                        <span class="glyphicon glyphicon-edit"></span>
                        <?php print _t('Batch edit set'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-center">
                <div class="row">
                    <div id="batchProcessingElapsedTime" class="col-md-12">
                        <span>N/A seconds</span>
                    </div>
                    <div id="batchProcessingMemoryUsage" class="col-md-12">
                        <span>0.00MB</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div id="countLabels" class="row">
                    <div class="col-md-12 text-right processed">
                        <label class="label label-info">0 processed</label>
                    </div>
                    <div class="col-md-12 text-right errors">
                        <label class="label label-danger">0 errors</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php print $this->render($this->request->getDirectoryPathForThemeFile('views/batch/common/batchEditorResults.php')); ?>


<script>
    var updateProgress;
    var appendResults;

    (function($) {
        var $batchResults = $('#batchResults');
        updateProgress = caUI.initBatchEditorProgressBar($batchResults.find('.progress-bar'), $batchResults.find('.panel-heading .label'), $('#countLabels'));
        appendResults = caUI.initBatchEditorResults($('#batchProcessingAccordion'));
    })(jQuery);
</script>
