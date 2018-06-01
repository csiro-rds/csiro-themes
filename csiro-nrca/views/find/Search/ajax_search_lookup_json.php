<?php
$o_dm = new Datamodel();
$va_data = array();

foreach ($this->getVar('matches') as $vs_table => $va_type_groups) {
    $t_instance = $o_dm->getInstanceByTableName($vs_table);
    foreach ($va_type_groups as $vs_type => $va_matches) {
        $va_numeric_matches = array_filter($va_matches, 'is_numeric', ARRAY_FILTER_USE_KEY);

        $va_data[] = array(
            'title' => $vs_type,
            'results' => array_map(
                function ($vn_id, $va_match) use ($t_instance) {
                    return array(
                        caNavUrl(
                            $this->request,
                            'editor/' . strtolower($t_instance->getProperty('NAME_SINGULAR')),
                            preg_replace('/\s+/', '', ucwords($t_instance->getProperty('NAME_SINGULAR'))) . 'Editor',
                            'Edit',
                            array( $t_instance->primaryKey() => $vn_id)
                        ),
                        $va_match['label'],
                        ''
                    );
                },
                array_keys($va_numeric_matches),
                $va_numeric_matches
            )
        );
    }
}

print json_encode($va_data);
