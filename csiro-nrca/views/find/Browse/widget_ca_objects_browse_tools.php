<?php
$this->setVar('extra_template_path', $this->request->getDirectoryPathForThemeFile('views/find/Browse/objects/viz_tools.php'));
?>
<?php print $this->render($this->request->getDirectoryPathForThemeFile('views/find/Browse/common/widget_browse_tools.php'));
