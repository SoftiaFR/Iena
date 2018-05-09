<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 13/03/18
	 * Time: 11:26
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
	 * view_send_message
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class view_send_message extends moodleform
	{
		public function definition()
		{
			// TODO: Implement definition() method.
			global $CFG;
			$mform = $this->_form; // Don't forget the underscore!
		}
		
		public function get_content($usersID)
		{
			global $DB, $CFG, $COURSE;
			
			$mform = $this->_form;
			//TODO add $description to settings value
			$description = "";
			$courseID = required_param('courseid', PARAM_INT);
			$mform->addElement('editor', 'summary', get_string('summary', 'format_iena'));
			$mform->setType('summary', PARAM_RAW);
			$mform->addRule('summary', get_string('error'), 'required', null, null, false, false);
			$mform->setDefault('summary', array('text' => $description));
			
			$content = "<form class=\"form-horizontal\" action=\"/course/format/iena/send_message_post.php?courseid=" . $COURSE->id . "\" method=\"post\">
                    <fieldset>
                    <div class=\"form-group\">";
			$temp = $mform->toHtml();
			//Del <form>
			$temp = substr($temp, (strpos($temp, '>') + 1));
			$temp = substr($temp, 0, -7);
			
			$content .= $temp;
			$content .= "<input type='hidden' name='usersid' value='" . $usersID . "'>";
			$link_annuler = $CFG->wwwroot . "/course/view.php?id=" . $courseID;
			$content .= '<section class="section">
                        <div class="row">  
                            <div class="col-lg-3 col-md-3 padding_column">
                                <button type="submit" class="btn btn-primary btn-lg btn-block btn-block-tide"><i class="fa fa-plus"></i> Enregistrer</button>
                            </div>
                            <div class="col-lg-3 col-md-3 padding_column">
                            <a href=\' ' . $link_annuler . ' \' class=\'btn btn-primary btn-lg btn-block btn-block-tide\'> Annuler </a>
                            </div>
                        </div>
                     </section>';
			$content .= "</form>";
			
			return $content;
			
		}
	}