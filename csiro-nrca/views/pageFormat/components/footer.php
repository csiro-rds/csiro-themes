<footer class="panel panel-default">
    <div class="panel-body text-muted text-center small">
        <div>
            <strong class="text-secondary">&copy; 2017 Whirl-i-Gig</strong>
        </div>
        <div>
            <a href="http://www.collectiveaccess.org" target="_blank">CollectiveAccess</a>
            <?php _p("is a trademark of"); ?>
            <a href="http://www.whirl-i-gig.com" target="_blank">Whirl-i-Gig</a>
        </div>
        <div>
            [<?php print $this->request->session->elapsedTime(4).'s'; ?>/<?php print caGetMemoryUsage(); ?>]
        </div>
        <?php if (Db::$monitor): ?>
            <div>
                [<a href="#" onclick="jQuery('#caApplicationMonitor').slideToggle(100); return false;">$</a>]
            </div>
        <?php endif; ?>
       <?php        
       $vs_full_app_version = Configuration::load(Configuration::load()->get('version_properties'));
       if(isset($vs_full_app_version)) {
           $build_number = $vs_full_app_version->get("build.number");
           echo "Version " . $build_number;
       }
       ?>
        
    </div>
</footer>
