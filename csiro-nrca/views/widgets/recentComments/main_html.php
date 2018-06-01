<?php
$po_request = $this->getVar('request');
$va_comments = $this->getVar('comment_list');
$vb_any_unmoderated = array_filter($va_comments, function ($va_comment_info) {
    return !$va_comment_info['moderated_on'];
});
?>
<div class="widget widget-recent-comments">
    <?php if (sizeof($va_comments) > 0): ?>
        <h3><?php print _t("%1 recent %2 comments", $this->getVar("limit"), $this->getVar("comment_type")); ?></h3>
        <form id="commentListForm">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?php print _t('Select'); ?></th>
                    <th><?php print _t('User'); ?></th>
                    <th><?php print _t('Commented'); ?></th>
                    <th><?php print _t('Created'); ?></th>
                    <th><?php print _t('Approved'); ?></th>
                    <th><?php print _t('Rating'); ?></th>
                    <th><?php print _t('Details'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($va_comments as $va_comment_info): ?>
                    <tr>
                        <td>
                            <?php if (!$va_comment_info["moderated_on"]): ?>
                                <input type="checkbox" name="comment_id[]" value="<?php print $va_comment_info['comment_id']; ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print $va_comment_info["fname"]; ?>
                            <?php print $va_comment_info["lname"]; ?>
                        </td>
                        <td>
                            <a href="<?php print caEditorUrl($po_request, $va_comment_info['table_num'], $va_comment_info['row_id']); ?>">
                                <?php print $va_comment_info["commented_on"]; ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($va_comment_info["created_on"]): ?>
                                <?php print $va_comment_info["created_on"]; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($va_comment_info["moderated_on"]): ?>
                                <?php print $va_comment_info["moderated_on"]; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print $va_comment_info["rating"]; ?>
                        </td>
                        <td>
                            <a href="#" id="more<?php print $va_comment_info["comment_id"]; ?>" class="btn btn-default btn-sm show-details" onclick="jQuery('#more<?php print $va_comment_info["comment_id"]; ?>').hide(); jQuery('#hide<?php print $va_comment_info["comment_id"]; ?>').show(); jQuery('#comment<?php print $va_comment_info["comment_id"]; ?>').slideDown(250); return false;">
                                <span class="glyphicon glyphicon-eye-open"></span>
                                <?php print _t("Show"); ?>
                            </a>
                            <a href="#" id="hide<?php print $va_comment_info["comment_id"]; ?>" class="btn btn-default btn-sm hide-details" onclick="jQuery('#more<?php print $va_comment_info["comment_id"]; ?>').show(); jQuery('#hide<?php print $va_comment_info["comment_id"]; ?>').hide(); jQuery('#comment<?php print $va_comment_info["comment_id"]; ?>').slideUp(250); return false;">
                                <span class="glyphicon glyphicon-eye-closed"></span>
                                <?php print _t("Hide"); ?>
                            </a>
                        </td>
                    </tr>
                    <tr><!-- Dummy to force the stripes to work correctly --></tr>
                    <tr>
                        <td colspan="6" id="comment<?php print $va_comment_info["comment_id"]; ?>" class="comment-details-container">
                            <?php print $va_comment_info["comment"]; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($vb_any_unmoderated): ?>
                <div>
                    <a href="#" onclick="jQuery('#commentListForm').attr('action', '<?php print caNavUrl($po_request, 'manage', 'Comments', 'Approve'); ?>').submit(); return false;" class="btn btn-success">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?php print _t('Approve'); ?>
                    </a>
                    <a href="#" onclick="jQuery('#commentListForm').attr('action', '<?php print caNavUrl($po_request, 'manage', 'Comments', 'Delete'); ?>').submit();" class="btn btn-warning">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Delete'); ?>
                    </a>
                </div>
                <input type="hidden" name="mode" value="dashboard" />
            <?php endif; ?>
        </form>
    <?php else: ?>
        <?php print _t("There are no %1 comments", $this->getVar("comment_type")); ?>
    <?php endif; ?>
</div>
