<?php
/**
 * Take a flat list of bundles and a map of form elements, and produce a grouped array of form elements based on the
 * selection of `new_group` options in the bundles.
 *
 * For example, if the bundles were A, B, C, D, E, and bundles B and D had `new_group` selected, the return value would
 * be `[ [ A ], [ B, C ], [ D, E ] ]`.
 *
 * @param array $pa_bundle_list Single dimension array of bundle definition objects.
 * @param array $pa_form_elements Map of bundle keys to form element HTML strings.
 *
 * @return array Nested array containing the HTML string values from `$pa_form_elements`.
 */
function groupFormElementsByBundle ($pa_bundle_list, $pa_form_elements) {
	$va_form_elements_grouped = array( array() );
    $va_bundle_list_keys = array_keys($pa_bundle_list);
	foreach (array_keys($pa_form_elements) as $vn_i => $vs_form_element_key) {
	    $va_bundle = $vn_i < sizeof($pa_bundle_list) ? $pa_bundle_list[$va_bundle_list_keys[$vn_i]] : null;
		if ($va_bundle && isset($va_bundle['new_group']) && $va_bundle['new_group'] && !empty($va_form_elements_grouped[sizeof($va_form_elements_grouped) - 1])) {
			$va_form_elements_grouped[] = array();
		}
		$va_form_elements_grouped[sizeof($va_form_elements_grouped) - 1][] = $pa_form_elements[$vs_form_element_key];
	}
	return $va_form_elements_grouped;
}

/**
 * Callback for batch editor bootstrap loading bars
 *
 *
 */
function caIncrementBatchEditorProgress($po_request, $pn_rows_complete, $pn_total_rows, $ps_message, $pn_elapsed_time, $pn_memory_used, $pn_num_processed, $pn_num_errors) {
    $vn_memory_used = sprintf("%4.2f", ($pn_memory_used / 1048576));
    $vn_num_errors = !!$pn_num_errors ? $pn_num_errors : 0;

    if (is_null($ps_message)) {
        $ps_message = _t('Processed %1/%2', $pn_rows_complete, $pn_total_rows);
    }
    print "<script>" .
        "(function($) {" .
            "'use strict';" .
                "$(function() {" .
                    "updateProgress({$pn_rows_complete}, {$pn_total_rows}, {$vn_num_errors}, {$pn_elapsed_time}, {$vn_memory_used}, {$t_new_rep});" .
                "});" .
            "})(jQuery);" .
        "</script>";
    caFlushOutput();
}

/**
 * Callback for batch editor result report.
 */
function caCreateBatchEditorResultsReport($po_request, $pa_general, $pa_notices, $pa_errors) {
    $va_results = [];

    if (is_array($pa_errors) && sizeof($pa_errors)) {
        foreach($pa_errors as $vn_id => $va_error) {
            $va_results[$vn_id]['errors'] = [];
            if(!isset($va_results[$vn_id])) {
                $va_results[$vn_id] = array(
                    'notices' => [],
                    'errors' => []
                );
            }
            foreach($va_error['errors'] as $o_error) {
                $va_results[$vn_id]['errors'][] = $o_error->getErrorDescription();
            }
        }
    }
    if (is_array($pa_notices) && sizeof($pa_notices)) {
        foreach($pa_notices as $vn_id => $va_notice) {
            if(!isset($va_results[$vn_id])) {
                $va_results[$vn_id] = array(
                    'notices' => [],
                    'errors' => [],
                );
            }

            $va_results[$vn_id]['idno'] = $vn_id;
            $va_results[$vn_id]['label'] = $va_notice['label'];
            $va_results[$vn_id]['status'] = $va_notice['status'];
            $va_results[$vn_id]['link'] = caNavUrl($po_request, preg_replace("![\r\n\t]+!", " ", $va_notice['label']), '', $pa_general['table'], $vn_id);
            $va_results[$vn_id]['message'] = $va_notice['message'];
        }
    }

    print "<script>";
    print "$(function() {" .
        "appendResults('" . json_encode($va_results) . "');" .
        "});";
    print "</script>";
    caFlushOutput();
}
