<?php
$this->setVar('templateValues', array('label', 'id', 'type_id', 'typename', 'idno', 'label', 'idno_sort'));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
