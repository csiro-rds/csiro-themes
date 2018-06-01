<?php
$this->setVar('moduleDirectory', 'manage');
print $this->render($this->request->getDirectoryPathForThemeFile('views/common/quickadd_html.php'));
