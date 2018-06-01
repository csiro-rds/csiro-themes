<?php
$vo_result = $this->getVar('result');
$t_display = $this->getVar('t_display');

$va_display_list = array_filter($this->getVar('display_list'), function ($va_display_info) {
    return !in_array($va_display_info['bundle_name'], array('ca_objects.preferred_labels', 'ca_object_labels.name'));
});

$va_result_items = array_filter(array_map(
    function () use ($vo_result, $va_display_list, $t_display) {
        if (!$vo_result->nextHit()) {
            return null;
        }
        return array(
            'object_id' => (int)$vo_result->get('object_id'),
            'media_tags' => $vo_result->getMediaTags('ca_object_representations.media', 'small'),
            'display_labels' => $vo_result->getDisplayLabels($this->request),
            'display_list' => array_filter(
                array_map(
                    function ($va_display_info, $vn_placement_id) use ($vo_result, $t_display) {
                        return array(
                            'label' => $va_display_info['display'],
                            'value' => $t_display->getDisplayValue($vo_result, $vn_placement_id, array_merge(array('request' => $this->request), is_array($va_display_info['settings']) ? $va_display_info['settings'] : array()))
                        );
                    },
                    $va_display_list,
                    array_keys($va_display_list)
                ),
                function ($va_display_info) {
                    return $va_display_info['value'];
                }
            )
        );
    },
    range(0, $this->getVar('current_items_per_page'))
));
?>

<div class="component component-results-full">
    <form id="caFindResultsForm">
        <?php foreach ($va_result_items as $va_item): ?>
            <div class="row item">
                <div class="col-md-4 clearfix">
                    <input type="checkbox" name="add_to_set_ids" value="<?php print $va_item['object_id']; ?>" class="add-to-set pull-left" />
                    <div class="text-center">
                        <a href="<?php print caEditorUrl($this->request, 'ca_objects', $va_item['object_id']); ?>">
                            <?php if (sizeof($va_item['media_tags']) > 0): ?>
                                <?php print $va_item['media_tags'][0]; ?>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-picture placeholder text-muted"></span>
                            <?php endif; ?>
                        </a>
                        <?php if (sizeof($va_item['media_tags']) > 0): ?>
                            <div>
                                <a href="#" onclick="caMediaPanel.showPanel('<?php print caNavUrl($this->request, 'find', 'SearchObjects', 'QuickLook', array('object_id' => $va_item['object_id'])); ?>');" class="quick-look small">
                                    <span class="glyphicon glyphicon-search"></span>
                                    <?php print _t("Quick Look"); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <?php foreach ($va_item['display_labels'] as $vs_display_label): ?>
                            <div>
                                <a href="<?php print caEditorUrl($this->request, 'ca_objects', $va_item['object_id']); ?>">
                                    <?php print $vs_display_label; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($va_item['display_list'] as $va_display_info): ?>
                        <div class="form-group">
                            <label><?php print $va_display_info['label']; ?></label>
                            <?php print $va_display_info['value']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</div>
