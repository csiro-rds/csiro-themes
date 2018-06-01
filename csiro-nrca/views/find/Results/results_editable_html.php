<div class="component component-results-editable">
    <div class="results-header clearfix">
        <div class="results-editor-status"></div>
        <a href="#" onclick="caResultsEditorPanel.hidePanel(); return false;" class="close">
            <span class="glyphicon glyphicon-remove"></span>
        </a>
    </div>

    <div class="results-editor-content">
        <div class="loading">
            <?php // TODO FIXME loading indicator ?>
            <?php print _t("Loading... "); ?>
            <?php print caBusyIndicatorIcon($this->request, array( 'style' => 'width: 30px; height 30px; color: #fff;' )); ?>
        </div>
    </div>

    <div id="caResultsComplexDataEditorPanel" class="results-editor-panel">
        <div id="caResultsComplexDataEditorPanelContent"></div>
    </div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            caUI.initTableView('.component-results-editable', {
                dataLoadUrl: '<?php print caNavUrl($this->request, '*', '*', 'getResultsEditorData'); ?>',
                dataSaveUrl: '<?php print caNavUrl($this->request, '*', '*', 'saveResultsEditorData'); ?>',
                dataEditUrl: '<?php print caNavUrl($this->request, '*', '*', 'resultsComplexDataEditor'); ?>',
                rowHeaders: true,
                dataEditorID: 'caResultsComplexDataEditorPanel',
                gridClassName: 'results-editor-content',
                currentRowClassName: 'current-row',
                currentColClassName: 'current-column',
                readOnlyCellClassName: 'read-only', // cannot be edited inline (ie. readonly in Handsontable)
                nonEditableCellClassName: 'non-editable', // cannot be edited inline or with an overlay
                overlayEditorIconClassName: 'overlay-icon',
                statusDisplayClassName: 'caResultsEditorStatus',
                errorCellClassName: 'error',
                loadingClassName: 'loading',
                colHeaders: <?php print json_encode($this->getVar('column_headers')); ?>,
                columns: <?php print json_encode($this->getVar('columns')); ?>,
                rowCount: <?php print (int)$this->getVar('num_rows'); ?>
            });
        });
    }(jQuery));
</script>
