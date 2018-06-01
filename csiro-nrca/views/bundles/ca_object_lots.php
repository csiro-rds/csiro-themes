<?php
$this->setVar('templateValues', array('label', 'idno_stub', 'id', 'type_id'));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
