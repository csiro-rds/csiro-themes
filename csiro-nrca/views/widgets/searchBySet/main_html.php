<?php
$va_table_lookup = array(
    'ca_objects' => array(
        'label' => _t('Object Sets'),
        'sets_search' => 'find/SearchObjects'
    ),
    'ca_entities' => array(
        'label' => _t('Entity Sets'),
        'sets_search' => 'find/SearchEntities'
    ),
    'ca_places' => array(
        'label' => _t('Place Sets'),
        'sets_search' => 'find/SearchPlaces'
    ),
    'ca_object_lots' => array(
        'label' => _t('Object Lot Sets'),
        'sets_search' => 'find/SearchObjectLots'
    ),
    'ca_storage_locations' => array(
        'label' => _t('Storage Location Sets'),
        'sets_search' => 'find/SearchStorageLocations'
    ),
    'ca_collections' => array(
        'label' => _t('Collection Sets'),
        'sets_search' => 'find/SearchCollections'
    ),
    'ca_occurrences' => array(
        'label' => _t('Occurrence Sets'),
        'sets_search' => 'find/SearchOccurrences'
    )
);

$va_sets_by_table = array_filter(
    array_map(
        function ($va_sets) {
            return array_filter($va_sets, function ($va_set) {
                return sizeof($va_set) > 0;
            });
        },
        $this->getVar('sets_by_table')
    ),
    function ($va_sets) {
        return sizeof($va_sets) > 0;
    }
);
?>
<div class="widget widget-saved-searches">
    <?php if (sizeof($va_sets_by_table) > 0): ?>
        <?php foreach ($va_sets_by_table as $vs_table => $va_sets): ?>
            <?php if (sizeof($va_sets) > 0): ?>
                <h3><?php print $va_table_lookup[$vs_table]['label']; ?></h3>
                <?php foreach ($va_sets as $va_set_info): ?>
                    <div class="row">
                        <?php if (sizeof($va_set_info) > 0): ?>
                            <div class="col-md-6">
                                <?php print caFormTag($this->getVar('request'), 'doSavedSearch', 'caSavedSearchesForm'.$vs_table.'Sets', $va_table_lookup[$vs_table]['sets_search'], 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true)); ?>
                                    <div class="input-group">
                                        <select name="saved_search_key" class="form-control" title="Select saved search">
                                            <?php foreach ($va_set_info as $va_search): ?>
                                                <option value='set:"<?php print $va_set_info['set_code']; ?>"'>
                                                    <?php print strip_tags($va_search['name']); ?>
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
        <?php print _t('No saved set searches to display.'); ?>
    <?php endif; ?>
</div>
