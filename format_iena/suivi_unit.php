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
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	define('NO_OUTPUT_BUFFERING', true);
	require_once('../../../config.php');
	require_once('view/view_param_indicateur.php');
	require_once('entity/course_format_iena_section_ressources.php');
	require_once('entity/course_format_iena_sections.php');
        require_once('entity/course_format_iena_groups.php');
	
	global $COURSE, $DB, $USER;
	
	// Defines the id of the course with a get parameter
	$courseID = required_param('courseid', PARAM_INT);
	// Define the url of the view
	$url = new moodle_url('/course/format/iena/suivi_unit.php', array('courseid' => $courseID));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	// Getting DB information (*)  of the course
	$course = $DB->get_record('course', array('id' => $courseID), '*', MUST_EXIST);
	require_login($course, false, NULL);
	$PAGE->set_title($course->fullname);
	$PAGE->set_heading($course->fullname);
	if (!has_capability('moodle/course:update', $context = context_course::instance($courseID), $USER->id)) {
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	echo $OUTPUT->header();
	// Loading CSS files
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"normalize.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	// Loading the JS file
	$PAGE->requires->js("/course/format/iena/js/w3.js");
	// If a post is sent trought the page
	if ($_POST) {
		require_once("$CFG->libdir/formslib.php");
		require_once('view/view_send_message.php');
		$usersID = $_POST["api_url"];
		$view = new view_send_message();
		echo $view->get_content($usersID);
	} else {
		
		$view_param_indicateur = new view_param_indicateur();
		$sections = $view_param_indicateur->get_course_sections_modules();
		$Tab_id_modules = array();
		$i = 0;
		
		foreach ($sections as $section) {
			foreach ($section->ressources as $mod) {
				$Tab_id_modules[$i] = $mod->id;
				$i++;
			}
		}
		
		foreach ($sections as $section) {
			foreach ($Tab_id_modules as $id_module) {
				$verif_db = $DB->get_record('format_iena_settings', array('cmid' => $id_module, 'sectionid' => $section->id), '*');
				
				if ($verif_db == false) {
					$format_iena_setting_data = new stdClass();
					$format_iena_setting_data->cmid = $id_module;
					$format_iena_setting_data->hide = 1;
					$format_iena_setting_data->courseid = $COURSE->id;
					$format_iena_setting_data->sectionid = $section->id;
					$DB->insert_record('format_iena_settings', $format_iena_setting_data, false);
				}
			}
		}
		
		require_once('view/view_suivi_unit.php');
	}
	
	echo $OUTPUT->footer();