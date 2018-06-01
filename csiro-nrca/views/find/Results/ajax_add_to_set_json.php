<?php
if ($vs_error = $this->getVar('error')) {
    print json_encode(array(
        'status' => 'error',
        'error' => $vs_error
    ));
} else {
    print json_encode(array(
        'status' => 'ok',
        'set_id' => $this->getVar('set_id'),
        'set_name' => $this->getVar('set_name'),
        'num_items_added' => $this->getVar('num_items_added'),
        'num_items_already_in_set' => $this->getVar('num_items_already_in_set')
    ));
}
