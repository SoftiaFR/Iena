<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 06/03/18
	 * Time: 10:26
	 */

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
	 * view_param_section
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class view_param_section extends moodleform
	{
		public function definition()
		{
			
			global $CFG;
			$mform = $this->_form; // Don't forget the underscore!
		}
		
		/**
		 * @param $course
		 * @param $dataSection
		 * @return string
		 * @throws HTML_QuickForm_Error
		 * @throws coding_exception
		 * @throws moodle_exception
		 */
		public function get_content($course, $dataSection)
		{
			global $DB, $CFG, $PAGE;
			
			$mform = $this->_form;
			$sectionId = required_param('sectionid', PARAM_INT);
			$courseID = required_param('courseid', PARAM_INT);
			
			$modinfo = get_fast_modinfo($course);
			$modinfo->get_sections();
			foreach ($modinfo->get_section_info_all() as $section => $thissection) {
				if ($thissection->id == $sectionId) {
					if (!$thissection->name) {
						$name = get_string('sectionname', 'format_iena') . ' ' . $thissection->section;
					} else {
						$name = $thissection->name;
					}
					$description = $thissection->summary;
					break;
				}
			}
			
			// Adding elements from Moodle basic components
			
			$mform->addElement('text', 'name', get_string('name', 'format_iena'));
			$mform->addRule('name', get_string('error'), 'required', null, null, false, false);
			$mform->setDefault('name', $name);
			
			$mform->addElement('editor', 'summary', get_string('summary', 'format_iena'));
			$mform->setType('summary', PARAM_RAW);
			$mform->addRule('summary', get_string('error'), 'required', null, null, false, false);
			$mform->setDefault('summary', array('text' => $description));
			
			$mform->addElement('date_time_selector', 'date_iena', 'Date');
			$dateUp = date_create($dataSection->date_rendu);
			$mform->setDefault('date_iena', $dateUp->getTimestamp());
			
			$mform->addElement('checkbox', 'date_render', get_string('form_not_defined', 'format_iena'));
			if (!$dataSection->date_rendu) {
				$mform->setDefault('date_render', '1');
			}
			
			//Adding elements to the form not included in moodle
			
			$content = "
		        <h1>" . get_string('settings_section_form', 'format_iena') . " : " . $name . "</h1>
		        <form class=\"form-horizontal\" action=\"param_section.php?courseid=" . $courseID . "&sectionid=" . $sectionId . "\" method=\"post\">
		                    <fieldset>
		                    <div class=\"form-group\">";
					$temp = $mform->toHtml();
					//Supprime le <form>
					$temp = substr($temp, (strpos($temp, '>') + 1));
					$temp = substr($temp, 0, -7);
					
					$content .= $temp;
					
					$content .= "
		<div class=\"row\">
		<!-- Multiple Radios -->
		<br>
		  <label class=\"col-md-3 control-label\" for=\"radios\">" . get_string('modalite', 'format_iena') . "</label>
		  <div class=\"col-md-4\">
		  <div class=\"radio\">
		    <label for=\"radios-0\">
		      <input type=\"radio\" name=\"presence\" id=\"presence-0\" value=\"1\" ";
					if ($dataSection->presence == 1) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('in_presence', 'format_iena') . "
		    </label>
			</div>
		  <div class=\"radio\">
		    <label for=\"radios-1\">
		      <input type=\"radio\" name=\"presence\" id=\"presence-1\" value=\"2\" ";
					if ($dataSection->presence == 2) $content .= "checked=\"checked\"";
					$content .= ">
		     " . get_string('not_presence', 'format_iena') . "
		    </label>
			</div>
			<div class=\"radio\">
		    <label for=\"radios-1\">
		      <input type=\"radio\" name=\"presence\" id=\"presence-2\" value=\"0\" ";
					if (!$dataSection->presence) $content .= "checked=\"checked\"";
					$content .= ">
		     " . get_string('form_not_defined', 'format_iena') . "
		    </label>
			</div>
		  </div>
		</div>
		
		<!-- Multiple Checkboxes -->
		<div class=\"row\">
		<div class=\"form-group\">
		<div class=\"col-md-3\">
		  <label class=\"control-label\" for=\"checkboxes\">" . get_string('notif', 'format_iena') . "</label>
		  <p style=\"font-size: 12px;\">" . get_string('notif_summary', 'format_iena') . "</p></div>
		  <div class=\"col-md-4\" style=\"padding-top: 30px;\">
		  <div class=\"checkbox\">
		    <label for=\"checkboxes-0\">
		      <input type=\"checkbox\" name=\"day_before\" id=\"day_before\" value=\"1\" ";
					if ($dataSection->day_before == 1) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('days_before', 'format_iena') . " : <select class=\"form-control\" style=\"width: 55px; display: inline;\" id=\"nb_days_before\" name =\"nb_days_before\">
		      <option ";
					if ($dataSection->nb_days_before == 1) $content .= "selected=\"selected\"";
					$content .= " >1</option>
		      <option ";
					if ($dataSection->nb_days_before == 2) $content .= "selected=\"selected\"";
					$content .= " >2</option>
		      <option ";
					if ($dataSection->nb_days_before == 3) $content .= "selected=\"selected\"";
					$content .= " >3</option>
		      <option ";
					if ($dataSection->nb_days_before == 4) $content .= "selected=\"selected\"";
					$content .= " >4</option>
		      <option ";
					if ($dataSection->nb_days_before == 5) $content .= "selected=\"selected\"";
					$content .= " >5</option>
		    </select> " . get_string('nb_days_before', 'format_iena') . "
		    </label>
			</div>
		  <div class=\"checkbox\">
		    <label for=\"checkboxes-1\">
		      <input type=\"checkbox\" name=\"day_same\" id=\"day_same\" value=\"1\" ";
					if ($dataSection->day_same == 1) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('days_same', 'format_iena') . "
		    </label>
			</div>
			 <div class=\"checkbox\">
		    <label for=\"checkboxes-2\">
		      <input type=\"checkbox\" name=\"day_after\" id=\"day_after\" value=\"1\" ";
					if ($dataSection->day_after == 1) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('days_after', 'format_iena') . " : <select class=\"form-control\" style=\"width: 55px; display: inline;\" id=\"nb_days_after\" name =\"nb_days_after\">
		      <option ";
					if ($dataSection->nb_days_after == 1) $content .= "selected=\"selected\"";
					$content .= " >1</option>
		      <option ";
					if ($dataSection->nb_days_after == 2) $content .= "selected=\"selected\"";
					$content .= " >2</option>
		      <option ";
					if ($dataSection->nb_days_after == 3) $content .= "selected=\"selected\"";
					$content .= " >3</option>
		      <option ";
					if ($dataSection->nb_days_after == 4) $content .= "selected=\"selected\"";
					$content .= " >4</option>
		      <option ";
					if ($dataSection->nb_days_after == 5) $content .= "selected=\"selected\"";
					$content .= " >5</option>
		    </select> " . get_string('nb_days_after', 'format_iena') . "
		    </label>
			</div>
		  </div>
		 
		</div>
		</div>
		
		<!-- Multiple Radios -->
		<div class=\"row\">
		<div class=\"form-group\">
		<div class=\"col-md-3\">
		  <label class=\" control-label\" for=\"radios\">" . get_string('hide_section', 'format_iena') . "</label>
		  <p style=\"font-size: 12px;\">" . get_string('hide_section_summary', 'format_iena') . "</p>
		  </div>
		  <div class=\"col-md-4\">
		  <div class=\"radio\">
		    <label for=\"radios-0\">
		      <input type=\"radio\" name=\"hide\" id=\"hide-0\" value=\"1\" ";
					if ($dataSection->hide == 1 || !$dataSection->hide) $content .= "checked=\"checked\"";
					$content .= ">
		     " . get_string('hide_option_1', 'format_iena') . "
		    </label>
			</div>
		  <div class=\"radio\">
		    <label for=\"radios-1\">
		      <input type=\"radio\" name=\"hide\" id=\"hide-1\" value=\"2\" ";
					if ($dataSection->hide == 2) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('hide_option_2', 'format_iena') . "
		    </label>
			</div>
			<div class=\"radio\">
		    <label for=\"radios-2\">
		      <input type=\"radio\" name=\"hide\" id=\"hide-2\" value=\"3\" ";
					if ($dataSection->hide == 3) $content .= "checked=\"checked\"";
					$content .= ">
		      " . get_string('hide_option_3', 'format_iena') . "
		    </label>
			</div>
		  </div>
		</div>
		</fieldset>
		</div>";
					$link_annuler = $CFG->wwwroot . "/course/view.php?id=" . $courseID;
					$content .= '<section class="section">
		                        <div class="row">
		                            <div class="col-lg-3 col-md-3 padding_column">
		                                <button type="submit" class="btn btn-primary btn-lg btn-block btn-block-tide"><i class="fa fa-plus"></i> ' . get_string('save', 'format_iena') . '</button>
		                            </div>
		                            <div class="col-lg-3 col-md-3 padding_column">
		                            <a href=\' ' . $link_annuler . ' \' class=\'btn btn-primary btn-lg btn-block btn-block-tide\'>' . get_string('cancel', 'format_iena') . '</a>
		                            </div>
		                        </div>
		                     </section>';
					$content .= "</form>
		<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>
		<script>
		$(document).ready(function () {
			$('.fdescription').hide();
		});
		</script>
";
			
			return $content;
		}
		
	}