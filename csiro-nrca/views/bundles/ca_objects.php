<?php
$this->setVar('labelField', 'surname');
$this->setVar('templateValues', $this->getVar('t_subject')->tableName() === 'ca_object_representations' ? array( 'label', 'id', 'type_id' ) : array( 'label', 'id' ));
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
