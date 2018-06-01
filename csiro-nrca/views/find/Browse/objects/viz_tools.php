<?php
$vo_result_context = $this->getVar('result_context');
$vo_result = $this->getVar('result');
$vs_viz_list = Visualizer::getAvailableVisualizationsAsHTMLFormElement(
    $vo_result->tableName(),
    'viz',
    array('id' => 'caSearchVizOpts'),
    array('resultContext' => $vo_result_context, 'data' => $vo_result, 'restrictToTypes' => array($vo_result_context->getTypeRestriction($vb_type_restriction_has_changed)))
);
$vs_viz_url = caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Viz', array());
?>
<?php if ($vs_viz_list): ?>
    <div id="vizToolsContainer">
        <button type="button" class="btn btn-default show-viz-tools">
            <span class="glyphicon glyphicon-stats"></span>
            <?php print _t('Show visualisation tools'); ?>
        </button>
        <div class="viz-tools hidden">
            <div>
                <?php print ($vs_viz_list ?: 'This is where the list goes'); ?>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-default hide-viz-tools">
                    <?php print _t('Hide'); ?>
                    <span class="glyphicon glyphicon-collapse-up"></span>
                </button>
                <button type="button" class="btn btn-success show-panel">
                    <?php print _t('Show visualisation'); ?>
                    <span class="glyphicon glyphicon-forward"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        (function ($) {
            'use strict';

            $(function () {
                var $container = $('#vizToolsContainer');

                $('.show-viz-tools', $container).click(function () {
                    $('.show-viz-tools', $container).hide();
                    $('.viz-tools', $container).slideDown(250);
                    return false;
                });

                $('.hide-viz-tools', $container).click(function () {
                    $('.viz-tools', $container).slideUp(250, function () {
                        $('.show-viz-tools', $container).show();
                    });
                    return false;
                });

                $('.show-panel', $container).click(function () {
                    caMediaPanel.showPanel('<?php print $vs_viz_url; ?>/viz/' + jQuery('#caSearchVizOpts').val());
                    return false;
                });

                $('.viz-tools', $container).hide().removeClass('hidden');
            });
        }(jQuery));
    </script>
<?php endif; ?>
