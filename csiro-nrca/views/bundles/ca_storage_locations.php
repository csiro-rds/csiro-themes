<?php
$this->setVar('templateValues', array('label', 'set_code', 'id'));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
