<?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/initialise.php')); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/meta.php')); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <section id="sidebar">
            <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/header.php')); ?>
            <?php if ($this->request->isLoggedIn()): ?>
                <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/controls.php')); ?>
                <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/navigation.php')); ?>
            <?php endif; ?>
            <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/footer.php')); ?>
        </section>
        <section id="main-content">
            <div class="full sidebar-collapse">
                <button class="btn btn-sm" id="sidebarCollapse" >
                    <i class="glyphicon glyphicon-menu-hamburger" title="Toggle Sidebar"></i>
                </button>
            </div>
            <div class="full">
                <script>
                    (function ($) {
                        "use strict";

                        $('#sidebarCollapse').on('click', function () {
                            $('[name=sidebar-collapsed]').click();
                        });

                    }(jQuery));
                </script>

                <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/tabs.php')); ?>
                <article class="<?php print (is_array($this->getVar('nav')->getNavInfo(2)) ? 'tabs' : 'no-tabs'); ?>">
                    <?php /* TODO FIXME These elements continue to pageFooter */ ?>
