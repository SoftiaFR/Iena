<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 06/03/18
	 * Time: 10:20
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
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	define('NO_OUTPUT_BUFFERING', true);
	require_once('../../../config.php');
	
	global $COURSE, $DB, $USER;
	
	$courseID = required_param('courseid', PARAM_INT);
	$sectionId = required_param('sectionid', PARAM_INT);
	$url = new moodle_url('/course/format/iena/param_section.php', array('courseid' => $courseID, 'sectionid' => $sectionId));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	if (!has_capability('moodle/course:update', $context = context_course::instance($courseID), $USER->id)) {
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	
	if ($_POST) {
		global $DB;
		$dateChaine = $_POST["date_iena"]["year"] . "-" . $_POST["date_iena"]["month"] . "-" . $_POST["date_iena"]["day"] . " " .
			$_POST["date_iena"]["hour"] . ":" . $_POST["date_iena"]["minute"] . ":00";
		$dateUp = date_create($dateChaine);
		$data_iena = new stdClass();
		if ($_POST['date_render']) {
			$dateChaine = "";
		}
		$data_iena->date_rendu = $dateChaine;
		$data_iena->presence = $_POST['presence'];
		$data_iena->id_section = $sectionId;
		if ($_POST['day_same']) {
			$data_iena->day_same = $_POST['day_same'];
		} else {
			$data_iena->day_same = 0;
		}
		
		if ($_POST['day_before']) {
			$data_iena->day_before = $_POST['day_before'];
		} else {
			$data_iena->day_before = 0;
		}
		if ($_POST['day_after']) {
			$data_iena->day_after = $_POST['day_after'];
		} else {
			$data_iena->day_after = 0;
		}
		$data_iena->nb_days_before = $_POST['nb_days_before'];
		$data_iena->nb_days_after = $_POST['nb_days_after'];
		$data_iena->hide = $_POST['hide'];
		
		$testIfsection = $DB->get_record('format_iena', array('id_section' => $sectionId), '*');
		if ($testIfsection == false) {
			$DB->insert_record('format_iena', $data_iena, false);
		} else {
			$data_iena->id = $testIfsection->id;
			$DB->update_record('format_iena', $data_iena, false);
		}
		$data['id'] = $sectionId;
		$data['timemodified'] = time();
		$data['name'] = $_POST['name'];
		$data['summary'] = $_POST['summary']['text'];
		$DB->update_record('course_sections', $data);
		rebuild_course_cache($courseid, true);
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	
	$dataSection = $DB->get_record('format_iena', array('id_section' => $sectionId), '*');
	$course = $DB->get_record('course', array('id' => $courseID), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	$PAGE->set_title($COURSE->fullname);
	$PAGE->set_heading($COURSE->fullname);
	echo $OUTPUT->header();
	//$PAGE->requires->js("/course/format/Sprint3/js/jquery.min.js");
	//$PAGE->requires->js("/course/format/iena/js/file.js");
	//$PAGE->requires->js_call_amd("/course/format/Sprint3/js/jquery.dataTables.js",  'init');
	require_once("$CFG->libdir/formslib.php");
	require_once('view/view_param_section.php');
	$view_param_section = new view_param_section();
	
	echo $view_param_section->get_content($course, $dataSection);
	
	echo $OUTPUT->footer();