<?php
$this->setVar('compact', true);
$this->setVar('search_tools_path', 'Search/search_tools_related_list_html.php');
print $this->render($this->request->getDirectoryPathForThemeFile('views/find/Results/common/search_options.php'));
