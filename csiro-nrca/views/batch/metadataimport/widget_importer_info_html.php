<?php
$t_item = $this->getVar('t_item');
$vn_importer_count = ca_data_importers::getImporterCount();
?>
<?php if (!$t_item->getPrimaryKey()): ?>
    <h3 class="importers">
        <?php print _t('Importers'); ?>:
    </h3>
    <label>
        <?php print $vn_importer_count === 1 ? _t('1 importer is defined') : _t('%1 importers are defined', $vn_importer_count); ?>
    </label>
<?php else: ?>
    <?php print caEditorInspector($this, array('backText' => _t('Back to list'))); ?>
<?php endif; ?>
