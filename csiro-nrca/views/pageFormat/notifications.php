<div id="pageNotifications">
</div>

<script>
    var caUI = caUI || {};

    (function ($) {
        'use strict';

        // A single object literal for `notificationClassMap` is difficult to read with PHP constants as keys.
        var notificationClassMap = {};
        notificationClassMap['<?php print __NOTIFICATION_TYPE_ERROR__; ?>'] = {
            alert: 'alert-danger',
            icon: 'glyphicon-exclamation-sign'
        };
        notificationClassMap['<?php print __NOTIFICATION_TYPE_WARNING__; ?>'] = {
            alert: 'alert-warning',
            icon: 'glyphicon-warning-sign'
        };
        notificationClassMap['<?php print __NOTIFICATION_TYPE_INFO__; ?>'] = {
            alert: 'alert-info',
            icon: 'glyphicon-info-sign'
        };

        caUI.addNotification = function (notificationType, message, $target) {
            $target = $target || $('#pageNotifications');
            $target.append(
                $('<div>')
                    .addClass('alert alert-dismissable fade in')
                    .addClass(notificationClassMap[notificationType].alert)
                    .alert()
                    .append(
                        $('<span>')
                            .addClass('glyphicon')
                            .addClass(notificationClassMap[notificationType].icon))
                    .append($('<span>').html(message))
                    .append(
                        $('<button>')
                            .attr({
                                type: 'button',
                                'data-dismiss': 'alert'
                            })
                            .addClass('close')
                            .append($('<span>').html('&times;'))));
        };

        $(function () {
            var $notifications = $('#pageNotifications');
            <?php foreach (($this->getVar('notifications') ?: array()) as $va_notification): ?>
                $notifications.append(caUI.addNotification('<?php print $va_notification['type']; ?>', '<?php print preg_replace('/\s+/', ' ', addslashes($va_notification['message'])); ?>'));
            <?php endforeach; ?>
        });
    }(jQuery))
</script>
