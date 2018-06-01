<?php
$this->setVar('confirm_title', 'Batch edit');
$this->setVar('confirm_message', 'You are apply changes to data. This operation cannot be undone.');
print $this->render($this->request->getDirectoryPathForThemeFile('views/batch/common/confirm_html.php'));
