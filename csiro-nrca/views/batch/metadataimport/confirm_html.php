<?php
$this->setVar('confirm_title', 'Data import');
$this->setVar('confirm_message', 'You are about to import data.');
print $this->render($this->request->getDirectoryPathForThemeFile('views/batch/metadataimport/confirm_html.php'));
