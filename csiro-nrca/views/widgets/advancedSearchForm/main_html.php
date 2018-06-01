<?php
$po_request = $this->getVar('request');
$vs_widget_id = $this->getVar('widget_id');
$t_form = $this->getVar('t_form');
$va_form_element_list = $this->getVar('form_elements');
$va_settings = $this->getVar('settings');
$vn_num_columns = $va_settings['form_width'] ?: 2;
$vs_col_class = 'col-md-' . floor(12 / $vn_num_columns);
$va_form_element_groups = array_chunk($va_form_element_list, $vn_num_columns);

$va_flds = array();
foreach($va_form_element_list as $vn_i => $va_element) {
    $va_flds[] = "'".$va_element['name']."'";
}

$vn_type_id = intval($this->getVar('type_id'));
$vs_type_id_form_element = '';
if ($vn_type_id) {
    $vs_type_id_form_element = '<input type="hidden" name="type_id" value="'.$vn_type_id.'"/>';
}

$t_form = $this->getVar('t_form');
$vn_form_id = $t_form->getPrimaryKey();
$vs_controller_name = $this->getVar('controller_name');
$vs_widget_id = $this->getVar('widget_id');

$o_dm = Datamodel::load();
?>
<div class="widget widget-advanced-search-form">
    <?php if (!$vn_form_id): ?>
        <p class="text-warning">
        <?php print _t("You must define a search form before you can use the advanced search."); ?>
        </p>
        <a href="<?php print caNavLink($this->request, 'manage', 'SearchForm', 'ListForms'); ?>" class="btn btn-default btn-sm">
            <strong><?php print _t('Create a form'); ?></strong>
        </a>
    <?php else: ?>
        <?php if (!$t_form->haveAccessToForm($this->request->getUserID(), __CA_SEARCH_FORM_READ_ACCESS__, $vn_form_id)): ?>
            <div class="alert-danger">
                <span class="glyphicon glyphicon-warning-sign"></span>
                <?php print _t('You do not have access to this form'); ?>
            </div>
        <?php else: ?>
            <h3>
                <?php print unicode_ucfirst($o_dm->getTableProperty($t_form->get('table_num'), 'NAME_PLURAL')); ?>:
                <?php print $t_form->getLabelForDisplay(); ?>
            </h3>
            <?php print caFormTag($this->request, 'Index', "AdvancedSearchForm_{$vs_widget_id}", "find/{$vs_controller_name}", 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                <?php foreach ($va_form_element_groups as $va_group): ?>
                    <div class="row">
                        <?php foreach ($va_group as $va_element): ?>
                            <div class="<?php print $vs_col_class; ?>">
                                <div class="form-group">
                                    <label><?php print $va_element['label']; ?></label>
                                    <?php print $va_element['element']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <input type="hidden" name="form_id" value="<?php print $vn_form_id; ?>" />
                <div class="btn-group pull-right">
                    <button type="reset" class="btn btn-default">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php print _t('Clear'); ?>
                    </button>
                    <button class="btn btn-success">
                        <span class="glyphicon glyphicon-search"></span>
                        <?php print _t('Search'); ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
