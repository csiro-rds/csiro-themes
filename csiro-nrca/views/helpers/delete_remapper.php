<?php
// TODO FIXME

$t_instance = $this->getVar('t_instance');
$va_reference_to_buf = $this->getVar('reference_to_buf');
$vn_reference_to_count = $this->getVar('reference_to_count');
$va_reference_from_buf = $this->getVar('reference_from_buf');
$vn_reference_from_count = $this->getVar('reference_from_count');
$vs_typename = $this->getVar('typename');
$vs_default = $this->getVar('default');
?>
<?php if (sizeof($va_reference_to_buf)): ?>
    <p id="caReferenceHandlingToCount">
        <?php print _t($vn_reference_to_count == 1 ? 'This %1 is referenced %2 time' : 'This %1 is referenced %2 times', $vs_typename, $vn_reference_to_count); ?>.
        <?php print _t('When deleting this %1:', $vs_typename); ?>
    </p>
<?php
    $va_delete_opts = ['value' => 'delete', 'id' => 'caReferenceHandlingToDelete'];
    $va_remap_opts = ['value' => 'remap', 'id' => 'caReferenceToHandlingRemap'];
    $va_remap_lookup_opts = ['value' => '', 'size' => 40, 'id' => 'caReferenceHandlingToRemapTo', 'class' => 'lookupBg'];
    if ($vs_default === 'delete') {
        $va_delete_opts['checked'] = 1;
        $va_remap_lookup_opts['disabled'] = 1;
    } else {
        $va_remap_opts['checked'] = 1;
    }
    $vs_output .= caHTMLRadioButtonInput('caReferenceHandlingTo', $va_delete_opts).' '._t('remove all references')."<br/>\n";
    $vs_output .= caHTMLRadioButtonInput('caReferenceHandlingTo', $va_remap_opts).' '._t('transfer references to').' '.caHTMLTextInput('caReferenceHandlingToRemapTo', $va_remap_lookup_opts);
    $vs_output .= "<a href='#' class='button' onclick='jQuery(\"#caReferenceHandlingToRemapToID\").val(\"\"); jQuery(\"#caReferenceHandlingToRemapTo\").val(\"\"); jQuery(\"#caReferenceHandlingToClear\").css(\"display\", \"none\"); return false;' style='display: none;' id='caReferenceHandlingToClear'>"._t('Clear').'</a>';
    $vs_output .= caHTMLHiddenInput('caReferenceHandlingToRemapToID', array('value' => '', 'id' => 'caReferenceHandlingToRemapToID'));
    $vs_output .= "<script>";

    $va_service_info = caJSONLookupServiceUrl($this->request, $t_instance->tableName(), array('noSymbols' => 1, 'noInline' => 1, 'exclude' => (int)$t_instance->getPrimaryKey(), 'table_num' => (int)$t_instance->get('table_num')));
    $vs_output .= "jQuery(document).ready(function() {";
    $vs_output .= "jQuery('#caReferenceHandlingToRemapTo').autocomplete(
                {
                    source: '".$va_service_info['search']."', html: true,
                    minLength: 3, delay: 800,
                    select: function(event, ui) {
                        jQuery('#caReferenceHandlingToRemapToID').val(ui.item.id);
                        jQuery('#caReferenceHandlingClear').css('display', 'inline');
                    }
                }
            );";

    $vs_output .= "jQuery('#caReferenceToHandlingRemap').click(function() {
            jQuery('#caReferenceHandlingToRemapTo').attr('disabled', false);
        });
        jQuery('#caReferenceHandlingToDelete').click(function() {
            jQuery('#caReferenceHandlingToRemapTo').attr('disabled', true);
        });
        ";
    $vs_output .= "});";
    $vs_output .= "</script>\n";

    TooltipManager::add('#caReferenceHandlingToCount', "<h2>"._t('References to this %1', $t_instance->getProperty('NAME_SINGULAR'))."</h2>\n".join("\n", $va_reference_to_buf));
