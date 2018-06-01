<?php
AssetLoadManager::register('sortableUI');

$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_tour = $this->getVar('t_tour');
$t_stop = $this->getVar('t_stop');
$va_initial_values = $this->getVar('stops');
$va_errors = array();
$va_failed_inserts = array();
$vs_edit_tour_stops = urldecode(caUrl($this->request, 'editor/tour_stops', 'TourStopEditor', 'Edit', array('stop_id' => '{stop_id}')));
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-tour-stops">
	<textarea class="item-template hidden">
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="tour-stop repeating-item">
            <div class="elements-container removable">
                <div class="error_{n} collapse" onload="showErrorAlert('error_{n}', '{error})'">
                    <div class="alert alert-danger ">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                        {error}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="formLabel hidden" id="{fieldNamePrefix}edit_name_{n}">
                            <label><?php print _t("Name") ?></label>
                            <input id="{fieldNamePrefix}name_{n}" name="{fieldNamePrefix}name_{n}" value="{name}"/>
                            <label><?php print _t("Type") ?></label>
                            <?php print $t_stop->getTypeListAsHTMLFormElement('{fieldNamePrefix}type_id_{n}'); ?>
                        </div>
                        <span id="{fieldNamePrefix}screen_name_{n}">
                            <label>{name}</label>
                            {typename}
                        </span>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php print $vs_edit_tour_stops; ?>" id="{fieldNamePrefix}edit_{n}" class="btn btn-default">
                            <span class="glyphicon glyphicon-edit"></span>
                            <label><?php print _t("Edit Tour Stops") ?></label>
                        </a>
                    </div>
                </div>
            </div>
            <button type="button" class="remove" title="<?php print _t('Remove this relationship'); ?>">
                <?php print _t('Remove'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
		</div>
	</textarea>

	<div class="bundleContainer">
		<div class="list-item"></div>
		<button type="button" class="add top-right">
            <?php print _t("Add stop"); ?>
            <span class="glyphicon glyphicon-plus"></span>
        </button>
	</div>

    <input type="hidden" id="<?php print $vs_id_prefix; ?>_StopBundleList" name="<?php print $vs_id_prefix; ?>_StopBundleList" value=""/>
</div>

<script>
    var showErrorAlert;

    (function($) {
        $(function(){
            showErrorAlert = function(n, error){
                if(!!error && error.length > 0) {
                    $('#<?php print $vs_id_prefix; ?>Item_' + n + ' .error_' + n).collapse('show');
                }
            }
        });
    })(jQuery);

	caUI.initBundle('#<?php print $vs_id_prefix; ?>', {
		fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
		templateValues: ['name', 'locale_id', 'rank', 'stop_id', 'typename'],
		initialValues: <?php print json_encode($va_initial_values); ?>,
		initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
		errors: <?php print json_encode($va_errors); ?>,
		forceNewValues: <?php print json_encode($va_failed_inserts); ?>,
		itemID: '<?php print $vs_id_prefix; ?>Item_',
		templateClassName: 'item-template',
		itemListClassName: 'list-item',
		itemClassName: 'tour-stop',
		addButtonClassName: 'add',
		deleteButtonClassName: 'remove',
		showOnNewIDList: ['<?php print $vs_id_prefix; ?>_edit_name_'],
		hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_stop_info_', '<?php print $vs_id_prefix; ?>_edit_'],
		showEmptyFormsOnLoad: 1,
		isSortable: true,
		listSortOrderID: '<?php print $vs_id_prefix; ?>_StopBundleList',
		defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
	});
</script>
