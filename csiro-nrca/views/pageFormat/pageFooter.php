                        <?php /* TODO FIXME These elements continue from pageHeader */ ?>
                    </article>
                </div>
            </section>
        </div>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/panels.php')); ?>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/scripts.php')); ?>
        <?php print $this->render($this->request->getDirectoryPathForThemeFile('views/pageFormat/components/instrumentation.php')); ?>
    </body>
</html>
