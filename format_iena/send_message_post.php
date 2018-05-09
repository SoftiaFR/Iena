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
	
	global $COURSE, $DB, $USER, $CFG;
	
	$courseID = required_param('courseid', PARAM_INT);
	$url = new moodle_url('/course/format/iena/send_message_post.php', array('courseid' => $courseID));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	if (!has_capability('moodle/course:update', $context = context_course::instance($courseID), $USER->id)) {
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	
	$course = $DB->get_record('course', array('id' => $courseID), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	if ($_POST) {
		$messageContent = $_POST['summary']['text'];
		$usersid = explode(",", $_POST["usersid"]);
		$messageContent .= "<br>". get_string('course', 'format_iena') . " : " . $course->fullname;
		$messageContent .= "<br>". get_string('link', 'format_iena') . " :  <a href='" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . "'>" . $course->fullname . "</a>";
		$messageContent .= "<br> ". get_string('prof', 'format_iena') . " : " . $USER->firstname . " " . $USER->lastname . " " . $USER->email;
		$course_ctx = context_course::instance($courseID);
		$students = get_enrolled_users($course_ctx);
		$message = new \core\message\message();
		$message->component = 'moodle';
		$message->name = 'instantmessage';
		$message->userfrom = $USER;
		$message->notification = '0';
		// sending a notification to each students
		foreach ($usersid as $userID) {
			foreach ($students as $student) {
				//var_dump($student);
				if ($student->id == $userID) {
					$message->userto = $student;
					$message->smallmessage = $messageContent;
					$message->subject = $messageContent;
					$messageid = message_send($message);
					break;
				}
			}
		}
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	
	$dataSection = $DB->get_record('format_iena', array('id_section' => $sectionId), '*');
	$course = $DB->get_record('course', array('id' => $courseID), '*', MUST_EXIST);
	require_login($course, false, NULL);
	echo $OUTPUT->header();
	
	
	echo $OUTPUT->footer();