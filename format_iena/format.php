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
	 * format_iena
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	defined('MOODLE_INTERNAL') || die();
	require_once($CFG->libdir . '/filelib.php');
	require_once($CFG->libdir . '/completionlib.php');
	require_once('entity/course_format_iena_section_ressources.php');
	require_once('entity/course_format_iena_sections.php');
	require_once('entity/course_format_iena_cron_action.php');
        require_once('entity/course_format_iena_groups.php');
	
	/*$cron_test = new course_format_iena_cron_action();
	$cron_test->cron_hide_section();
	$cron_test->cron_message();*/
//use core_completion\progress;
	
	require_once($CFG->dirroot . '/blocks/myoverview/lib.php');
	require_once($CFG->dirroot . '/completion/classes/progress.php');
	require_once($CFG->libdir . '/completionlib.php');
        
        $PAGE->requires->js('/course/format/iena/js/jquery.min.js');
	$PAGE->requires->js('/course/format/iena/format.js');
	
	if ($topic = optional_param('topic', 0, PARAM_INT)) {
		$url = $PAGE->url;
		$url->param('section', $topic);
		debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
		redirect($url);
	}
	$context = context_course::instance($course->id);
	if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
		$course->marker = $marker;
		course_set_marker($course->id, $marker);
	}
	$course = course_get_format($course)->get_course();
	course_create_sections_if_missing($course, range(0, $course->numsections));
	$renderer = $PAGE->get_renderer('format_iena');
	if (!empty($displaysection)) {
		$renderer->print_single_section_page($course, null, null, null, null, $displaysection);
	} else {
		$renderer->print_iena_section_pages($course);
	}
//	$PAGE->requires->js('/course/format/iena/js/jquery.min.js');
//	$PAGE->requires->js('/course/format/iena/format.js');
