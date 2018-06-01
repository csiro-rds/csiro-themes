<?php
$va_table_lookup = array(
    'ca_objects' => array(
        'label' => _t('Object Searches'),
        'basic_search' => 'find/SearchObjects',
        'advanced_search' => 'find/SearchObjectsAdvanced'
    ),
    'ca_entities' => array(
        'label' => _t('Entity Searches'),
        'basic_search' => 'find/SearchEntities',
        'advanced_search' => 'find/SearchEntitiesAdvanced'
    ),
    'ca_places' => array(
        'label' => _t('Place Searches'),
        'basic_search' => 'find/SearchPlaces',
        'advanced_search' => 'find/SearchPlacesAdvanced'
    ),
    'ca_object_lots' => array(
        'label' => _t('Object Lot Searches'),
        'basic_search' => 'find/SearchObjectLots',
        'advanced_search' => 'find/SearchObjectLotsAdvanced'
    ),
    'ca_storage_locations' => array(
        'label' => _t('Storage Location Searches'),
        'basic_search' => 'find/SearchStorageLocations',
        'advanced_search' => 'find/SearchStorageLocationsAdvanced'
    ),
    'ca_collections' => array(
        'label' => _t('Collection Searches'),
        'basic_search' => 'find/SearchCollections',
        'advanced_search' => 'find/SearchCollectionsAdvanced'
    ),
    'ca_occurrences' => array(
        'label' => _t('Occurrence Searches'),
        'basic_search' => 'find/SearchOccurrences',
        'advanced_search' => 'find/SearchOccurrencesAdvanced'
    )
);
?>
<div class="widget widget-saved-searches">
    <?php if (sizeof($this->getVar('saved_searches')) > 0): ?>
        <?php foreach ($this->getVar('saved_searches') as $vs_table => $va_searches): ?>
            <?php if ((sizeof($va_searches['advanced_search']) > 0) || (sizeof($va_searches['basic_search']) > 0)): ?>
                <h3><?php print $va_table_lookup[$vs_table]['label']; ?></h3>
                <?php foreach ($va_searches as $vs_search_type => $va_search_info): ?>
                    <div class="row">
                        <?php if (sizeof($va_search_info) > 0): ?>
                            <div class="col-md-6">
                                <?php print caFormTag($this->getVar('request'), 'doSavedSearch', 'caSavedSearchesForm'.$vs_table.$vs_search_type, $va_table_lookup[$vs_table][$vs_search_type], 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <?php print _t(ucwords(preg_replace('/_search$/', '', $vs_search_type))); ?>
                                        </span>
                                        <select name="saved_search_key" class="form-control" title="Select saved search">
                                            <?php foreach (array_reverse($va_search_info) as $vs_key => $va_search): ?>
                                                <option value="<?php print htmlspecialchars($vs_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php print strip_tags($va_search['_label']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default">
                                                <span class="glyphicon glyphicon-search"></span>
                                            </button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?php print _t('No saved searches to display.'); ?>
    <?php endif; ?>
</div>
