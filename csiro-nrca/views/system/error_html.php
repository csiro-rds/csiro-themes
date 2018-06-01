<div class="component component-error alert alert-danger">
    <span class="glyphicon glyphicon-exclamation-sign"></span>
    <?php print _t("Errors occurred when trying to access"); ?>
    <code><?php print $this->getVar('referrer'); ?></code>:<br/>
    <ul>
        <?php foreach($this->getVar("error_messages") as $vs_message): ?>
            <li>
                <?php print $vs_message; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
