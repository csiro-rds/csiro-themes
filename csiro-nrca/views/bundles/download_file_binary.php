<?php
header("Content-type: application/octet-stream");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Cache-control: private");
header("Content-Disposition: attachment; filename=".preg_replace('![^A-Za-z0-9\.\-]+!', '_', $this->getVar('archive_name')));

set_time_limit(0);

$o_zip = $this->getVar('zip_stream');
$vs_path = $this->getVar('archive_path');

if ($o_zip) {
    $o_zip->stream();
} elseif (file_exists($vs_path)) {
    $o_fp = @fopen($vs_path,"rb");
    while(is_resource($o_fp) && !feof($o_fp)) {
        print(@fread($o_fp, 1024*8));
        ob_flush();
        flush();
    }
    exit();
} else {
    throw new ApplicationException(_t('File for download does not exist'));
}
