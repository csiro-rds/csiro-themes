<?php if (!$this->request->isAjax()): ?>
    <?php $vs_form_list_select = $this->getVar('t_form')->getFormsAsHTMLSelect('form_id', array('onchange' => 'caLoadAdvancedSearchForm(this.options[this.selectedIndex].value)', 'class' => 'form-control'), array('value' => $this->getVar('form_id'), 'access' => __CA_SEARCH_FORM_READ_ACCESS__, 'user_id' => $this->request->getUserID(), 'table' => $this->getVar('t_subject')->tableNum(), 'restrictToTypes' => [intval($this->getVar('type_id'))])); ?>

    <div class="top-right-fixed">
        <div class="input-group">
            <?php if ($vs_form_list_select): ?>
                <div id="advancedSearchFormContainerFormSelector">
                    <?php print $vs_form_list_select; ?>
                </div>
            <?php endif; ?>
            <div class="input-group-btn">
                <button type="button" class="btn btn-default" id="advancedSearchFormContainerToggle">
                    <span class="glyphicon glyphicon-eye-close"></span>
                    <span class="text"><?php print _t('Hide search form'); ?></span>
                </button>
            </div>
        </div>
    </div>

    <div id="advancedSearchFormContainer">
        <?php // Initial form can be replaced by AJAX loading in a different form ?>
        <?php print $this->render('Search/search_advanced_form_html.php'); ?>
    </div>

    <script>
        function caLoadAdvancedSearchForm(form_id) {
            jQuery('#advancedSearchFormContainer').load('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getAdvancedSearchForm'); ?>', {form_id: form_id});
        }

        (function ($) {
            'use strict';

            var caCookieJar, cookieName;

            caCookieJar = $.cookieJar('caCookieJar');
            cookieName = '<?php print $this->getVar('table_name'); ?>_hide_adv_search_form';

            function updateSearchFormToggle (visible) {
                var $toggle = $('#advancedSearchFormContainerToggle');
                $toggle.find('span.text').text(visible ? '<?php print _t('Hide search form'); ?>' : '<?php print _t('Show search form'); ?>');
                $toggle.find('span.glyphicon')
                    [visible ? 'removeClass' : 'addClass']('glyphicon-eye-open')
                    [visible ? 'addClass' : 'removeClass']('glyphicon-eye-close');
            }

            $(function () {
                if (caCookieJar.get(cookieName) === '1') {
                    $("#advancedSearchFormContainer").hide();
                    updateSearchFormToggle(false);
                }

                $("#advancedSearchFormContainerToggle").click(function() {
                    $("#advancedSearchFormContainer").slideToggle(350, function() {
                        var visible = this.style.display === 'block';
                        caCookieJar.set(cookieName, visible ? '0' : '1');
                        updateSearchFormToggle(visible);
                    });
                    return false;
                });
            });
        }(jQuery));
    </script>
<?php endif; ?>

<script>
    (function ($) {
        'use strict';

        // Show "add to set" controls if set tools is open
        $(function() {
            if ($("#searchSetTools").is(":visible")) {
                $(".addItemToSetControl").show();
            }
        });
    }(jQuery));
</script>
