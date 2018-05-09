<?php
	
	class block_career extends block_base
	{
		/**
		 *
		 */
		public function init()
		{
			$this->title = get_string('title_plugin', 'block_career');
		}
		
		/**
		 * @return bool
		 */
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
		
		}
		
		/**
		 * @return stdClass
		 */
		public function get_content()
		{
			global $CFG;
			global $COURSE;
			global $DB;
			
			if ($this->content !== null) {
				return $this->content;
			}
			if (empty($this->config)) {
				$this->config = new stdClass();
			}
			
			$request = $DB->get_records_sql('SELECT * FROM {block_career} WHERE course = ?', array($COURSE->id));
			$careerId = optional_param("career", NULL, PARAM_INT);
			$active = "";
			
			$this->content = new stdClass;
			$this->content->text .= '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id . '"  class="btn  btn-lg btn-block btn_orange">Accueil du cours</a><br>';
			
			$image = "";
			
			$this->content->text .= '<div class="list-group">';
			
			foreach ($request as $value) {
				
				if (file_get_contents("$CFG->wwwroot/blocks/career/$value->image") != null) {
					$image = "<img src='$CFG->wwwroot/blocks/career/$value->image' class='img_moodle_list'/>";
				}
				
				if ($careerId != null && $careerId == $value->id) {
					$active = "active";
				} else {
					$active = "";
				}
				
				$this->content->text .= "<a href='" . $CFG->wwwroot . "/blocks/career/career_unit.php?career=" . $value->id . "' class='full list-group-item list-group-item-action $active'><div class=' left img_center'>$image</div>
                 &nbsp&nbsp $value->name</a><br>";
			}
			
			$this->content->text .= '</div>';
			
			if (empty($request)) {
				$this->content->text .= "<h3>" . get_string('any_carrer', 'block_career') . "</h3>";
			}
			
			$this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/career/career_list.php?course=' . $COURSE->id . '" type="button " class="btn  btn-lg btn-block btn_blue">Gérer les parcours</a>';
			
			$this->content->text .= "<p></p>";
			
			return $this->content;
		}
	}

?>