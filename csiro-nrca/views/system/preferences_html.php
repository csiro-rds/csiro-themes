<?php
$t_user = $this->getVar('t_user');
$vs_group = $this->getVar('group');
$va_group_info = $t_user->getPreferenceGroupInfo($vs_group);
$va_prefs = $t_user->getValidPreferences($vs_group);
$this->setVar('cancel_url', caNavUrl($this->request, 'system', 'Preferences', $this->request->getAction()));
?>
<div id="page-preferences">
    <?php print caFormTag($this->request, 'Save', 'PreferencesForm'); ?>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/common/basic_controls_html.php')); ?>
        <h1><?php print _t("Preferences: %1", _t($va_group_info['name'])); ?></h1>
        <?php foreach ($va_prefs as $vs_pref): ?>
            <div class="form-group">
                <?php print $t_user->preferenceHtmlFormElement($vs_pref, null, array()); ?>
            </div>
        <?php endforeach; ?>
        <input type="hidden" name="action" value="<?php print $this->request->getAction(); ?>"/>
    </form>
</div>
