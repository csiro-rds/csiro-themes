<?php
$po_request = $this->getVar('request');
$va_watched_items = $this->getVar('watched_items');
?>
<div class="widget widget-watched-items">
    <?php if (sizeof($va_watched_items) > 0): ?>
        <form id="watchedItemsForm">
            <input type="hidden" name="mode" value="list" />
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?php print _t('Select'); ?></th>
                    <th><?php print _t('Item'); ?></th>
                    <th><?php print _t('Type'); ?></th>
                    <th><?php print _t('Change Log'); ?></th>
                </tr>
                </thead>
                <?php foreach ($va_watched_items as $va_item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="watch_id[]" value="<?php print $va_item["watch_id"]; ?>" />
                        </td>
                        <td>
                            <?php if ($va_item["primary_key"]): ?>
                                <a href="<?php print caEditorUrl($po_request, $va_item["table_name"], $va_item["row_id"]); ?>">
                                    <?php print ($va_item['idno'] ? '['.$va_item['idno'].'] ' : '') . $va_item["displayName"]; ?>
                                </a>
                            <?php else: ?>
                                <?php print _t('[DELETED] Row %1', $va_item["row_id"]); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php print $va_item["table_name"] ?: $va_item["table_num"]; ?>
                        </td>
                        <td>
                            <a href="#" id="more<?php print $va_item["watch_id"]; ?>" class="btn btn-default btn-sm show-details" onclick="jQuery('#more<?php print $va_item["watch_id"]; ?>').hide(); jQuery('#hide<?php print $va_item["watch_id"]; ?>').show(); jQuery('#details<?php print $va_item["watch_id"]; ?>').slideDown(250); return false;">
                                <span class="glyphicon glyphicon-eye-open"></span>
                                <?php print _t("Show"); ?>
                            </a>
                            <a href="#" id="hide<?php print $va_item["watch_id"]; ?>" class="btn btn-default btn-sm hide-details" onclick="jQuery('#more<?php print $va_item["watch_id"]; ?>').show(); jQuery('#hide<?php print $va_item["watch_id"]; ?>').hide(); jQuery('#details<?php print $va_item["watch_id"]; ?>').slideUp(250); return false;">
                                <span class="glyphicon glyphicon-eye-close"></span>
                                <?php print _t("Hide"); ?>
                            </a>
                        </td>
                    </tr>
                    <tr><!-- Dummy to force the stripes to work correctly --></tr>
                    <tr>
                        <td colspan="4" class="details-container" id="details<?php print $va_item['watch_id']; ?>">
                            <?php print $va_item["change_log"]; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div>
                <a href="#" onclick="jQuery('#watchedItemsForm').attr('action', '<?php print caNavUrl($po_request, 'manage', 'WatchedItems', 'Delete'); ?>').submit(); return false;" class="btn btn-default">
                    <span class="glyphicon glyphicon-eye-close"></span>
                    <?php print _t('Unwatch'); ?>
                </a>
            </div>
            <input type="hidden" name="mode" value="dashboard">
        </form>
    <?php else: ?>
        <?php print _t("You have no items on your watch list."); ?>
    <?php endif; ?>
</div>
