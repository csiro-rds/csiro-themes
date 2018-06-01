<?php
$vs_widget_id = $this->getVar('widget_id');
$vn_jobs_done = $this->getVar('jobs_done');
$va_jobs_done = $this->getVar('jobs_done_data');
$vn_jobs_queued_processing = $this->getVar('jobs_queued_processing');
$va_jobs_queued = $this->getVar('qd_job_data');
$va_jobs_processing = $this->getVar('pr_job_data');

$va_job_categories = array(
    'running' => array(
        'jobs' => $va_jobs_processing,
        'heading' => _t("Jobs currently being processed"),
        'completed' => false
    ),
    'queued' => array(
        'jobs' => $va_jobs_queued,
        'heading' => _t("Jobs queued for later processing"),
        'completed' => false
    ),
    'completed' => array(
        'jobs' => $va_jobs_done,
        'heading' => _t("Jobs completed in the last %1 hours", $this->getVar('hours')),
        'completed' => true
    )
);
?>
<div class="widget widget-track-processing" id="widget_<?php print $vs_widget_id; ?>">
    <div class="pull-right" id="widget_last_update_display_<?php print $vs_widget_id; ?>">
        <?php print _t('Updated at %1', date('H:i')); ?>
    </div>
    <?php if ((sizeof($va_jobs_processing) > 0) || (sizeof($va_jobs_queued) > 0) || (sizeof($va_jobs_done) > 0)): ?>
        <h3>Status</h3>
        <ul>
            <?php if (sizeof($va_jobs_processing) > 0): ?>
                <li>
                    <a href="#running_<?php print $vs_widget_id; ?>">
                        <?php print _t("%1 running", sizeof($va_jobs_processing)); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (sizeof($va_jobs_queued) > 0): ?>
                <li>
                    <a href="#queued_<?php print $vs_widget_id; ?>">
                        <?php print _t("%1 queued", sizeof($va_jobs_queued)); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (sizeof($va_jobs_done) > 0): ?>
                <li>
                    <a href="#completed_<?php print $vs_widget_id; ?>">
                        <?php print _t("%1 completed", $vn_jobs_done); ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <?php foreach ($va_job_categories as $vs_category_key => $va_category_info): ?>
            <?php if (sizeof($va_category_info['jobs']) > 0): ?>
                <div id="<?php print $vs_category_key?>_<?php print $vs_widget_id; ?>">
                    <h3><?php print $va_category_info['heading']; ?></h3>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><?php print _t('Handler'); ?></th>
                            <th><?php print _t('Created'); ?></th>
                            <th><?php print _t('By'); ?></th>
                            <?php if ($va_category_info['completed']): ?>
                                <th><?php print _t('Completed'); ?></th>
                                <th><?php print _t('Errors'); ?></th>
                            <?php endif; ?>
                            <th><?php print _t('Status'); ?></th>
                            <?php if ($va_category_info['completed']): ?>
                                <th><?php print _t('Processing Time'); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($va_category_info['jobs'] as $va_job): ?>
                            <tr>
                                <td>
                                    <?php print unicode_strtolower($va_job['handler_name']); ?>
                                </td>
                                <td>
                                    <?php print date("n/d/Y @ g:i:sa T", $va_job["created"]); ?>
                                </td>
                                <td>
                                    <?php print $va_job['by']; ?>
                                </td>
                                <?php if ($va_category_info['completed']): ?>
                                    <td>
                                        <?php print date("n/d/Y @ g:i:sa T", $va_job["completed_on"]); ?>
                                    </td>
                                    <td>
                                        <div class="text-danger">
                                            <?php print $va_job["error_message"]; ?>
                                            [<?php print $va_job["error_code"]; ?>]
                                            <em><?php print _t('TASK DID NOT COMPLETE'); ?></em>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php foreach ($va_job['status'] as $vs_code => $va_info): ?>
                                        <div>
                                            <strong><?php print $va_info['label']; ?></strong>:
                                            <?php if ($va_category_info['completed'] && $vs_code === 'table'): ?>
                                                <?php
                                                $va_tmp = explode(':', $va_job['status']['table']['value']);
                                                $vs_url = caEditorUrl($this->request, $va_tmp[0], $va_tmp[2], array(), array(), array('verifyLink' => true));
                                                ?>
                                                <?php if ($vs_url): ?>
                                                    <a href="<?php print $vs_url; ?>">
                                                        <?php print $va_info['value']; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php print $va_info['value']; ?>
                                                    [<em> <?php print _t('DELETED'); ?></em>]
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php print $va_info['value']; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <?php if ($va_category_info['completed']): ?>
                                    <td>
                                        <strong><?php print _t("Total processing time"); ?></strong>:
                                        <?php print $va_job['processing_time']; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?php print _t("There are no running jobs, queued jobs or jobs completed in the last %1 hours.", $this->getVar('hours')); ?>
    <?php endif; ?>
</div>
<script>
    (function ($) {
        'use strict';

        $(function () {
            $('#tabContainer_<?php print $vs_widget_id; ?>').tabs();

            <?php if (!$this->request->isAjax()): ?>
                setInterval(function() {
                    $('#widget_<?php print $vs_widget_id; ?>').load('<?php print caNavUrl($this->request, '', 'Dashboard', 'getWidget', array('widget_id' => $vs_widget_id));?>');
                }, <?php print ($this->getVar('update_frequency') * 1000); ?>);
            <?php endif; ?>
        });
    }(jQuery));
</script>
