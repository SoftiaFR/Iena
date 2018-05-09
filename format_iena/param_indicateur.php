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
	
	require_once('../../../config.php');
	require_once('view/view_param_indicateur.php');
	require_once('entity/course_format_iena_section_ressources.php');
	require_once('entity/course_format_iena_sections.php');
	
	global $COURSE, $DB, $USER;
	
	$courseID = required_param('courseid', PARAM_INT);
	$url = new moodle_url('/course/format/iena/param_indicateur.php', array('courseid' => $courseID));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	if (!has_capability('moodle/course:update', $context = context_course::instance($courseID), $USER->id)) {
		$link = $CFG->wwwroot . '/course/view.php?id=' . $courseID;
		header("Location: {$link}");
		exit;
	}
	$course = $DB->get_record('course', array('id' => $courseID), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	$PAGE->set_title('Indicateurs de suivi');
	$PAGE->set_heading('Indicateurs de suivi');
	
	/////////////// Update in Database all tables format_iena_settings for each module////////
	if ($_POST) {
		if ($_POST['select-section']) {
			$sectionid = $_POST['select-section'];
			$sectionid = explode('-', $sectionid)[1];
			
		} else {
			$sectionid = "undefined";
		}
		
		//Récupération de l'état des modules du formulaire
		$y = 0;
		foreach ($_POST as $key => $form_module) {
			$temp = explode($_POST['select-section'], $key);
			if (count($temp) > 1) {
				$tab_form_module[$y] = explode($_POST['select-section'], $key)[0];
			}
			$y++;
		}
		
		// récupération de l'état des modules dans la BDD
		$modulesStates = $DB->get_records('format_iena_settings', array('sectionid' => $sectionid));
		foreach ($modulesStates as $module) {
			//var_dump($modulesStates);
			// Création de l'objet à updater
			//$format_iena_setting_data_upd = new stdClass();
			$indic = false;
			foreach ($tab_form_module as $form_module) {
				if ($module->cmid == $form_module) {
					$format_iena_setting_data_upd = new stdClass();
					$format_iena_setting_data_upd->hide = 1;
					$format_iena_setting_data_upd->sectionid = $sectionid;
					$format_iena_setting_data_upd->id = $module->id;
					$DB->update_record('format_iena_settings', $format_iena_setting_data_upd, false);
					$indic = true;
					break;
				}
			}
			if ($indic == false) {
				$format_iena_setting_data_upd = new stdClass();
				$format_iena_setting_data_upd->hide = 0;
				$format_iena_setting_data_upd->sectionid = $sectionid;
				$format_iena_setting_data_upd->id = $module->id;
				$DB->update_record('format_iena_settings', $format_iena_setting_data_upd, false);
			}
		}
		
		$link = $CFG->wwwroot . '/course/format/iena/suivi_unit.php?courseid=' . $courseID . '&sectionid=' . $sectionid;
		header("Location: {$link}");
		exit;
	}
	
	echo $OUTPUT->header();
//	$PAGE->requires->js("/course/format/Sprint3/js/jquery.min.js");
//	$PAGE->requires->js("/course/format/iena/js/file.js");
//	$PAGE->requires->js_call_amd("/course/format/Sprint3/js/jquery.dataTables.js",  'init');
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"normalize.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css\">";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	
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
				$format_iena_setting_data->hide = 0;
				$format_iena_setting_data->courseid = $COURSE->id;
				$format_iena_setting_data->sectionid = $section->id;
				$DB->insert_record('format_iena_settings', $format_iena_setting_data, false);
			}
		}
	}
	
	
	echo $view_param_indicateur->get_content();
	echo $OUTPUT->footer();