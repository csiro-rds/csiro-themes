<?php
if (!$this->getVar('no_hierarchies_defined')) {
    print $this->render($this->request->getDirectoryPathForThemeFile('views/find/Results/common/results_list.php'));
}
