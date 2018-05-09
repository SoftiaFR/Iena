<?php
	
	require_once('../../config.php');

// ENLEVER SI NON NECESSAIRE :
	require_once('entity/block_competency_iena_competency.php');
	require_once('entity/block_competency_iena_module.php');
	require_once('entity/block_competency_iena_ressource.php');
	require_once('entity/block_competency_iena_section.php');
	require_once('entity/block_competency_iena_student.php');
	require_once('entity/block_competency_iena_cron_roles.php');
	require_once('entity/block_competency_iena_cron_competency.php');
	require_once('view/view_competency_iena_competencies.php');
	
	
	global $COURSE, $DB, $USER;
	
	try {
		$courseid = required_param('courseid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$studentid = optional_param('studentid', $USER->id, PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$url = new moodle_url('/blocks/competency_iena/competency_iena_competencies.php', array('courseid' => $courseid, 'studentid' => $studentid));
	} catch (moodle_exception $e) {
	}
	
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
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	
	/*$cron_test = new block_competency_iena_cron_roles();
	$cron_test->attribute_roles_iena_complete();*/
	/*$cron_test = new block_competency_iena_cron_competency();
	$cron_test->attribute_competency_iena();*/
	
	$view = new view_competency_iena_competencies();
	echo $view->get_content($studentid);
	
	try {
		echo $OUTPUT->footer();
	} catch (coding_exception $e) {
	}
