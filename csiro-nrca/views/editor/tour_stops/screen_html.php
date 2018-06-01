<?php
require_once($this->request->getDirectoryPathForThemeFile('helpers/themeHelpers.php'));

$t_stop = $this->getVar('t_subject');
$vn_stop_id = $this->getVar('subject_id');

$va_bundle_list = array();
$va_form_elements = $t_stop->getBundleFormHTMLForScreen(
    $this->request->getActionExtra(),
    array(
        'request' => $this->request,
        'formName' => 'TourStopEditorForm',
        'context_id' => $this->getVar('_context_id')
    ),
    $va_bundle_list
);
$this->setVar('delete_url', caNavUrl($this->request, 'editor/tour_stops', 'TourStopEditor', 'Delete/'.$this->request->getActionExtra(), array('stop_id' => $vn_stop_id)));
$this->setVar('cancel_url', caNavUrl($this->request, 'editor/tour_stops', 'TourStopEditor', 'Edit/'.$this->request->getActionExtra(), ($vn_stop_id ? array('stop_id' => $vn_stop_id) : array('type_id' => $t_stop->getTypeID()))));
?>
<div class="component component-screen">
    <?php print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/stop_id/'.$vn_stop_id, 'TourStopEditorForm', null, 'POST', 'multipart/form-data'); ?>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/common/editor_controls_html.php')); ?>
        <div class="bundles-container">
            <?php foreach (groupFormElementsByBundle($va_bundle_list, $va_form_elements) as $va_group): ?>
                <div class="row">
                    <?php print join("\n", $va_group); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <input id="isSaveAndReturn" type="hidden" name="is_save_and_return" value="0" />
        <input type="hidden" name="stop_id" value="<?php print $vn_stop_id; ?>"/>
        <input type="hidden" name="above_id" value="<?php print $this->getVar('above_id'); ?>" />
        <input type="hidden" name="after_id" value="<?php print $this->getVar('after_id'); ?>"/>
        <input type="hidden" name="_context_id" value="<?php print $this->getVar('_context_id'); ?>"/>
        <input type="hidden" name="rel_table" value="<?php print $this->getVar('rel_table'); ?>" />
        <input type="hidden" name="rel_type_id" value="<?php print $this->getVar('rel_type_id'); ?>" />
        <input type="hidden" name="rel_id" value="<?php print $this->getVar('rel_id'); ?>" />
        <?php if ($this->request->getParameter('rel', pInteger)): ?>
            <input type="hidden" name="rel" value="1" />
        <?php endif; ?>
    </form>
    <?php print caSetupEditorScreenOverlays($this->request, $t_stop, $va_bundle_list); ?>
</div>
