<?php
$t_instance = $this->getVar('t_instance');
$pa_parameters = $this->getVar('parameters');
$vs_warning = isset($pa_parameters['warning']) ? $pa_parameters['warning'] : null;
?>
<?php print caFormTag($this->request, 'Delete', 'caDeleteForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
    <h1 class="text-danger"><?php print _t('Really delete "%1"?', $this->getVar('item_name')); ?></h1>
    <p>Are you sure you want to delete this record?</p>
    <div>
        <div>
            <?php print caDeleteRemapper($this->request, $t_instance); ?>
        </div>
        <?php if ($vs_warning): ?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign"></span>
                <?php print $vs_warning; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="well">
        <div class="btn-group">
            <button id="caDeleteForm" class="btn btn-danger">
                <span class="glyphicon glyphicon-trash"></span>
                <?php print _t('Delete'); ?>
            </button>
            <a href="<?php print caNavUrl($this->request, $this->getVar('module_path'), $this->getVar('controller'), $this->getVar('cancel_action'), $pa_parameters); ?>" class="btn btn-default">
                <span class="glyphicon glyphicon-remove"></span>
                <?php print _t('Cancel'); ?>
            </a>
        </div>
    </div>
    <?php foreach (array_merge($pa_parameters, array('confirm' => 1)) as $vs_f => $vs_v): ?>
        <?php print caHTMLHiddenInput($vs_f, array('value' => $vs_v)); ?>
    <?php endforeach; ?>
    <?php print caHTMLHiddenInput($t_instance->primaryKey(), array('value' => $t_instance->getPrimaryKey())); ?>
</form>
