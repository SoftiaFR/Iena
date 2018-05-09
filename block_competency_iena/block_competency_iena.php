<?php
	
	require_once('entity/block_competency_iena_competency.php');
	
	class block_competency_iena extends block_base
	{
		public function init()
		{
			$this->title = get_string('title_plugin', 'block_competency_iena');
		}
		
		function instance_allow_multiple()
		{
			return true;
		}
		
		/**
		 * Set the applicable formats for this block to all
		 * @return array
		 */
		function applicable_formats()
		{
			return array('all' => true);
		}
		
		/**
		 * Allow the user to configure a block instance
		 * @return bool Returns true
		 */
		function instance_allow_config()
		{
			return true;
		}
		
		function has_config()
		{
			return true;
		}
		
		public function get_content()
		{
			global $CFG;
			global $COURSE;
			global $USER;
			
			if ($this->content !== null) {
				return $this->content;
			}
			if (empty($this->config)) {
				$this->config = new stdClass();
			}
			
			$this->content = new stdClass;
			$this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/competency_iena/competency_iena_competencies.php?courseid=' . $COURSE->id . '"  class="btn  btn-lg btn-block btn_blue">Mes Compétences</a><br>';
			if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
				$this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/competency_iena/competency_iena_competencies_mgmt.php?courseid=' . $COURSE->id . '" type="button " class="btn  btn-lg btn-block btn_blue">Gérer les compétences</a>';
			}
			$competenceI = new block_competency_iena_competency();
			$competences = $competenceI->get_competencies_by_userID($USER->id);
			$nb_ok = 0;
			$nb_total = count($competences);
			foreach ($competences as $comp) {
				if ($comp->student_proficiency == 1) {
					$nb_ok++;
				}
			}
			$this->content->text .= "<p></p>";
			$this->content->text .= "
            <div class=\"thermo\">
               <div class=\"round_thermo\">
                  <span class=\"text_round_thermo\">
                     $nb_ok/$nb_total
                 </span>
               </div>
               <div class=\"thermo_bar\">
                 <progress class=\"progress\" max=\"$nb_total\" value=\"$nb_ok\"></progress>
               </div>
           
           
           </div>
            ";
			
			return $this->content;
		}
	}

?>