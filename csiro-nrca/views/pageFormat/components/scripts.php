<?php
$config = Configuration::load('keyboard.conf');
/* TODO FIXME Use resource bundles */
?>
<script src="<?php echo $this->request->getUrlPathForThemeFile('js/bootstrap.min.js'); ?>"></script>
<script>
    (function ($) {
        'use strict';

        var KEYBOARD_SHORTCUT_ACTIONS = {
            save_record: function () {
                $('section#main-content article form[id$="EditorForm"]').submit();
                return false;
            },
            previous_screen: function () {
                var $previous = $('section#main-content ul.nav.nav-tabs li.active').prev().find('a');
                if ($previous.length > 0) {
                    $previous[0].click();
                }
                return false;
            },
            next_screen: function () {
                var $next = $('section#main-content ul.nav.nav-tabs li.active').next().find('a');
                if ($next.length > 0) {
                    $next[0].click();
                }
                return false;
            },
            previous_record: function () {
                var $previous = $('section#sidebar aside .component-editor-info a.prev.record');
                if ($previous.length > 0) {
                    $previous[0].click();
                }
                return false;
            },
            next_record: function () {
                var $next = $('section#sidebar aside .component-editor-info a.next.record');
                if ($next.length > 0) {
                    $next[0].click();
                }
                return false;
            },
            results_list: function () {
                var $results = $('section#sidebar aside .component-editor-info a.results-list');
                if ($results.length > 0) {
                    $results[0].click();
                }
                return false;
            },
            quick_search: function () {
                $('#caQuickSearchFormText').focus();
                return false;
            }
        };

        $(function() {
            $('#caQuickSearchFormText').searchlight(
                '<?php print caNavUrl($this->request, 'find', 'SearchObjects', 'lookup'); ?>',
                {
                    showIcons: false,
                    searchDelay: 100,
                    minimumCharacters: 3,
                    limitPerCategory: 3
                }
            );

            $('ul.sf-menu').superfish(
                {
                    delay: 350,
                    speed: 150,
                    disableHI: true,
                    animation: { opacity: 'show' }
                }
            );

            $('#caSideNavMoreToggle').toggle(($('#leftNav').height() > 0) && ($('#leftNav').height() - $('#widgets').height()) < ($('#leftNav #leftNavSidebar').height() + 50));

            <?php foreach ($config->get('active_shortcuts') as $vs_shortcut => $vs_action_name): ?>
                $('*').on('keydown.<?php print $vs_shortcut; ?>', function (evt) {
                    var f = KEYBOARD_SHORTCUT_ACTIONS.<?php print $vs_action_name; ?>;
                    return (typeof f === 'function') ? f(evt) : true;
                });
            <?php endforeach; ?>

            // Remove targets within editor form (the "add" and "remove" buttons by default) from tab sequence.
            $('section#main-content article form[id$="EditorForm"]').find('<?php print join(',', ($config->get('tab_index_skip') ?: [])); ?>').attr('tabindex', -1);
        });
    }(jQuery));
</script>
<?php print TooltipManager::getLoadHTML(); ?>
<?php print FooterManager::getLoadHTML(); ?>
