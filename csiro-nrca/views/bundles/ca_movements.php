<?php
$this->setVar('labelField', 'label');
$this->setVar('templateValues', array( 'label', 'id', 'type_id', 'typename', 'idno_sort' ));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
