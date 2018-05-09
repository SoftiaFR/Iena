<?php
	
	require_once('../../config.php');

// ENLEVER SI NON NECESSAIRE :
	require_once('entity/block_competency_iena_competency.php');
	require_once('entity/block_competency_iena_module.php');
	require_once('entity/block_competency_iena_ressource.php');
	require_once('entity/block_competency_iena_section.php');
	require_once('entity/block_competency_iena_student.php');
	
	
	global $COURSE, $DB;
	
	try {
		$courseid = required_param('courseid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$url = new moodle_url('/blocks/competency_iena/competency_iena_competencies_form.php', array('courseid' => $courseid));
	} catch (moodle_exception $e) {
	}
//$requete = $DB->get_record_sql('SELECT course FROM {block_competency_iena} WHERE id = ?', array($courseid));
	
	$PAGE->set_pagelayout('course');
	try {
		$PAGE->set_url($url);
	} catch (coding_exception $e) {
	}
	
	try {
		$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
	} catch (dml_exception $e) {
	}
	try {
		require_login($course, false, NULL);
	} catch (coding_exception $e) {
	} catch (require_login_exception $e) {
	} catch (moodle_exception $e) {
	}
	
	try {
		$PAGE->set_title(get_string('title_plugin', 'block_competency_iena'));
	} catch (coding_exception $e) {
	}
	try {
		$PAGE->set_heading($OUTPUT->heading($COURSE->fullname, 2, 'headingblock header outline'));
	} catch (coding_exception $e) {
	}
	try {
		echo $OUTPUT->header();
	} catch (coding_exception $e) {
	}
//$PAGE->requires->js("/blocks/competency_iena/js/jquery.min.js");
//$PAGE->requires->js("/blocks/competency_iena/js/file.js");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";

//require_once('view/view_competency_iena_unit.php');
	
	try {
		echo $OUTPUT->footer();
	} catch (coding_exception $e) {
	}
