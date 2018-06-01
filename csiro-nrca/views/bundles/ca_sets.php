<?php
$this->setVar('templateValues', array('label', 'set_code', 'id'));
$this->setVar('additionalQuickAddParameters', array('table_num' => $this->getVar('t_subject')->tableNum()));
$this->setVar('moduleDirectory', 'manage');
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
