<?php
	
	// Michaël Lebeau
	
	class view_competency_iena_competency_mgmt extends moodleform
	{
        public function definition()
        {
            // TODO: Implement definition() method.
            global $CFG;
            $mform = $this->_form; // Don't forget the underscore!
        }
		
		public function get_content($courseid, $ref)
		{
			global $COURSE, $CFG, $USER;
            $mform = $this->_form;
			$competency_instance = new block_competency_iena_competency();
			$competencies = $competency_instance->get_competencies_by_courseID($courseid);  // ($COURSE->id);
			
			$module_instance = new block_competency_iena_module();
			$modules = $module_instance->get_modules_by_courseID($courseid);
			
			$section_instance = new block_competency_iena_section();
			$sections = $section_instance->get_sections_by_id_course($courseid);
			
			$link = $CFG->wwwroot . '/blocks/competency_iena/competency_iena_competency_mgmt.php?courseid=' . $COURSE->id;
			$content = false;
			
			$content .= "
			
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js\"></script>
			
			<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css\">
			
			
			
			<form action='" . $CFG->wwwroot . "/blocks/competency_iena/competency_iena_competency_mgmt.php?courseid=" . $COURSE->id . "' method='POST'>
				<h1>".get_string('ask_demande_modify_iena', 'block_competency_iena')."</h1>
					<div class=\"well well-lg\">
                                                <h3>".get_string('apc_iena', 'block_competency_iena')."</h3>
                            
                                                <p>$CFG->apc_iena</p>
                                        </div>
			
                                        <div class=\"well well-lg\">
                                                <h3>".get_string('info_iena', 'block_competency_iena')."</h3>
                            
                                                <p>$CFG->info_iena</p>
                                        </div>
                                        <input type='hidden' value='" . $ref[1]->id . "' id='ref_mod_valid' name='ref_mod_valid'>
                                        <p style='font-style:italic'>".get_string('ref_iena', 'block_competency_iena')."" . $ref[1]->shortname . " </p>
                                        <!--<textarea name=\"textarea_message\" rows=\"5\" cols=\"100\"></textarea>-->
                                        ";
                                        $mform->addElement('editor', 'textarea_message', get_string('message_ask_iena', 'block_competency_iena'));
										$mform->setType('textarea_message', PARAM_RAW);
										$mform->addRule('textarea_message', get_string('error'), 'required', null, null, false, false);
										$temp = $mform->toHtml();
										//Supprime le <form>
										$temp = substr($temp,(strpos($temp,'>')+1));
										$temp = substr($temp,0, -7);

										$content .= $temp;
										$content .= "
             									<p style='margin-top: 4rem;'>".get_string('ask_acces_mod_iena', 'block_competency_iena')."</p>
												<div class='align_center'>
                                                <a onclick=\"window.history.go(-1); return false;\"   class='btn btn-secondary' >".get_string('cancel', 'block_competency_iena')."</a>
                                                <button type='submit'  id='" . $ref[1]->id . "' class='btn btn-success' style='margin-left: 2rem '>".get_string('yes', 'block_competency_iena')."</button>
                                        </div>
			
			</form>";
			
			return $content;
			
			
		}
	}

?>

