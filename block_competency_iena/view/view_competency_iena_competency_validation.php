<?php
	
	// Michaël Lebeau
	
	class view_competency_iena_competency_validation
	{
		
		public function get_content($courseid)
		{
			global $COURSE, $CFG, $USER;
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
			<div class=\"alert alert-success\" role=\"alert\">
				<p>".get_string('send_demande','block_competency_iena')."</p>
			</div>
			<a href='" . $CFG->wwwroot . "/course/view.php?id=" . $COURSE->id . "' class='btn btn-primary'>".get_string('back_course','block_competency_iena')."</a>
			
			";
		
			
			
			return $content;
			
			
			
		}
	}

?>

