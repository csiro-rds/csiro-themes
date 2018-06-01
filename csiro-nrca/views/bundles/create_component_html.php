<div id="caObjectComponentPanel" class="modal fade" data-toggle="modal" role="dialog">
    <div id="caObjectComponentPanelContentArea" class="modal-dialog modal-lg"></div>
</div>

<script>
	var caObjectComponentPanel;

    (function ($) {
        'use strict';

        $(function() {
            if (caUI.initPanel) {
                caObjectComponentPanel = caUI.initPanel({
                    panelID: "caObjectComponentPanel",
                    panelContentID: "caObjectComponentPanelContentArea",
                    exposeBackgroundColor: "#000000",
                    exposeBackgroundOpacity: 0.7,
                    panelTransitionSpeed: 400,
                    closeButtonSelector: ".close",
                    center: true
                });
            }

            $("#caObjectComponentPanelContentArea").data("panel", caObjectComponentPanel);
        });
    }(jQuery));
</script>
