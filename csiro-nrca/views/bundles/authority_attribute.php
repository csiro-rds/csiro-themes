<?php
$vs_field_name_prefix = $this->getVar('field_name_prefix');
$pa_options = $this->getVar('options');
$pa_element_info = $this->getVar('element_info');
$vn_width = (isset($pa_options['width']) && $pa_options['width'] > 0) ? $pa_options['width'] : $this->getVar('settings')['fieldWidth'];
$vn_height = (isset($pa_options['height']) && $pa_options['height'] > 0) ? $pa_options['height'] : 1;
?>
<div id="<?php print $vs_field_name_prefix; ?>_display{n}" class="pull-right"></div>
<?php print caHTMLTextInput("{$vs_field_name_prefix}_autocomplete{n}", array( 'size' => $vn_width, 'height' => $vn_height, 'value' => '{{'.$pa_element_info['element_id'].'}}', 'maxlength' => 512, 'id' => "{$vs_field_name_prefix}_autocomplete{n}", 'class' => $this->getVar('class') )); ?>
<?php print caHTMLHiddenInput("{$vs_field_name_prefix}_{n}", array( 'value' => '{{'.$pa_element_info['element_id'].'}}', 'id' => "{$vs_field_name_prefix}_{n}" )); ?>

<div id="caRelationQuickAddPanel<?php print $vs_field_name_prefix; ?>_{n}" class="modal fade" data-toggle="modal">
    <div id="caRelationQuickAddPanel<?php print $vs_field_name_prefix; ?>ContentArea_{n}" class="modal-dialog modal-lg"></div>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            var quickAddPanelId, quickAddPanelContentId;
            if (caUI.initPanel) {
                quickAddPanelId = "caRelationQuickAddPanel<?php print $vs_field_name_prefix; ?>_{n}";
                quickAddPanelContentId = quickAddPanelId + 'ContentArea';
                caRelationQuickAddPanel<?php print $vs_field_name_prefix; ?>_{n} = caUI.initPanel({
                    panelID: quickAddPanelId, /* DOM ID of the <div> enclosing the panel */
                    panelContentID: quickAddPanelContentId, /* DOM ID of the content area <div> in the panel */
                    initialFadeIn: false,
                    useExpose: false,
                    onOpenCallback: function (url, postData) {
                        $.get(url, postData, function () {
                            $('#' + quickAddPanelId).modal('show');
                        });
                    },
                    onCloseCallback: function () {
                        $(quickAddPanelId).modal('hide');
                    }
                });
            }

            var $display = $('#<?php print $vs_field_name_prefix; ?>_display{n}');
            var $item = $('#<?php print $vs_field_name_prefix; ?>_{n}');
            var $autocomplete = $('#<?php print $vs_field_name_prefix; ?>_autocomplete{n}');

            $autocomplete
                .val(
                    $autocomplete.val()
                        .replace(/(<\/?[^>]+>)/gi, function(m, p1) {
                            $display.html(p1);
                            return '';
                        })
                        .replace(/\[([\d]+)]$/gi, function(m, p1) {
                            $item.val(parseInt(p1));
                            return '';
                        })
                        .trim()
                )
                .autocomplete({
                    minLength: 3,
                    delay: 800,
                    html: true,
                    source: function (request, response) {
                        $.ajax({
                            url: '<?php print $this->getVar('lookup_url'); ?>',
                            dataType: 'json',
                            data: { term: request.term, quickadd: <?php print $this->getVar('allowQuickadd') ? 1 : 0; ?>,
                            noInline: <?php print $this->getVar('allowQuickadd') ? 0 : 1; ?> },
                            success: function( data ) {
                                response(data);
                            }
                        });
                    },
                    select: function (event, ui) {
                        var quickaddPanel = caRelationQuickAddPanel<?php print $vs_field_name_prefix; ?>_{n};
                        var quickaddUrl = '<?php print $this->getVar('quickadd_url'); ?>';

                        if (!parseInt(ui.item.id) || (ui.item.id == 0)) {
                            var panelUrl = quickaddUrl;

                            quickaddPanel.showPanel(panelUrl, null, null, { q: ui.item._query, field_name_prefix: '<?php print $vs_field_name_prefix; ?>' });
                            var quickAddPanelContent = $('#' + quickAddPanelContentId);
                            quickAddPanelContent.data('panel', quickaddPanel);
                            quickAddPanelContent.data('autocompleteInput', $autocomplete.val());
                            quickAddPanelContent.data('autocompleteInputID', $autocomplete.attr('id'));
                            quickAddPanelContent.data('autocompleteItemIDID', $item.attr('id'));
                            event.preventDefault();
                            return;
                        } else if (ui.item.id == -1) {
                            event.preventDefault();
                            return;
                        }

                        $item.val(ui.item.id);
                        $autocomplete.val($.trim(ui.item.label.replace(/<\/?[^>]+>/gi, '')));
                        event.preventDefault();
                    },
                    change: function () {
                        // If nothing has been selected remove all content from text input
                        if (!$item.val()) {
                            $autocomplete.val('');
                        }
                    }
                })
                .click(function() {
                    this.select();
                });
        });
    }(jQuery));
</script>
