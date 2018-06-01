<?php
$this->setVar('labelField', 'label');
$this->setVar('templateValues', array( 'label', 'type_id', 'id' ));
$this->setVar('quickadd_enabled', true);
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
