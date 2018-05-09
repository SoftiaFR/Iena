<?php
if (!defined('MOODLE_INTERNAL'))
    die('Direct access to this script is forbidden.');

class block_mahara_iena_edit_form extends block_edit_form {
 
	function specific_definition($mform) {
 
		$mform->addElement('header','configheader', get_string('blocksettings', 'block'));
    }
}
?>


