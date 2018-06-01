<!-- Overlay for media display triggered from left sidenav widget or quicklook -->
<div id="caMediaPanel" class="caMediaPanel">
    <div id="caMediaPanelContentArea"></div>
</div>

<!-- Overlay for search/browse results-based editing -->
<div id="caResultsEditorPanel" class="caResultsEditorPanel">
    <div id="caResultsEditorPanelContentArea"></div>
</div>

<div id="editorFieldList">
    <div id="editorFieldListHeader"><?php print _t('Form table of contents'); ?></div>
    <div id="editorFieldListContentArea"></div>
</div>

<div id="caHierarchyOverviewPanel">
    <div id="caHierarchyOverviewClose" class="close"> </div>
    <div id="caHierarchyOverviewHeader"><?php print _t('Browse hierarchy'); ?></div>
    <div id="caHierarchyOverviewContentArea"></div>
</div>
<div id="caTempExportForm" class="hidden"></div>

<script>
    var caMediaPanel, caResultsEditorPanel, caEditorFieldList, caHierarchyOverviewPanel;

    (function ($) {
        'use strict';

        $(function() {
            if (caUI.initPanel) {
                caMediaPanel = caUI.initPanel({
                    panelID: 'caMediaPanel',
                    panelContentID: 'caMediaPanelContentArea',
                    exposeBackgroundColor: '#000000',
                    exposeBackgroundOpacity: 0.7,
                    panelTransitionSpeed: 400,
                    closeButtonSelector: '#caMediaPanelContentArea .close'
                });

                caResultsEditorPanel = caUI.initPanel({
                    panelID: 'caResultsEditorPanel',
                    panelContentID: 'caResultsEditorPanelContentArea',
                    exposeBackgroundColor: '#000000',
                    exposeBackgroundOpacity: 0.7,
                    panelTransitionSpeed: 100,
                    closeButtonSelector: '#caResultsEditorPanelContentArea .close',
                    closeOnEsc: false
                });

                caEditorFieldList = caUI.initPanel({
                    panelID: 'editorFieldList',
                    panelContentID: 'editorFieldListContentArea',
                    exposeBackgroundColor: '#000000',
                    exposeBackgroundOpacity: 0.7,
                    panelTransitionSpeed: 200,
                    closeButtonSelector: '#editorFieldListContentArea .close',
                    center: true
                });

                caHierarchyOverviewPanel = caUI.initPanel({
                    panelID: 'caHierarchyOverviewPanel',
                    panelContentID: 'caHierarchyOverviewContentArea',
                    exposeBackgroundColor: '#000000',
                    exposeBackgroundOpacity: 0.7,
                    panelTransitionSpeed: 200,
                    closeButtonSelector: '#caHierarchyOverviewContentArea .close',
                    center: true
                });
            }
        });
    }(jQuery));
</script>
