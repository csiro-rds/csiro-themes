<?php
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance = $this->getVar('t_subject');
$t_caption = $this->getVar('t_caption');
$va_settings = $this->getVar('settings');
$vs_add_label = $this->getVar('add_label') ?: _t("Add caption file");

$vb_read_only =	$va_settings['readonly']  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') == __CA_BUNDLE_ACCESS_READONLY__);

$va_initial_values = $this->getVar('initialValues');
if (!is_array($va_initial_values)) {
    $va_initial_values = array();
}
$vs_caption_download_url = caNavUrl($this->request, '*', '*', 'downloadCaptionFile', array('representation_id' => $t_instance->getPrimaryKey(), 'caption_id' => "{caption_id}", 'download' => 1), array('id' => "{$vs_id_prefix}download{caption_id}", 'class' => 'attributeDownloadButton'));
?>
<div id="<?php print $vs_id_prefix; ?>" class="component copmonent-bundle component-bundle-object-representation-captions">
    <?php print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $va_settings); ?>
	<textarea class="representiation-new-item-template hidden">
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo repeating-item">
            <div class="elements-container removable">
                <div class="caListItem">
                    <span class="formLabel"><?php print _t('VTT or SRT format caption file'); ?></span>
                    <?php print $t_caption->htmlFormElement('caption_file', '^ELEMENT', array('name' => $vs_id_prefix.'_caption_file{n}',
                        'id' => $vs_id_prefix.'_caption_file{n}', 'no_tooltips' => true)); ?>
                    <span class="formLabel"><?php print _t('Locale'); ?></span>
                    <?php print $t_caption->htmlFormElement('locale_id', '^ELEMENT', array('name' => $vs_id_prefix.'_locale_id{n}',
                        'id' => $vs_id_prefix.'_locale_id{n}', 'no_tooltips' => true, 'dont_show_null_value' => true)); ?>
                    <input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
                </div>
            </div>
            <button type="button" class="remove" title="<?php print _t('Remove this relationship'); ?>">
                <?php print $this->getVar('remove_label'); ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
		</div>
	</textarea>

	<textarea class="representation hidden">
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
            <div class="elements-container removable">
                <div class="caListItem">
                    <a href="<?php print $vs_caption_download_url ?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-download"></span>
                        <span class="formLabel">{locale} ({filesize})</span>
                    </a>
                    <input type="hidden" name="<?php print $vs_id_prefix; ?>_caption_id{n}" id="<?php print $vs_id_prefix; ?>_caption_id{n}" value="{caption_id}"/>
                </div>
            </div>
            <button type="button" class="remove" title="remove">
                <?php print _t('Remove') ?>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
		</div>
	</textarea>

	<div class="bundleContainer">
		<div class="item-list"></div>
        <?php if (!$vb_read_only): ?>
            <button type="button" class="btn bth-primary labelInfo add">
                <span class="glyphicon glyphicon-plus"></span>
                <?php print $vs_add_label ?: _t("Add caption file"); ?>
            </button>
        <?php endif; ?>
	</div>
</div>
<script>
    (function ($) {
        'use strict';

        $(function() {
            caUI.initRelationBundle('#<?php print $vs_id_prefix; ?>', {
                fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
                templateValues: ['locale_id', 'locale', 'caption_id', 'filesize'],
                initialValues: <?php print json_encode($va_initial_values); ?>,
                initialValueOrder: <?php print json_encode(array_keys($va_initial_values)); ?>,
                itemID: '<?php print $vs_id_prefix; ?>Item_',
                initialValueTemplateClassName: 'representation',
                templateClassName: 'representiation-new-item-template',
                itemListClassName: 'item-list',
                addButtonClassName: 'add',
                deleteButtonClassName: 'remove',
                showEmptyFormsOnLoad: 0,
                readonly: <?php print $vb_read_only ?>
            });
        });
    }(jQuery));
</script>
