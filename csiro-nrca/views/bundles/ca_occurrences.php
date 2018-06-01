<?php
$this->setVar('labelField', 'label');
$this->setVar('templateValues', array( 'label', 'type_id', 'id' ));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
