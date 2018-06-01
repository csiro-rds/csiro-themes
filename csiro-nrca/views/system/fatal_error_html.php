<?php
AppController::getInstance()->removeAllPlugins();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>CollectiveAccess error</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="<?php print __CA_THEME_URL__; ?>/css/base.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <img src="<?php print __CA_THEME_URL__; ?>/graphics/logos/menu_logo.png" class="pull-right" />
                <h1 class="modal-title"><?php print _t('Fatal error'); ?></h1>
            </div>
            <div class="panel-body">
                <?php if ((defined('__CA_ENABLE_DEBUG_OUTPUT__') && __CA_ENABLE_DEBUG_OUTPUT__) || (defined('__CA_STACKTRACE_ON_EXCEPTION__') && __CA_STACKTRACE_ON_EXCEPTION__)): ?>
                    <div class="alert alert-danger">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                        <strong>Error</strong>
                        <?php print $ps_errstr; ?> in <?php print $ps_errfile; ?> line <?php print $pn_errline; ?>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th><?php print _t('Call'); ?></th>
                            <th><?php print _t('File'); ?></th>
                            <th><?php print _t('Line'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($pa_errcontext as $vn_i => $va_trace): ?>
                            <tr>
                                <td>
                                    <?php print $va_trace['class'] . $va_trace['type'] . $va_trace['function'] . '(' . join(', ', $pa_errcontext_args[$vn_i]) . ')'; ?>
                                </td>
                                <td>
                                    <a class="tracelistEntry" title="<?php print $va_trace['file']; ?>" ondblclick="var f = this.innerHTML; this.innerHTML = this.title; this.title = f;">
                                        <?php print pathinfo($va_trace['file'], PATHINFO_FILENAME); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php print $va_trace['line']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (is_array($pa_request_params) && (sizeof($pa_request_params) > 0)): ?>
                        <div id="requestParameters" class="alert alert-info">
                            <span class="glyphicon glyphicon-info-sign"></span>
                            <div class="errorDescription">
                                Request parameters:
                                <ol class="paramList">
                                    <?php foreach($pa_request_params as $vs_k => $vs_v): ?>
                                        <li>
                                            <?php print "$vs_k =&gt; $vs_v"; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="label label-danger">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                        <?php print _t("There was an uncaught fatal error. Please contact your system administrator and check the CollectiveAccess log files."); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
