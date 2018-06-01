<?php print json_encode(sizeof($this->getVar('errors')) ? array('status' => 'error', 'errors' => $this->getVar('errors')) : array('status' => 'ok', 'state' => $this->getVar('state')));
