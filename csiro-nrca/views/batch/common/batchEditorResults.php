<script>
    var caUI = caUI || {};

    (function ($) {
        'use strict';

        caUI.initBatchEditorProgressBar = function($progressBar, $batchTitle, $countLabels) {
            return function (completedRows, totalRows, errorCount, elapsedTime, memoryUsed) {
                var $errorCountLabel = $countLabels.find('.errors label');
                var percent = (completedRows / totalRows) * 100;
                $progressBar.removeClass('progress-bar-success progress-bar-danger');
                $batchTitle.removeClass('label-success label-secondary label-danger');
                $errorCountLabel.removeClass('label-success label-secondary label-danger');
                $countLabels.find('.processed label').text(completedRows + ' / ' + totalRows + ' processed');
                if (errorCount > 0) {
                    $progressBar.attr('progress-bar progress-bar-danger');
                    $batchTitle.addClass('label-danger');
                    $batchTitle.text('Error');
                    $errorCountLabel.addClass('label-danger');
                    $errorCountLabel.text(errorCount + ' errors');
                } else {
                    if (percent >= 100) {
                        $progressBar.addClass('progress-bar-success');
                        $batchTitle.addClass('label-success');
                        $batchTitle.text('Complete');
                    } else {
                        $batchTitle.addClass('label-secondary');
                    }
                    $errorCountLabel.addClass('label-success');
                }
                $errorCountLabel.text(errorCount + ' errors');
                $progressBar.attr('aria-valuenow', percent);
                $progressBar.find('span').html(percent + '%');
                $progressBar.width(percent + '%');

                $('#batchProcessingElapsedTime span').html(elapsedTime + ' seconds');
                $('#batchProcessingMemoryUsage span').html(memoryUsed + ' MiB');
            }
        };

        caUI.initBatchEditorResults = function($batchAccordionTarget) {
            var getResultAlertPanelBody = function (result) {
                var resultAlertPanel = $('<div>')
                    .addClass('panel-body');

                if (result.errors.length > 0) {
                    result.errors.forEach(function (error) {
                        resultAlertPanel.append(generateAlert(result.label, error, true));
                    });
                } else {
                    resultAlertPanel.append(generateAlert(result.label, 'processed successfully!', false));
                }
                return resultAlertPanel;
            };
            var generateAlert = function (resultLabel, alertText, isError) {
                var alertClass = isError ? 'alert-danger' : 'alert-success';
                var alertGlyphiconClass = isError ? 'glyphicon-exclamation-sign' : 'glyphicon-ok-sign';
                var alertText = isError ? ' failed: ' + alertText : ' processed successfully!';
                return $('<div>')
                    .addClass('alert ' + alertClass)
                    .attr('role', 'alert')
                    .append(
                        $('<span>')
                            .addClass('glyphicon ' + alertGlyphiconClass)
                    )
                    .append(resultLabel + ' ' + alertText);
            };

            return function (resultJsonString) {
                var results = JSON.parse(resultJsonString);
                $.each(results, function (index, result) {
                        var collapseId = 'collapse_' + result.idno;
                        var headingId = 'heading_' + result.idno;
                        var headerGlyphicon = result.errors.length > 0 ? 'glyphicon-exclamation-sign' : 'glyphicon-ok-sign';

                        $batchAccordionTarget.append(
                            $('<div>')
                                .addClass('panel panel-default')
                                .append(
                                    $('<div>')
                                        .addClass('panel-heading clickable-panel-heading')
                                        .attr('role', 'tab')
                                        .attr('id', headingId)
                                        .append(
                                            $('<h4>')
                                                .addClass('panel-title')
                                                .append(
                                                    $('<a>')
                                                        .addClass('collapsed')
                                                        .attr('role', 'button')
                                                        .attr('data-toggle', 'collapse')
                                                        .attr('data-parent', $batchAccordionTarget.selector)
                                                        .attr('href', '#' + collapseId)
                                                        .attr('aria-expanded', 'true')
                                                        .attr('aria-controls', '#' + collapseId)
                                                        .text(result.label)
                                                        .append(
                                                            $('<span>')
                                                                .addClass('pull-right glyphicon ' + headerGlyphicon)
                                                        )
                                                )
                                        )
                                )
                                .append(
                                    $('<div>')
                                        .addClass('panel-collapse collapse')
                                        .attr('id', collapseId)
                                        .attr('role', 'tabpanel')
                                        .attr('aria-labelledby', '#' + headingId)
                                        .append(getResultAlertPanelBody(result))
                                )
                        );
                    });
            }
        };

    }(jQuery));
</script>
