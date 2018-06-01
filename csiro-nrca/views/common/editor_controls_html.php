<?php
$t_subject = $this->getVar('t_subject');

?>
<div class="btn-group top-right-fixed">
    <a href="<?php print $this->getVar('cancel_url'); ?>" class="btn btn-default">
        <span class="glyphicon glyphicon-remove-circle"></span>
        <?php print _t("Discard Changes"); ?>
    </a>
    <?php if ($t_subject->isSaveable($this->request)): ?>
        <?php if ($this->getVar('show_save_and_return')): ?>
            <button class="btn btn-success" onclick="jQuery('#isSaveAndReturn').val(1);">
                <span class="glyphicon glyphicon-save"></span>
                <?php print _t("Save and return"); ?>
            </button>
        <?php else: ?>
            <button class="btn btn-success">
                <span class="glyphicon glyphicon-save"></span>
                <?php print _t("Save"); ?>
            </button>
        <?php endif; ?>
    <?php endif; ?>
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="#" id="collapseAll" onclick="caBundleVisibilityManager.close(); return false;">
                <span class="glyphicon glyphicon-collapse-up"></span>
                <?php print _t("Collapse all"); ?>
            </a>
        </li>
        <li>
            <a href="#" id="expandAll" onclick="caBundleVisibilityManager.open(); return false;">
                <span class="glyphicon glyphicon-collapse-down"></span>
                <?php print _t("Expand all"); ?>
            </a>
        </li>
        <?php if ($t_subject->isDeletable($this->request) || $this->getVar('show_save_and_return')): ?>
            <!-- 1px padding to fix bootstrap weirdness -->
            <li class="divider" role="separator" style="padding-top: 1px"></li>
        <?php endif; ?>
        <!-- If the `save and return` button is visible, move the regular `save` button to the dropdown menu -->
        <?php if ($this->getVar('show_save_and_return')): ?>
            <li>
                <a href="#">
                    <span class="glyphicon glyphicon-save"></span>
                    <?php print _t("Save"); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($t_subject->isDeletable($this->request)): ?>
            <li>
                <a href="<?php print $this->getVar('delete_url'); ?>" class="text-danger">
                    <span class="glyphicon glyphicon-trash"></span>
                    <?php print _t("Delete"); ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
