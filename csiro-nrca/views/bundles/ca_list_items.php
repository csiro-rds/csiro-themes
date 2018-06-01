<?php
$this->setVar('createEditorPanel', true);
if((bool)$va_settings['restrictToTermsRelatedToCollection']) {
    $this->setVar('createQuickAddPanel', false);
    $this->setVar('createRelationshipBundle', false);
    $this->setVar('createCheckListBundle', true);
} else {
    //relationship bundle variable
    $this->setVar('templateValues', array('label', 'type_id', 'id'));
    $this->setVar('autocompleteController', 'Vocabulary');
}
print $this->render($this->request->getDirectoryPathForThemeFile('views/bundles/common/relationships.php'));
