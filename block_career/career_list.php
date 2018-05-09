<?php
	define('NO_OUTPUT_BUFFERING', true);
	require_once('../../config.php');
	require_once('entity/block_career_ressource.php');
	require_once('entity/block_career_section.php');
	require_once('view/view_career_list.php');
	
	global $COURSE;
	global $USER;
	global $DB;
	global $CFG;
	require_once($CFG->libdir . '/adminlib.php');
	
	$id_course = required_param('course', PARAM_INT);
	
	$url = new moodle_url('/blocks/career/career_list.php', array('course' => $id_course));
	//Check if the user has capability to update course
	if (!has_capability('moodle/course:update', $context = context_course::instance($id_course), $USER->id)) {
		header("Location: {$_SERVER['HTTP_REFERER']}");
		exit;
	}
	
	$PAGE->set_url($url);
	$PAGE->set_pagelayout('admin');
	
	$course = $DB->get_record('course', array('id' => $id_course), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	$PAGE->set_title(get_string('title_plugin', 'block_career'));
	$PAGE->set_heading($OUTPUT->heading($COURSE->fullname, 2, 'headingblock header outline'));
	
	$ressource = new block_career_ressource();
	$section = new block_career_section();
	echo $OUTPUT->header();
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	$content = new view_career_list();
	echo $content->get_content();
	echo $OUTPUT->footer();