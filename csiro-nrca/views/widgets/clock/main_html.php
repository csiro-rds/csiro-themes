<div class="widget widget-clock">
    <div id="ca-clock-<?php print $this->getVar('widget_id'); ?>"></div>
</div>

<script src="<?php print __CA_URL_ROOT__; ?>/app/widgets/clock/epiclock/javascript/jquery.dateformat.js"></script>
<script src="<?php print __CA_URL_ROOT__; ?>/app/widgets/clock/epiclock/javascript/jquery.epiclock.js"></script>
<script src="<?php print __CA_URL_ROOT__; ?>/app/widgets/clock/epiclock/renderers/retro/epiclock.retro.js"></script>
<script src="<?php print __CA_URL_ROOT__; ?>/app/widgets/clock/epiclock/renderers/retro-countdown/epiclock.retro-countdown.js"></script>
<script>
    (function ($) {
        'use strict';

        $('#ca-clock-<?php print $this->getVar('widget_id'); ?>').epiclock({
            format: '<?php print $this->getVar('settings')['display_format']; ?>',
            renderer: '<?php print $va_settings['display_mode']; ?>'
        });
    }(jQuery));
</script>
