<?php

    //require_once ('entity/block_competency_iena_competency.php');
	class block_mahara_iena extends block_base
	{
		public function init()
		{
			$this->title = get_string('title_plugin', 'block_mahara_iena');
		}

        function instance_allow_multiple() {
            return true;
        }

        /**
         * Set the applicable formats for this block to all
         * @return array
         */
        function applicable_formats() {
            return array('all' => true);
        }

        /**
         * Allow the user to configure a block instance
         * @return bool Returns true
         */
        function instance_allow_config() {
            return true;
        }

        function has_config() {
            return true;
        }

        public function get_content()
		{
            global $CFG;
            global $COURSE;
            global $USER;
            global $DB;
                    
			if ($this->content !== null)
			{
				return $this->content;
			}
			if (empty($this->config)) {
				$this->config = new stdClass();
			}

			$this->content =  new stdClass;
            $result = $DB->get_records_sql('SELECT * FROM {block_mahara_iena} WHERE course = ?', array($COURSE->id));
            if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
                if (count($result) == 0){
                $this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/mahara_iena/mahara_iena.php?courseid='
                . $COURSE->id . '"  class="btn  btn-lg btn-block btn_blue">'.get_string('create_group', 'block_mahara_iena').'</a><br>';}
            }
            $this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/mahara_iena/mahara_iena_link.php?courseid=' . $COURSE->id . '" type="button " class="btn  btn-lg btn-block btn_blue">'.get_string('link_grp_mahara', 'block_mahara_iena').'</a>';
            if (count($result) == 1){
                $this->content->text .= '<a  target="_blank" href="'.$CFG->base_mahara.'group/view.php?id='.reset($result)->code.'" class="btn  btn-lg btn-block btn_blue">'.get_string('acces_group', 'block_mahara_iena').'</a>';
            } else {
                $this->content->text .= '<button type="button" class="btn  btn-lg btn-block btn_blue" disabled >'.get_string('acces_group', 'block_mahara_iena').'</button>';
            }
			return $this->content;
		}
	}
?>
