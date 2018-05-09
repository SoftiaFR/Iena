<?php
	if (!defined('MOODLE_INTERNAL'))
		die('Direct access to this script is forbidden.');
	
	class block_career_edit_form extends block_edit_form
	{
		
		/**
		 * @param $mform
		 */
		function specific_definition($mform)
		{
			// Adding an element to the form
			$mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
		}
	}

?>


