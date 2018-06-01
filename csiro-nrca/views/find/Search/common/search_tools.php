<?php
$t_subject = $this->getVar('t_subject');
$vs_url_string = $this->getVar('relatedListURLParamString');

$va_export_mappings = $this->getVar('exporter_list');
$vb_display_export = is_array($va_export_mappings) && sizeof($va_export_mappings) > 0;

$va_label_format_options = array();
foreach($this->getVar('label_formats') as $va_form_info) {
    $va_label_format_options[$va_form_info['name']] = $va_form_info['code'];
}
uksort($va_label_format_options, 'strnatcasecmp');
$vs_current_label_format = $this->getVar('current_label_form');
$vb_display_print_labels = is_array($va_label_format_options) && sizeof($va_label_format_options);

$va_export_format_options = array();
foreach($this->getVar('export_formats') as $vn_i => $va_format_info) {
    $va_export_format_options[$va_format_info['name']] = $va_format_info['code'];
}
$vb_display_download_results = true;
$vs_current_export_format = $this->getVar('current_export_format');

$va_download_version_options = array();
$va_download_versions = $this->request->config->getList('ca_object_representation_download_versions');
$va_download_selection_types = array(
    'selected' => _t('Selected results: %1', $vs_version),
    'all' => _t('All results: %1', $vs_version)
);
foreach($va_download_versions as $vs_version) {
    foreach($va_download_selection_types as $vs_mode => $vs_label) {
        $va_download_versions[$vs_label] = "{$vs_mode}_{$vs_version}";
    }
}
ksort($va_download_versions);
$vb_display_download_media = in_array($t_subject->tableName(), array('ca_objects', 'ca_object_representations')) &&
    ($this->request->user->canDoAction('can_download_ca_object_representations')) &&
    is_array($va_download_versions);

$vn_visible_components = sizeof(array_filter(array( $vb_display_export, $vb_display_print_labels, $vb_display_download_results, $vb_display_download_media )));
$vn_column_size = floor(12 / $vn_visible_components);
$vn_remaining_column_size = 12 - $vn_column_size * $vn_visible_components;
?>
<div id="component component-search-tools">
    <div id="searchToolsBox" class="search-box collapse">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?php print _t('Export tools'); ?></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php if ($vb_display_export): ?>
                        <div class="col-md-<?php print $vn_column_size; ?>">
                            <?php print caFormTag($this->request, 'ExportData' . $vs_url_string, 'caExportWithMappingForm', 'manage/MetadataExport', 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                            <input type="hidden" name="caIsExportFromSearchOrBrowseResult" value="1" />
                            <input type="hidden" name="find_type" value="<?php print $this->getVar('find_type'); ?>" />
                            <label><?php print _t("Export results with mapping"); ?></label>
                            <div class="input-group">
                                <select name="exporter_id" class="form-control">
                                    <?php foreach ($va_export_mappings as $va_exporter): ?>
                                        <option value="<?php print $va_exporter['exporter_id']; ?>">
                                            <?php print $va_exporter['label']; ?> (<?php print $va_exporter['exporter_code']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="input-group-btn">
                                <button class="btn btn-success">
                                    <span class="glyphicon glyphicon-ok"></span>
                                    <?php print _t('Export results'); ?>
                                </button>
                            </span>
                            </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if ($vb_display_print_labels): ?>
                        <div class="col-md-<?php print $vn_column_size; ?>">
                            <?php print caFormTag($this->request, 'printLabels' . $vs_url_string, 'caPrintLabelsForm', $this->request->getModulePath().'/'.$this->request->getController(), 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                            <input type="hidden" name="download" value="1" />
                            <label><?php print _t("Print results as labels"); ?></label>
                            <div class="input-group">
                                <select name="label_form" class="form-control">
                                    <?php foreach ($va_label_format_options as $vs_label => $vs_value): ?>
                                        <option value="<?php print $vs_value; ?>" <?php print ($vs_value === $vs_current_label_format ? 'selected' : ''); ?>>
                                            <?php print $vs_label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="input-group-btn">
                                <button class="btn btn-success">
                                    <span class="glyphicon glyphicon-ok"></span>
                                    <?php print _t('Print results'); ?>
                                </button>
                            </span>
                            </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if ($vb_display_download_results): ?>
                        <div class="col-md-<?php print $vn_column_size; ?>">
                            <?php print caFormTag($this->request, 'export' . $vs_url_string, 'caExportForm', $this->request->getModulePath().'/'.$this->request->getController(), 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                            <input type="hidden" name="download" value="1" />
                            <label><?php print _t("Download results as"); ?></label>
                            <div class="input-group">
                                <select name="export_format" class="form-control">
                                    <?php foreach ($va_export_format_options as $vs_label => $vs_value): ?>
                                        <option value="<?php print $vs_value; ?>" <?php print ($vs_value === $vs_current_export_format ? 'selected' : ''); ?>>
                                            <?php print $vs_label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="input-group-btn">
                                        <button class="btn btn-success">
                                            <span class="glyphicon glyphicon-ok"></span>
                                            <?php print _t('Download results'); ?>
                                        </button>
                                    </span>
                            </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if ($vb_display_download_media): ?>
                        <div class="col-md-<?php print $vn_column_size; ?>">
                            <form id="caDownloadMediaFromSearchResult">
                                <label><?php print _t("Download media as"); ?></label>
                                <div class="input-group">
                                    <select name="mode" class="form-control">
                                        <?php foreach ($va_download_versions as $vs_version_label => $vs_version): ?>
                                            <option value="<?php print $vs_version; ?>">
                                                <?php print $vs_version_label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success" onclick="caDownloadRepresentations(jQuery('#caDownloadRepresentationMode').val());">
                                            <span class="glyphicon glyphicon-ok"></span>
                                            <?php print _t('Download media'); ?>
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if ($vn_remaining_column_size > 0): ?>
                        <div class="col-md-offset-<?php print $vn_remaining_column_size; ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-footer clearfix">
                <button type="button" class="btn btn-default pull-right" data-toggle="collapse" data-target="#searchToolsBox">
                    <span class="glyphicon glyphicon-collapse-up"></span>
                    <?php print _t('Hide'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function caDownloadRepresentations(mode) {
        var tmp = mode.split('_');
        window.location = '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'DownloadMedia'); ?>/<?php print $t_subject->tableName(); ?>' + (tmp[0] === 'all' ? '/all' : caGetSelectedItemIDsToAddToSet().join(';')) + '/version/' + tmp[1] + '/download/1';
    }
</script>
