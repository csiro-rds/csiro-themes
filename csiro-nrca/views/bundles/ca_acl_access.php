<?php
$t_subject = $this->getVar('t_subject');
$vb_can_edit = $t_subject->isSaveable($this->request);
$vb_can_delete = $t_subject->isDeletable($this->request);
$va_cancel_parameters = array( $t_subject->primaryKey() => $t_subject->getPrimaryKey() );
$va_global_access_status = $t_subject->getACLWorldAccess(array('returnAsInitialValuesForBundle' => true))['access_display'];
$va_group_access = $t_subject->getACLUserGroups(array('returnAsInitialValuesForBundle' => true));
$va_user_access = $t_subject->getACLUsers(array('returnAsInitialValuesForBundle' => true));

$this->setVar('is_collapsible', false);
$this->setVar('cancel_url', caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Access/'.$this->request->getActionExtra(), $va_cancel_parameters));
?>
<div id="page-acl-access">
    <h1>Access</h1>
    <?php print caFormTag($this->request, 'SetAccess', 'caAccessControlList'); ?>
        <input type="hidden" name="<?php print $t_subject->primaryKey(); ?>" value="<?php $t_subject->getPrimaryKey(); ?>" />

        <?php if ($vb_can_edit): ?>
            <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/common/basic_controls_html.php')); ?>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php print _t('Global access'); ?></h2>
            </div>
            <div class="panel-body">
                <div id="edit-global-access-button" class="pull-right">
                    <a href="#" onclick="jQuery('#edit-global-access').show(250); jQuery('#edit-global-access-button').hide(); return false;" class="btn btn-warning">
                        <span class="glyphicon glyphicon-edit"></span>
                        <?php print _t('Edit Global Access'); ?>
                    </a>
                </div>
                <p><?php print _t('All groups and users %1 this record, unless you create an exception.', "<strong>$va_global_access_status</strong>"); ?></p>
                <div id="edit-global-access">
                    <?php print $t_subject->getACLWorldHTMLFormBundle($this->request, 'caAccessControlList'); ?>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php print _t('Exceptions'); ?></h2>
            </div>
            <div class="panel-body">
                <div id="manage-exceptions-button" class="pull-right">
                    <a href="#" onclick="jQuery('#manage-exceptions').show(250); jQuery('#manage-exceptions-button').hide(); return false;" class="btn btn-warning">
                        <span class="glyphicon glyphicon-edit"></span>
                        <?php print (($t_subject->getACLUserGroups()) || ($t_subject->getACLUsers())) ? _t('Edit Exceptions') : _t('Create an Exception'); ?>
                    </a>
                </div>
                <p>
                <?php if (($t_subject->getACLUserGroups()) || ($t_subject->getACLUsers())): ?>
                    <?php print _t('The following groups and users have special access or restrictions for this record.'); ?>
                <?php else: ?>
                    <?php print _t('No access exceptions exist for this record.'); ?>
                <?php endif; ?>
                </p>
                <div id="manage-exceptions">
                    <h3><?php print _t('Group access'); ?></h3>
                    <?php print $t_subject->getACLGroupHTMLFormBundle($this->request, 'caAccessControlList'); ?>
                    <?php foreach($va_group_access as $va_group_access_item): ?>
                        <div>
                            <strong><?php print ucwords($va_group_access_item['name']); ?></strong> (group):
                            <?php print $va_group_access_item['access_display']; ?>
                        </div>
                    <?php endforeach; ?>

                    <h3><?php print _t('User access'); ?></h3>
                    <?php print $t_subject->getACLUserHTMLFormBundle($this->request, 'caAccessControlList'); ?>
                    <?php foreach($va_user_access as $va_user_access_item): ?>
                        <div>
                            <strong><?php print $va_user_access_item['lname']; ?>, <?php print $va_user_access_item['fname']; ?></strong>:
                            <?php print $va_user_access_item['access_display']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($t_subject->hasField('acl_inherit_from_ca_collections')): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><?php print _t('Inherit from related collection?'); ?></h2>
                </div>
                <div class="panel-body">
                    <?php print $t_subject->getBundleFormHTML('acl_inherit_from_ca_collections', '', array('forACLAccessScreen' => true), array('request' => $this->request)); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($t_subject->hasField('acl_inherit_from_parent')): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><?php print _t('Inherit from parent?'); ?></h2>
                </div>
                <div class="panel-body">
                    <?php print $t_subject->getBundleFormHTML('acl_inherit_from_parent', '', array('forACLAccessScreen' => true), array('request' => $this->request)); ?>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>
