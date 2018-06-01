<?php
header("Content-type: {$this->getVar('export_mimetype')}");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Cache-control: private");
header("Content-Disposition: attachment; filename={$this->getVar('export_filename')}");
print $this->getVar('export_data');
