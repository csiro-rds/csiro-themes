<?php
$vn_type_id = intval($this->getVar('type_id'));
$t_form = $this->getVar('t_form');
$vn_form_id = $t_form->getPrimaryKey();
$vb_has_form_access = $t_form->haveAccessToForm($this->request->getUserID(), __CA_SEARCH_FORM_READ_ACCESS__, $vn_form_id);

$va_fields = array_map(function ($va_element) {
    return "'" . $va_element['name'] . "'";
}, $this->getVar('form_elements'));
?>

<div class="component component-search-form component-search-form-advanced">
    <?php print caFormTag($this->request, 'Index', 'AdvancedSearchForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?php print _t('Advanced Search'); ?></div>
            </div>
            <div class="panel-body">
                <?php print $this->render('Search/search_forms/search_form_table_html.php'); ?>
            </div>
            <div class="panel-footer clearfix">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default" onclick="caAdvancedSearchFormReset();">
                        <span class="glyphicon glyphicon-refresh"></span>
                        <?php print _t('Reset'); ?>
                    </button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="caSearchAdvancedSaveSearchModal">
                        <span class="glyphicon glyphicon-save"></span>
                        <?php print _t('Save search'); ?>&hellip;
                    </button>
                    <button class="btn btn-success">
                        <span class="glyphicon glyphicon-search"></span>
                        <?php print _t('Search'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php if ($vn_type_id): ?>
            <input type="hidden" name="type_id" value="<?php print $vn_type_id; ?>"/>
        <?php endif; ?>
    </form>
</div>

<div id="caSearchAdvancedSaveSearchModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php print _t('Save search'); ?>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php print _t("Save search as"); ?></label>
                    <input name="_label" id="caAdvancedSearchSaveLabelInput" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default" data-toggle="modal">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Cancel'); ?>
                    </button>
                    <button type="button" class="btn btn-success" onclick="caSaveSearch('AdvancedSearchForm', jQuery('#caAdvancedSearchSaveLabelInput').val(), [<?php print join(',', $va_fields); ?>]);">
                        <span class="glyphicon glyphicon-save"></span>
                        <?php print _t('Save'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function caSaveSearch(form_id, label, field_names) {
        var vals = {};
        jQuery(field_names).each(function(i, field_name) {
            var field_name_with_no_period = field_name.replace('.', '_');
            vals[field_name] = jQuery('#' + form_id + ' [id=' + field_name_with_no_period + ']').val();
        });
        vals['_label'] = label;
        vals['_field_list'] = field_names;
        vals['_form_id'] = <?php print (int)$vn_form_id; ?>;

        jQuery.getJSON('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), "addSavedSearch"); ?>', vals, function(data, status) {
            if ((data) && (data.md5)) {
                jQuery('.savedSearchSelect').prepend(jQuery("<option></option>").attr("value", data.md5).text(data.label)).attr('selectedIndex', 0);

            }
        });
    }

    function caAdvancedSearchFormReset() {
        jQuery('#AdvancedSearchForm textarea').val('');
        jQuery('#AdvancedSearchForm input[type=text]').val('');
        jQuery('#AdvancedSearchForm input[type=hidden]').val('');
        jQuery('#AdvancedSearchForm select').val('');
        jQuery('#AdvancedSearchForm input[type=checkbox]').attr('checked', 0);
    }

    <?php if (!$vn_form_id || !$vb_has_form_access): ?>
        (function ($) {
            'use strict';

            $(function () {
                <?php if (!$vn_form_id): ?>
                    caUI.addNotification('warning', '<?php print _t("You must define a search form before you can use the advanced search.").' '.caNavLink($this->request, _t('Click here to create a new form.'), '', 'manage', 'SearchForm', 'ListForms'); ?>');
                <?php elseif (!$vb_has_form_access): ?>
                    caUI.addNotification('error', '<?php print _t('You do not have access to this form'); ?>');
                <?php endif; ?>
            });
        }(jQuery));
    <?php endif; ?>
</script>