<?php
$this->setVar('confirm_title', 'Media Import');
$this->setVar('confirm_message', 'You are about to import media from a directory.');
print $this->render($this->request->getDirectoryPathForThemeFile('views/batch/mediaimport/confirm_html.php'));
