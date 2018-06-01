<?php
$po_request = $this->getVar('request');
$va_tags = $this->getVar('tags_list');
$vb_any_unmoderated = array_filter($va_tags, function ($va_tag_info) {
    return !$va_tag_info['moderated_on'];
});
?>
<div class="widget widget-recent-tags">
    <?php if (sizeof($va_tags) > 0): ?>
        <h3><?php print _t("%1 recent %2 tags", $this->getVar("limit"), $this->getVar("tag_type")); ?></h3>
        <form id="tagListForm">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?php print _t('Select'); ?></th>
                    <th><?php print _t('User'); ?></th>
                    <th><?php print _t('Tagged'); ?></th>
                    <th><?php print _t('Tag'); ?></th>
                    <th><?php print _t('Created'); ?></th>
                    <th><?php print _t('Approved'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($va_tags as $va_tag_info): ?>
                    <tr>
                        <td>
                            <?php if (!$va_tag_info["moderated_on"]): ?>
                                <input type="checkbox" name="tag_relation_id[]" value="<?php print $va_tag_info['relation_id']; ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print $va_tag_info["fname"]; ?>
                            <?php print $va_tag_info["lname"]; ?>
                        </td>
                        <td>
                            <a href="<?php print caEditorUrl($po_request, $va_tag_info['table_num'], $va_tag_info['row_id']); ?>">
                                <?php print $va_tag_info["item_tagged"]; ?>
                            </a>
                        </td>
                        <td>
                            <?php print $va_tag_info["tag"]; ?>
                        </td>
                        <td>
                            <?php if ($va_tag_info["created_on"]): ?>
                                <?php print $va_tag_info["created_on"]; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($va_tag_info["moderated_on"]): ?>
                                <?php print $va_tag_info["moderated_on"]; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($vb_any_unmoderated): ?>
                <div>
                    <a href="#" onclick="jQuery('#tagListForm').attr('action', '<?php print caNavUrl($po_request, 'manage', 'Tags', 'Approve'); ?>').submit(); return false;" class="btn btn-success">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?php print _t('Approve'); ?>
                    </a>
                    <a href="#" onclick="jQuery('#tagListForm').attr('action', '<?php print caNavUrl($po_request, 'manage', 'Tags', 'Delete'); ?>').submit();" class="btn btn-warning">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Delete'); ?>
                    </a>
                </div>
                <input type="hidden" name="mode" value="dashboard" />
            <?php endif; ?>
        </form>
    <?php else: ?>
        <?php print _t("There are no %1 tags", $this->getVar("tag_type")); ?>
    <?php endif; ?>
</div>
