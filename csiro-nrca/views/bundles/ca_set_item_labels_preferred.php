<?php
$this->setVar('bundlePreviewName', 'caption');
$this->setVar('templateValues', array('caption', 'locale_id'));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/labels_preferred.php'));
