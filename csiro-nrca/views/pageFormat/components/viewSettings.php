<?php
$va_preferences = [
    'form-spacing' => [
        'label' => _t('Form Spacing'),
        'default' => 'normal',
        'values' => [
            'collapsed' => _t('Collapsed'),
            'compact' => _t('Compact'),
            'normal' => _t('Normal'),
            'sparse' => _t('Sparse'),
            'expanded' => _t('Expanded'),
        ]
    ],
    'field-padding' => [
        'label' => _t('Field Padding'),
        'default' => 'normal',
        'values' => [
            'collapsed' => _t('Collapsed'),
            'compact' => _t('Compact'),
            'normal' => _t('Normal'),
            'sparse' => _t('Sparse'),
            'expanded' => _t('Expanded'),
        ]
    ],
    'font-size' => [
        'label' => _t('Font Size'),
        'default' => 'medium',
        'values' => [
            'tiny' => _t('Tiny'),
            'small' => _t('Small'),
            'medium' => _t('Medium'),
            'large' => _t('Large'),
            'huge' => _t('Huge'),
        ]
    ]
];

$va_toggles = [
    'sidebar-collapsed' => _t('Sidebar Collapsed'),
    'display-keyboard-shortcuts' => _t('Display Keyboard Shortcuts'),
    'linear-forms' => _t('Linear Forms'),
];

$va_preference_keys = [];
$va_default_preferences = [];
foreach ($va_preferences as $vs_key => $va_preference){
    $va_default_preferences[] = ['name' => $vs_key, 'value' => $va_preference['default']];
    foreach ($va_preference['values'] as $vs_value => $vs_description){
        $va_preference_keys[] = $vs_key . '-' . $vs_value;
    }
}
$vs_default_preferences = json_encode($va_default_preferences, JSON_OBJECT_AS_ARRAY);
$vs_all_classes = join(' ', array_merge($va_preference_keys, array_keys( $va_toggles)));
?>

<a href="#" data-toggle="modal" data-target="#viewSettings">
    <span class="glyphicon glyphicon-dashboard"></span>
    View Settings
</a>

<div class="modal fade text-left" id="viewSettings" tabindex="-1" role="dialog"
     aria-labelledby="viewSettingsLabel">
    <form id="viewSettingsForm">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="viewSettingsLabel">View Settings</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php foreach ($va_preferences as $vs_group => $va_preference): ?>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <?php print $va_preference['label'] ?>
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="btn-group-vertical" style="width:100%" data-toggle="buttons">
                                            <?php foreach ($va_preference['values'] as $vs_value => $vs_title): ?>
                                                <label class="btn btn-default">
                                                    <input type="radio" name="<?php print $vs_group; ?>" value="<?php print $vs_value; ?>" autocomplete="off" <?php if ($va_preference['default'] === $vs_value) {print 'checked="checked"'; } ?>/>
                                                    <?php print $vs_title; ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php print _t('Other Preferences'); ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <?php foreach ($va_toggles as $vs_name => $vs_title): ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="on" name="<?php print $vs_name; ?>">
                                        <?php print $vs_title; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function ($) {
        'use strict';

        function applyPreferences (preferences) {
            if (preferences){
                var $body = $(document.body);
                $body.removeClass('<?php print $vs_all_classes; ?>');
                $.each(preferences, function (i) {
                    var suffix = preferences[i].value !== 'on' ? '-'  + preferences[i].value : '';
                    $body.addClass(preferences[i].name + suffix);
                });
            }
        }

        function savePreferences (preferences) {
            window.localStorage.setItem('userPreferences', JSON.stringify(preferences));
            applyPreferences(preferences);
        }

        function loadPreferences () {
            return JSON.parse(window.localStorage.getItem('userPreferences') || '<?php print $vs_default_preferences; ?>');
        }

        $(function () {
            var $form = $('#viewSettingsForm');
            $form.find(':input').on('change', function () {
                $(document.body).addClass('animate');
                savePreferences($form.serializeArray());
            });
            $.each(loadPreferences(), function (i, preference) {
                var $elem = $form.find(':input[name="' + preference.name + '"][value="' + preference.value + '"]');
                $elem.prop('checked', 'checked');
                $elem.parent('label').addClass('active');
            });
        });

        // Add classes to the `body` per saved preferences.  This is fine to do here, before the DOM ready event.
        applyPreferences(loadPreferences());
    }(jQuery));
</script>
