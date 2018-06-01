<?php
$t_subject = $this->getVar('t_subject');
$t_set = $this->getVar('t_set');
$vn_set_id = $this->getVar('set_id');
$va_bundle_list = array();
$va_form_elements = $t_subject->getBundleFormHTMLForScreen($this->request->getActionExtra(), array(
    'request' => $this->request,
    'formName' => 'caBatchEditorForm',
    'batch' => true,
    'restrictToTypes' => array_keys($t_set->getTypesForItems(array('includeParents' => true))),
    'ui_instance' => $this->getVar('t_ui'),
    'set_id' => $vn_set_id
), $va_bundle_list);
?>

<div class="sectionBox">
    <?php print caFormTag($this->request, 'Save/'.$this->request->getActionExtra(), 'caBatchEditorForm', null, 'POST', 'multipart/form-data', '_top', array('noTimestamp' => true)); ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="panel-title"><?php print _t('Save batch'); ?></h4>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger">
                                <span class="glyphicon glyphicon-remove"></span>
                                <?php print _t('Cancel'); ?>
                            </button>
                            <button type="button" class="btn btn-primary" onclick="caConfirmBatchExecutionPanel.showPanel();">
                                <span class="glyphicon glyphicon-ok"></span>
                                <?php print _t('Execute batch edit'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?php foreach($va_form_elements as $va_form_element): ?>
                    <?php print $va_form_element ?>
                <?php endforeach ?>
            </div>
        </div>
        <input type="hidden" name="confirm" value="1"/>
        <input type="hidden" name="set_id" value="<?php print $vn_set_id; ?>"/>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/batch/editor/confirm_html.php')); ?>
    <?php print '</form>'; ?>
</div>
