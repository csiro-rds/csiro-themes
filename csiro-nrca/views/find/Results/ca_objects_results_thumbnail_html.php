<?php
$vs_caption_template = $this->request->config->get('ca_objects_results_thumbnail_caption_template') ?: "^ca_objects.preferred_labels.name%truncate=27&ellipsis=1<br/>^ca_objects.idno";

$vo_result = $this->getVar('result');
$va_result_items = array_chunk(array_filter(array_map(
    function () use ($vo_result, $vs_caption_template) {
        if (!$vo_result->nextHit()) {
            return null;
        }
        $va_media_tags = $vo_result->getMediaTags('ca_object_representations.media', 'preview170');
        return array(
            'object_id' => (int)$vo_result->get('object_id'),
            'caption' => $vo_result->getWithTemplate($vs_caption_template),
            'media_tags' => $va_media_tags,
            'padding_top_bottom' => sizeof($va_media_tags) > 0 ? (170 - $vo_result->getMediaInfo('ca_object_representations.media', 'preview170')['HEIGHT']) / 2 : 0
        );
    },
    range(0, $this->getVar('current_items_per_page'))
)), 4);
?>

<div class="component component-results component-results-thumbnail">
    <form id="caFindResultsForm">
        <?php foreach ($va_result_items as $va_row): ?>
            <div class="row">
                <?php foreach ($va_row as $va_item): ?>
                    <div class="col-md-3 item">
                        <div class="thumbnail">
                            <div class="image-container" style="padding: <?php print $va_item['padding_top_bottom']; ?>px 0;">
                                <a href="<?php print caEditorUrl($this->request, 'ca_objects', $va_item['object_id'], array(), array('data-id' => $va_item['object_id'])); ?>" class="editor-link">
                                    <?php if (sizeof($va_item['media_tags']) > 0): ?>
                                        <?php print $va_item['media_tags'][0]; ?>
                                    <?php else: ?>
                                        <span class="glyphicon glyphicon-picture placeholder text-muted"></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="caption well clearfix">
                                <div class="pull-right text-right small">
                                    <div class="add-to-set">
                                        <?php print _t('Add to set'); ?>
                                        <input type="checkbox" name="add_to_set_ids" value="<?php print $va_item['object_id']; ?>" class="add-to-set" />
                                    </div>
                                    <?php if (sizeof($va_item['media_tags']) > 0): ?>
                                        <div>
                                            <a href="#" class="quick-look small" data-id="<?php print $va_item['object_id']; ?>">
                                                <span class="glyphicon glyphicon-search"></span>
                                                <?php print _t("Quick Look"); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php print $va_item['caption']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </form>
</div>

<script>
    (function ($) {
        'use strict';

        $(function() {
            $('a.quick-look').on('click', function () {
                caMediaPanel.showPanel('<?php print caNavUrl($this->request, 'find', 'SearchObjects', 'QuickLook'); ?>/object_id/' + $(this).data('id'));
                return false;
            });
        });
    }(jQuery));
</script>
