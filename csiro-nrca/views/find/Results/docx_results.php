<?php
$t_display = $this->getVar('t_display');
$vo_result = $this->getVar('result');
$vn_cm_to_twips = 567; // For easier calculation; 1 cm = 1440/2.54 = 566.93 twips
$t_word = new \PhpOffice\PhpWord\PhpWord();

// Every element you want to append to the word document is placed in a section.
// New portrait section
$va_section_style = array(
    'orientation' => 'portrait',
    'marginTop' => 2 * $vn_cm_to_twips,
    'marginBottom' => 2 * $vn_cm_to_twips,
    'marginLeft' => 2 * $vn_cm_to_twips,
    'marginRight' => 2 * $vn_cm_to_twips,
    'headerHeight' => 1 * $vn_cm_to_twips,
    'footerHeight' => 1 * $vn_cm_to_twips,
    'colsNum' => 1,
);
$t_section = $t_word->addSection($va_section_style);

// Add header for all pages
$t_header = $t_section->addHeader();
$vs_header_image = $this->request->getThemeDirectoryPath() . "/graphics/logos/" . $this->request->config->get('report_img');
if (file_exists($vs_header_image)) {
    $t_header->addImage($vs_header_image,array( 'height' => 30, 'wrappingStyle' => 'inline' ));
}

// Add footer
$t_footer = $t_section->addFooter();
$t_footer->addPreserveText('{PAGE}/{NUMPAGES}', null, array('align' => 'right'));

// Defining font style for headers
$t_word->addFontStyle('headerStyle', array(
    'name'=>'Verdana',
    'size'=>12,
    'color'=>'444477'
));

// Defining font style for display values
$t_word->addFontStyle('displayValueStyle', array(
    'name'=>'Verdana',
    'size'=>14,
    'color'=>'000000'
));
$va_font_header = array('bold'=>true, 'size'=>13, 'name'=>'Calibri');
$va_font_bundle_name = array('bold'=>false, 'underline'=>'single', 'color'=>'666666', 'size'=>11, 'name'=>'Calibri');
$va_font_content = array('bold'=>false, 'size'=>11, 'name'=>'Calibri');

// Table style
$va_style_table = array('borderSize'=>0, 'borderColor'=>'ffffff', 'cellMargin'=>80);
$va_style_first_row = array('borderBottomSize'=>18, 'borderBottomColor'=>'0000FF');
$t_word->addTableStyle('myOwnTableStyle', $va_style_table, $va_style_first_row);

while ($vo_result->nextHit()) {
    $t_table = $t_section->addTable('myOwnTableStyle');
    $t_table->addRow();
    $va_list = $this->getVar('display_list');

    // First column : media
    $t_media_cell = $t_table->addCell( 5 * $vn_cm_to_twips);
    $va_info = $vo_result->getMediaInfo('ca_object_representations.media',"medium");

    if ($va_info['MIMETYPE'] === 'image/jpeg') { // don't try to insert anything non-jpeg into an Excel file
        $vs_path = $vo_result->getMediaPath('ca_object_representations.media', 'medium');
        if (is_file($vs_path)) {
            $t_media_cell->addImage(
                $vs_path,
                array(
                    'width' => 195,
                    'wrappingStyle' => 'inline'
                )
            );
        }
    }

    // Second column : bundles
    $t_content_cell = $t_table->addCell(12 * $vn_cm_to_twips);
    $t_content_cell->addText(
        caEscapeForXML(html_entity_decode(strip_tags(br2nl($vo_result->get('preferred_labels'))), ENT_QUOTES | ENT_HTML5)),
        $va_font_header
    );

    foreach ($va_list as $vn_placement_id => $va_info) {
        $vs_display_text = $t_display->getDisplayValue(
            $vo_result,
            $vn_placement_id,
            array_merge(
                array('request' => $this->request, 'purify' => true),
                is_array($va_info['settings']) ? $va_info['settings'] : array()
            )
        );

        // make sure that for the 'url' mode we don't insert the image here
        if (strpos($va_info['bundle_name'], 'ca_object_representations.media') !== false && ($va_info['settings']['display_mode'] === 'media')) {
            // Inserting bundle name on one line
            $t_content_cell->addText(caEscapeForXML($va_info['display']).': ', $va_font_bundle_name);

            // Fetching version asked & corresponding file
            $vs_version = str_replace('ca_object_representations.media.', '', $va_info['bundle_name']);
            $va_info = $vo_result->getMediaInfo('ca_object_representations.media', $vs_version);

            // If it's a JPEG, print it (basic filter to avoid non handled media version)
            if ($va_info['MIMETYPE'] === 'image/jpeg') { // don't try to insert anything non-jpeg into an Excel file
                $vs_path = $vo_result->getMediaPath('ca_object_representations.media', $vs_version);
                if (is_file($vs_path)) {
                    $t_content_cell->addImage($vs_path);
                }
            }
        } elseif ($vs_display_text) {
            $t_text_run = $t_content_cell->createTextRun();
            $t_text_run->addText(caEscapeForXML($va_info['display']).': ', $va_font_bundle_name);
            $t_text_run->addText(
                caEscapeForXML(html_entity_decode(strip_tags(br2nl($vs_display_text)), ENT_QUOTES | ENT_HTML5)),
                $va_font_content
            );
        }
    }

    $t_section->addTextBreak(2);
}

// Write the document
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($t_word, 'Word2007');
header("Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header('Content-Disposition:inline;filename=Export.docx ');
$objWriter->save('php://output');