?>
<?php endif; ?>
<?php
if (sizeof($va_reference_from_buf)) {
    // add autocompleter for remapping
    if ($vn_reference_from_count == 1) {
        $vs_output .= "<h3 id='caReferenceHandlingFromCount'>"._t('This %1 references %2 other item in metadata', $vs_typename, $vn_reference_from_count).". "._t('When deleting this %1:', $vs_typename)."</h3>\n";
    } else {
        $vs_output .= "<h3 id='caReferenceHandlingFromCount'>"._t('This %1 references %2 other items in metadata', $vs_typename, $vn_reference_from_count).". "._t('When deleting this %1:', $vs_typename)."</h3>\n";
    }

    $va_delete_opts = ['value' => 'delete', 'id' => 'caReferenceHandlingFromDelete'];
    $va_remap_opts = ['value' => 'remap', 'id' => 'caReferenceHandlingFromRemap'];
    $va_remap_lookup_opts = ['value' => '', 'size' => 40, 'id' => 'caReferenceHandlingToRemapFrom', 'class' => 'lookupBg'];
    if ($vs_default === 'delete') {
        $va_delete_opts['checked'] = 1;
        $va_remap_lookup_opts['disabled'] = 1;
    } else {
        $va_remap_opts['checked'] = 1;
    }
    $vs_output .= caHTMLRadioButtonInput('caReferenceHandlingFrom', $va_delete_opts).' '._t('remove these references')."<br/>\n";
    $vs_output .= caHTMLRadioButtonInput('caReferenceHandlingFrom', $va_remap_opts).' '._t('transfer these references and accompanying metadata to').' '.caHTMLTextInput('caReferenceHandlingToRemapFrom', $va_remap_lookup_opts);
    $vs_output .= "<a href='#' class='button' onclick='jQuery(\"#caReferenceHandlingToRemapFromID\").val(\"\"); jQuery(\"#caReferenceHandlingToRemapFrom\").val(\"\"); jQuery(\"#caReferenceHandlingClear\").css(\"display\", \"none\"); return false;' style='display: none;' id='caReferenceHandlingClear'>"._t('Clear').'</a>';
    $vs_output .= caHTMLHiddenInput('caReferenceHandlingToRemapFromID', array('value' => '', 'id' => 'caReferenceHandlingToRemapFromID'));
    $vs_output .= "<script>";

    $va_service_info = caJSONLookupServiceUrl($this->request, $t_instance->tableName(), array('noSymbols' => 1, 'noInline' => 1, 'exclude' => (int)$t_instance->getPrimaryKey(), 'table_num' => (int)$t_instance->get('table_num')));
    $vs_output .= "jQuery(document).ready(function() {";
    $vs_output .= "jQuery('#caReferenceHandlingToRemapFrom').autocomplete(
                {
                    source: '".$va_service_info['search']."', html: true,
                    minLength: 3, delay: 800,
                    select: function(event, ui) {
                        jQuery('#caReferenceHandlingToRemapFromID').val(ui.item.id);
                        jQuery('#caReferenceHandlingClear').css('display', 'inline');
                    }
                }
            );";

    $vs_output .= "jQuery('#caReferenceHandlingFromRemap').click(function() {
            jQuery('#caReferenceHandlingToRemapFrom').attr('disabled', false);
        });
        jQuery('#caReferenceHandlingFromDelete').click(function() {
            jQuery('#caReferenceHandlingToRemapFrom').attr('disabled', true);
        });
        ";
    $vs_output .= "});";
    $vs_output .= "</script>\n";

    TooltipManager::add('#caReferenceHandlingFromCount', "<h2>"._t('References by this %1', $t_instance->getProperty('NAME_SINGULAR'))."</h2>\n".join("<br/>\n", $va_reference_from_buf));
}

print $vs_output;
