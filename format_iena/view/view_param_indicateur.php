<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
	
	/**
	 *
	 * view_param_indicateur
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     Vrignaud Camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class view_param_indicateur
	{
		/**
		 * @return array
		 */
		public function get_course_sections_modules()
		{
			global $COURSE;
			$res_course = new course_format_iena_sections();
			$res_sections = new course_format_iena_section_ressources();
			
			// Loading the course object with its section objects and their respective module objects
			$sections = $res_course->get_sections_by_id_course($COURSE->id);
			
			// For each section, it loads its resources
			foreach ($sections as $section) {
				$section->ressources = $res_sections->get_ressources_by_id_section($section->id);
			}
			// When the resources are loaded, we assign the indicator to each ressources
			foreach ($sections as $section) {
				foreach ($section->ressources as $mod) {
					$mod->ressource_hide_indic = $res_sections->get_ressource_hide_indicator($mod->id);
				}
			}
			
			return $sections;
		}
		
		/**
		 * @param $id_section
		 * @return array
		 * @throws dml_exception
		 */
		public function get_ressource_hide_indicator_new($id_section)
		{
			global $DB;
			$modules_state = $DB->get_records_sql('SELECT * from {format_iena_settings}
                                                where sectionid = ?', array($id_section));
			
			return $modules_state;
		}
		
		/**
		 * @return string
		 * @throws coding_exception
		 * @throws dml_exception
		 */
		public function get_content()
		{
			global $COURSE, $DB, $CFG;
			$sections = $this->get_course_sections_modules();
			$moduleTools = new course_format_iena_section_ressources();
//			$content = '';
//			$content .= require("subview_param_indic.php");
			$content .= "
   
<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>

<script>
$(document).ready(function()
    {
        $('.sectionH').hide();
        $('.'+$('#select-section').val()).show();
        $('#select-section').on('change', function()
        {
            $('.sectionH').hide();
            $('.'+this.value).show();
        });
   });
</script>

<form action=\"/course/format/iena/param_indicateur.php?courseid=" . $COURSE->id . " \" method=\"post\">
	<section class=\"section\" id=\"params\">
		<div class=\"\">
			<h2 class=\"param\">" . get_string('indic_suivi', 'format_iena') . "</h2>
			<select class=\"select\" name='select-section' id=\"select-section\">";
			
			foreach ($sections as $section) {
				$checked = "";
				if ($section->id == $_GET['sectionid']) {
					$checked = "selected";
				}
				$content .= "<option value=\"section-" . $section->id . "\" " . $checked . ">" . $section->name . "</option>";
			}
			$content .= "
                        </select>
		</div>
		<div class=\"\">
			<h2  class=\"param\">" . get_string('check_completion', 'format_iena') . "</h2>
			<p>" . get_string('for_section_select', 'format_iena') . "</p>
		</div>
	</section>";
			
			foreach ($sections as $section) {
				$hidden_modules = $this->get_ressource_hide_indicator_new($section->id);
				$content .= "
        <section class=\"section-" . $section->id . " sectionH \">
		<div class=\"heading_title\">
			<p>" . $section->name . "</p>
		</div>";
				
				foreach ($hidden_modules as $hidden_mod) {
					$content .= "
                <div class=\"field\">
                        <div class=\"control\">
				<label class=\"checkbox\">
					<input type=\"checkbox\" name=\"" . $hidden_mod->cmid . "section-" . $hidden_mod->sectionid . "\"";
					
					if ($hidden_mod->hide == 1) {
						$content .= "checked";
					}
					
					
					$content .= ">";
					$moduleTools->get_ressource_by_id($hidden_mod->cmid);
					$content .= $moduleTools->name;
					$content .= "
				</label>
			</div>
		</div>";
				}
				$content .= "
	</section>";
			}
			$link_annuler = $CFG->wwwroot . "/course/format/iena/suivi_unit.php?courseid=" . $COURSE->id . "&sectionid=" . $_GET['sectionid'];
			$content .= "
	<section>
	<a id=\"button\" href='" . $link_annuler . "' class=\"btn btn_reset big_button\" style=\"font-weight:bold;\">" . get_string('cancel', 'format_iena') . "</i></a>
		<button id=\"button\" class=\"btn btn_blue big_button\" style=\"font-weight:bold;\" type=\"submit\">" . get_string('save', 'format_iena') . "</i>
		</button>
	</section>
</form>";
			
			return $content;
		}
	}
