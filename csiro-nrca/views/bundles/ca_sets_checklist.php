<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_table_num = $this->getVar('table_num');
$va_set_options = array_flip($this->getVar('set_options'));
natcasesort($va_set_options);
$va_settings = $this->getVar('settings');
$va_initial_values = $this->getVar('initial_values');
$vb_read_only = (isset($va_settings['readonly']) && $va_settings['readonly']);
$vb_batch = $this->getVar('batch');
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-sets-checklist">
    <textarea class="sets-checklist-new-item-template hidden">
        <div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
            <span class="formLabelError">{error}</span>
            <?php if (sizeof($va_set_options)): ?>
                <div class="row">
                    <?php foreach($va_set_options as $vn_set_id => $vs_set_name): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input id="{fieldNamePrefix}<?php print $vn_set_id ?>" class="form-check-input" type="checkbox" value="<?php print $vn_set_id ?>">
                                    <?php print $vs_set_name; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    <?php print _t('No sets are available'); ?>
                </div>
            <?php endif; ?>
        </div>
    </textarea>

    <?php if ($vb_batch): ?>
        <?php print caBatchEditorSetsModeControl($vn_table_num, $vs_id_prefix); ?>
    <?php endif; ?>
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix.$vn_table_num.'_sets', $va_settings); ?>
    <div class="bundleContainer">
        <div class="item-list"></div>
    </div>
</div>

<script>
    caUI.initChecklistBundle('#<?php print $vs_id_prefix; ?>', {
        fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
        templateValues: [ 'set_id' ],
        initialValues: <?php print json_encode($va_initial_values); ?>,
        initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
        errors: [],
        itemID: '<?php print $vs_id_prefix; ?>Item_',
        templateClassName: 'sets-checklist-new-item-template',
        itemListClassName: 'item-list',
        minRepeats: 0,
        maxRepeats: <?php print sizeof($this->getVar('sets')); ?>,
        readonly: <?php print json_encode($vb_read_only); ?>
    });
</script>
