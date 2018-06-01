<?php
$t_nav = $this->getVar('nav');
$vs_target_table = $this->request->config->get('one_table_search') ?: 'QuickSearch';
$vs_menu_bar = $t_nav->getHTMLMenuBar('menuBar', $this->request);
?>
<aside class="panel panel-default">
    <div class="panel-heading">
        Navigation
    </div>
    <div class="panel-body">
        <ul class="sf-menu">
            <?php /* TODO Replace the navigation rendering? */ ?>
            <?php print $vs_menu_bar; ?>
        </ul>
    </div>
    <div class="panel-footer">
        <?php if ($this->request->user->canDoAction('can_quicksearch')): ?>
            <?php print caFormTag($this->request, 'Index', 'caQuickSearchForm', 'find/'.$vs_target_table, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                <div class="input-group">
                    <input name="search" class="form-control" id="caQuickSearchFormText" placeholder="Quick search..." value="<?php print htmlspecialchars($this->request->session->getVar('quick_search_last_search'), ENT_QUOTES, 'UTF-8'); ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                </div>
            </form>
        <?php endif; ?>
    </div>
</aside>
