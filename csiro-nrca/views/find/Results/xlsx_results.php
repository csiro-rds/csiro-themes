<?php
$va_display_list = $this->getVar('display_list');
$vo_result = $this->getVar('result');
$vn_ratio_pixels_to_excel_height = 0.85;
$vn_ratio_pixels_to_excel_width = 0.135;
$va_supercol_a_to_z = range('A', 'Z');
$vs_supercol = '';
$va_a_to_z = range('A', 'Z');

$t_workbook = new PHPExcel();

// more accurate (but slower) automatic cell size calculation
PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

$o_sheet = $t_workbook->getActiveSheet();

// mise en forme
$columntitlestyle = array(
    'font' => array(
        'name' => 'Arial',
        'size' => 12,
        'bold' => true
    ),
    'alignment'=>array(
        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap' => true,
        'shrinkToFit'=> true
    ),
    'borders' => array(
        'allborders'=>array(
            'style' => PHPExcel_Style_Border::BORDER_THICK
        )
    )
);

$cellstyle = array(
    'font'=>array(
        'name' => 'Arial',
        'size' => 11,
        'bold' => false
    ),
    'alignment'=>array(
        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap' => true,
        'shrinkToFit'=> true
    ),
    'borders' => array(
        'allborders'=>array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$o_sheet->getDefaultStyle()->applyFromArray($cellstyle);
$o_sheet->setTitle('CollectiveAccess');
$vs_column = reset($va_a_to_z);
$vn_line = 1;

// Column headers
$o_sheet->getRowDimension($vn_line)->setRowHeight(30);
foreach ($va_display_list as $vn_placement_id => $va_info) {
    if ($vs_column) {
        $o_sheet->setCellValue($vs_supercol.$vs_column.$vn_line,$va_info['display']);
        $o_sheet->getStyle($vs_supercol.$vs_column.$vn_line)->applyFromArray($columntitlestyle);
        $vs_column = next($va_a_to_z);
        if (!$vs_column) {
            $vs_supercol = array_shift($va_supercol_a_to_z);
            $vs_column = reset($va_a_to_z);
        }
    }
}

// Other lines
for ($vn_line = 2; $vo_result->nextHit(); ++$vn_line) {
    $vs_column = reset($va_a_to_z);
    $va_supercol_a_to_z = range('A', 'Z');
    $vs_supercol = '';

    // default to automatic row height. works pretty well in Excel but not so much in LibreOffice/OOo :-(
    $o_sheet->getRowDimension($vn_line)->setRowHeight(-1);

    if (!is_array($va_media_versions = $vo_result->getMediaVersions('ca_object_representations.media'))) {
        $va_media_versions = [];
    }

    foreach ($va_display_list as $vn_placement_id => $va_info) {
        if ((strpos($va_info['bundle_name'], 'ca_object_representations.media') !== false) && (!isset($va_info['settings']['display_mode']) || ($va_info['settings']['display_mode'] !== 'url'))) {
            $va_bits = explode(".", $va_info['bundle_name']);
            $vs_version = array_pop($va_bits);

            if (!in_array($vs_version, $va_media_versions)) {
                $vs_version = $va_media_versions[sizeof($va_media_versions) - 1];
            }

            $va_info = $vo_result->getMediaInfo('ca_object_representations.media',$vs_version);

            if($va_info['MIMETYPE'] === 'image/jpeg') { // don't try to insert anything non-jpeg into an Excel file
                if (is_file($vs_path = $vo_result->getMediaPath('ca_object_representations.media',$vs_version))) {
                    $image = "image".$vs_supercol.$vs_column.$vn_line;
                    $drawing = new PHPExcel_Worksheet_Drawing();
                    $drawing->setName($image);
                    $drawing->setDescription($image);
                    $drawing->setPath($vs_path);
                    $drawing->setCoordinates($vs_supercol.$vs_column.$vn_line);
                    $drawing->setWorksheet($o_sheet);
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                }

                $vn_width = floor(intval($va_info['PROPERTIES']['width']) * $vn_ratio_pixels_to_excel_width);
                $vn_height = floor(intval($va_info['PROPERTIES']['height']) * $vn_ratio_pixels_to_excel_height);

                // set the calculated withs for the current row and column,
                // but make sure we don't make either smaller than they already are
                if ($vn_width > $o_sheet->getColumnDimension($vs_supercol.$vs_column)->getWidth()) {
                    $o_sheet->getColumnDimension($vs_supercol.$vs_column)->setWidth($vn_width);
                }
                if ($vn_height > $o_sheet->getRowDimension($vn_line)->getRowHeight()){
                    $o_sheet->getRowDimension($vn_line)->setRowHeight($vn_height);
                }
            }
        } elseif ($vs_display_text = $this->getVar('t_display')->getDisplayValue($vo_result, $vn_placement_id, array_merge(array('request' => $this->request, 'purify' => true), is_array($va_info['settings']) ? $va_info['settings'] : array()))) {
            $o_sheet->setCellValue($vs_supercol.$vs_column.$vn_line, html_entity_decode(strip_tags(br2nl($vs_display_text)), ENT_QUOTES | ENT_HTML5));
            // We trust the autosizing up to a certain point, but we want column widths to be finite :-).
            // Since Arial is not fixed-with and font rendering is different from system to system, this can get a
            // little dicey. The values come from experimentation.
            if ($o_sheet->getColumnDimension($vs_supercol.$vs_column)->getWidth() == -1) {  // don't overwrite existing settings
                if (strlen($vs_display_text) > 55) {
                    $o_sheet->getColumnDimension($vs_supercol.$vs_column)->setWidth(50);
                }
            }
        }

        $vs_column = next($va_a_to_z);
        if (!$vs_column) {
            $vs_supercol = array_shift($va_supercol_a_to_z);
            $vs_column = reset($va_a_to_z);
        }
    }
}

// set column width to auto for all columns where we haven't set width manually yet
foreach (range('A','Z') as $vs_chr) {
    if ($o_sheet->getColumnDimension($vs_chr)->getWidth() === -1) {
        $o_sheet->getColumnDimension($vs_chr)->setAutoSize(true);
    }
}

@header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
@header('Content-Disposition:inline;filename=Export.xlsx ');

$o_writer = new PHPExcel_Writer_Excel2007($t_workbook);
$o_writer->save('php://output');
