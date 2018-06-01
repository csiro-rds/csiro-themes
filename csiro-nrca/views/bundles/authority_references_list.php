<?php
AssetLoadManager::register('tabUI');
$vs_id_prefix = $this->getVar('placement_code').$this->getVar('id_prefix');
$vn_table_num = $this->getVar('table_num');
$t_instance = $this->getVar('t_instance');
$va_settings = $this->getVar('settings');
$va_errors = $this->getVar('errors');
$va_references = $this->getVar('references');
$va_search_strings = $this->getVar('search_strings');
$vn_max_items = caGetOption('maxReferencesToDisplay', $va_settings, 100);

$va_reference_info = array_map(function ($va_rows, $vn_table_num) use($t_instance, $va_settings)  {
    $t_ref_instance = $t_instance->getAppDatamodel()->getInstance($vn_table_num, true);
    $template = caGetOption("{$t_ref_instance->table_name}_displayTemplate", $va_settings, null);
    $table_name = $t_ref_instance->tableName();
    if(!$template) {
        $template_settings = $t_instance->getAppConfig()->getList("{$table_name}_lookup_settings");
        $template_delimiter = join($t_instance->getAppConfig()->get("{$table_name}_lookup_delimiter"), $template);
        if(is_array($template_settings)) {
            $template = $template_delimiter;
        } elseif(!($template_settings)) {
            $template = '^' . $table_name . '.preferred_labels';
        }
        $template = "^{$t_ref_instance->table_name}.preferred_labels";
    }

    return array(
        'table_name' => $table_name,
        'name_plural' => $t_ref_instance->getProperty('NAME_PLURAL'),
        'rows' => $va_rows,
        'template' => $template,
        'items' => caProcessTemplateForIDs("<li>" . $template . "</li>", $table_name, array_keys($va_rows), array('returnAsArray' => true))
    );
}, $va_references);
?>
<div id="<?php print $vs_id_prefix; ?>" class="component component-bundle component-bundle-authority-references-list">
    <div class="caItemList">
        <div class="labelInfo authorityReferenceList">
            <?php if (is_array($va_errors) && sizeof($va_errors)): ?>
                <?php foreach($va_errors as $error): ?>
                    <div class="alert alert-danger">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                        <?php print $error; ?>
                    </div>
                <?php endforeach ?>
            <?php elseif (sizeof($va_reference_info) > 0): ?>
                <div id="<?php print $vs_id_prefix; ?>AuthorityReferenceTabs" class="authorityReferenceListContainer" onload="activateTabs()">
                    <ul>
                        <?php foreach($va_reference_info as $reference): ?>
                            <li>
                                <a href="#<?php print $vs_id_prefix; ?>AuthorityReferenceTabs-<?php print $reference->table_name; ?>">
                                    <span><?php print _t('%1 (%2)', $reference->table_name, $reference->rows); ?></span>
                                </a>
                            </li>
                        <?php endforeach;?>
                    </ul>
                    <?php foreach($va_reference_info as $reference): ?>
                        <div id="<?php print $vs_id_prefix; ?>AuthorityReferenceTabs-<?php print $reference->table_name; ?>" class="authorityReferenceListTab">
                            <ul class='authorityReferenceList'>
                                <li>
                                    <?php print $reference->template; ?>
                                </li>
                                <?php foreach($reference->items as $index => $item): ?>
                                    <?php if ($index < $vn_max_items): ?>
                                        <li class='authorityReferenceList'>
                                            <?php print $item ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if ((sizeof($reference->items) >= $vn_max_items) && is_array($va_search_strings[$reference->table_name])): ?>
                                    <li>
                                        <?php print caSearchLink($this->request, _t('... and %1 more', sizeof($reference->items) - $vn_max_items),
                                            '', $reference->table_name, join(" OR ", $va_search_strings[$reference->table_name])) ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach ?>

                <?php else: ?>
                    <div>
                        <?php print _t('No references'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    var activateTabs;
    (function ($) {
        'use strict';

        $(function() {
            activateTabs = function() {
                $("#<?php print $vs_id_prefix; ?>AuthorityReferenceTabs").tabs({selected: 0});
            }
        });
    })(jQuery);
</script>
