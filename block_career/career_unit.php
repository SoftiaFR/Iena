<?php
	
	require_once('../../config.php');
	require_once('entity/block_career_ressource.php');
	require_once('entity/block_career_section.php');
	
	global $COURSE, $DB;
	
	$careerId = required_param('career', PARAM_INT);
	$url = new moodle_url('/blocks/career/career_unit.php', array('career' => $careerId));
	$requete = $DB->get_record_sql('SELECT course FROM {block_career} WHERE id = ?', array($careerId));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	// Getting DB data for the course
	$course = $DB->get_record('course', array('id' => $requete->course), '*', MUST_EXIST);
	// Must be logged in
	require_login($course, false, NULL);
	
	$PAGE->set_title(get_string('title_plugin', 'block_career'));
	$PAGE->set_heading($OUTPUT->heading($COURSE->fullname, 2, 'headingblock header outline'));
	echo $OUTPUT->header();
	$PAGE->requires->js("/blocks/career/js/jquery.min.js");
	$PAGE->requires->js("/blocks/career/js/file.js");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	
	require_once('view/view_career_unit.php');
	
	echo $OUTPUT->footer();
