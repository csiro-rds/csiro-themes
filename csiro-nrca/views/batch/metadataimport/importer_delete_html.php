<?php
	$t_importer = $this->getVar('t_importer');
	$vn_importer_id = $this->getVar('importer_id');
?>
<div class="sectionBox">
<?php print caDeleteWarningBox($this->request, $t_importer, $t_importer->getLabelForDisplay(false), 'batch', 'MetadataImport', 'Index', array('importer_id' => $vn_importer_id)); ?>
</div>
