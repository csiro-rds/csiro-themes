<?php
$t_subject = $this->getVar('t_subject');
$vs_table = $t_subject->tableName();
$va_lookup_urls = caJSONLookupServiceUrl($this->request, $vs_table, array('noInline' => 1));
$vn_type_id = intval($this->getVar('type_id'));
$vb_uses_hierarchy_browser = $this->getVar('uses_hierarchy_browser');
?>

<?php if (!$this->request->isAjax()): ?>
    <div class="well well-sm clearfix">
        <?php print caFormTag($this->request, 'Index', 'BasicSearchForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
            <?php print ($vb_uses_hierarchy_browser && $vn_type_id ? '<input type="hidden" name="type_id" value="' . $vn_type_id . '"/>' : ''); ?>
            <div class="simple-search-box">
                <label for="BasicSearchInput"><?php print _t('Search'); ?></label>
                <div class="input-group">
                    <input id="BasicSearchInput" name="search" placeholder="Search" value="<?php print htmlspecialchars($this->getVar('search'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Search <?php print $t_subject->getProperty('NAME_PLURAL'); ?>" />
                    <div class="input-group-btn">
                        <button class="btn btn-success" id="BasicSearchSubmit">
                            <?php print _t('Search'); ?>
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button type="button" onclick="caSaveSearch('BasicSearchForm', jQuery('#BasicSearchInput').val(), ['search']);" class="btn btn-default">
                            <?php print _t('Save search'); ?>
                            <span class="glyphicon glyphicon-save-file"></span>
                        </button>
                        <?php if ($vb_uses_hierarchy_browser): ?>
                            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#browse">
                                <?php print _t('Hierarchy browser'); ?>
                                <span class="caret"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php if ($vb_uses_hierarchy_browser): ?>
        <div id="browse" class="collapse">
            <h2><?php print _t('Hierarchy'); ?></h2>
            <?php if ($this->request->user->canDoAction('can_edit_'.$vs_table) && ($this->getVar('num_types') > 0)): ?>
                <div id="browseTypeMenu" class="clearfix">
                    <form action='#'>
                        <?php print _t('Add under %2 new %1', $this->getVar('type_menu')); ?>
                        <a href="#" onclick="document.location = '<?php print caEditorUrl($this->request, $vs_table, 0); ?>/type_id/' + jQuery('#hierTypeList').val() + '/parent_id/' + oHierBrowser.getSelectedItemID();">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                        <span id='browseCurrentSelection'></span>
                    </form>
                </div>
            <?php endif; ?>
	<div class="component-hierarchy-browser">
		<div id="hierarchyBrowser" class="hierarchyBrowser"></div>
	</div>
</div>
    <?php endif; ?>
<?php endif; ?>

<script>
    var oHierBrowser, stateCookieJar = jQuery.cookieJar('caCookieJar');

    function caOpenBrowserWith (id) {
    	oHierBrowser.setUpHierarchy(id);
        jQuery("#browse").collapse('open');        
    }

    function caSaveSearch (form_id, label, field_names) {
        var vals = {};
        jQuery(field_names).each(function(i, field_name) {
            vals[field_name] = jQuery('#' + form_id + ' [name=' + field_name + ']').val();
        });
        vals['_label'] = label;
        vals['_field_list'] = field_names;
        jQuery.getJSON(
            '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), "addSavedSearch"); ?>',
            vals,
            function(data) {
                if ((data) && (data.md5)) {
                    jQuery('.savedSearchSelect').prepend(jQuery("<option></option>").attr("value", data.md5).text(data.label)).attr('selectedIndex', 0);
                }
            }
        );
    }

    // Show "add to set" controls if set tools is open
    (function () {
        'use strict';

        $(function() {
            if ($("#searchSetTools").is(":visible")) {
                $(".addItemToSetControl").show();
            }

            <?php if (!$this->request->isAjax() && $vb_uses_hierarchy_browser): ?>
                $('#browseTypeMenu .sf-hier-menu .sf-menu a').click(function () {
                    $(document).attr('location', $(this).attr('href') + oHierBrowser.getSelectedItemID());
                    return false;
                });

                oHierBrowser = caUI.initHierBrowser('hierarchyBrowser', {
                    levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
                    initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',
                    editUrl: '<?php print caEditorUrl($this->request, $vs_table, null, false, array(), array('action' => $this->getVar('default_action'))); ?>',
                    editButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_RIGHT_ARROW__, 1); ?>",
                    disabledButtonIcon: "<?php print caNavIcon(__CA_NAV_ICON_DOT__, 1); ?>",
                    disabledItems: 'full',
                    allowDragAndDropSorting: <?php print caDragAndDropSortingForHierarchyEnabled($this->request, $t_subject->tableName(), null) ? "true" : "false"; ?>,
                    sortSaveUrl: '<?php print $va_lookup_urls['sortSave']; ?>',
                    dontAllowDragAndDropSortForFirstLevel: true,
                    initItemID: '<?php print $this->getVar('browse_last_id'); ?>',
                    indicator: "<?php print caNavIcon(__CA_NAV_ICON_SPINNER__, 1); ?>",
                    typeMenuID: 'browseTypeMenu',
                    currentSelectionDisplayID: 'browseCurrentSelection'
                });

                $('#BasicSearchInput').autocomplete(
                    {
                        minLength: 3,
                        delay: 800,
                        html: true,
                        source: '<?php print $va_lookup_urls['search']; ?>',
                        select: function(event, ui) {
                            if (parseInt(ui.item.id) > 0) {
                            	$('#BasicSearchInput').attr("placeholder", ui.item.value).val("").focus().blur();
                                caOpenBrowserWith(ui.item.id);
                            }
                            event.preventDefault();
                        }
                    }
                );

                $("#browse").on('shown.bs.collapse', function() {
                    stateCookieJar.set('<?php print $vs_table; ?>BrowserIsClosed', 0);
                });

                $("#browse").on('hidden.bs.collapse', function() {
                    stateCookieJar.set('<?php print $vs_table; ?>BrowserIsClosed', 1);
                });
            <?php endif; ?>
        });
    }(jQuery));
</script>
