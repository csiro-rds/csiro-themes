<?php
$this->setVar('advanced', true);
$this->setVar('quick_look', true);
$this->setVar('allowed_views', array( 'full', 'list', 'editable', 'no_results', 'thumbnail' ));
$this->setVar('default_view', 'thumbnail');
print $this->render($this->request->getDirectoryPathForThemeFile('views/find/Search/common/search.php'));
