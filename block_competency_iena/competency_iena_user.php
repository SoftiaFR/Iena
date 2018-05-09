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
	require_once('view/view_competency_iena_user.php');
	
	global $COURSE, $DB, $USER;
	
	try {
		$courseid = required_param('courseid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$competencyid = required_param('competencyid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$url = new moodle_url('/blocks/competency_iena/competency_iena_user.php');
	} catch (moodle_exception $e) {
	}
//$url = new moodle_url('/blocks/competency_iena/competency_iena_user.php',array('courseid' => $courseid, 'studentid' => $studentid));
//$url = new moodle_url('/blocks/competency_iena/competency_iena_user.php',array('courseid' => $courseid, 'studentid' => $studentid));
	
	$PAGE->set_pagelayout('course');
	try {
		$PAGE->set_url($url);
	} catch (coding_exception $e) {
	}
	
	if ($_POST) {
		// Delete the commentary in block_competency_iena_com table
		if ($_POST['info'][0] == "delete") {
			// Check if comment id exists
			try {
				$data_exist = $DB->get_record_sql('select count(*) AS count FROM {block_competency_iena_com} WHERE id=?', array($_POST['info'][1]));
			} catch (dml_exception $e) {
			}
			
			if ($data_exist->count > 0) {
				try {
					$DB->delete_records('block_competency_iena_com', array('id' => $_POST['info'][1]));
				} catch (dml_exception $e) {
				}
				echo 'true';
			} else {
				// if comment id doesn't exist
				echo 'false';
			}
		} // Insert
		else {
			$date = new DateTime();
			$comment_data = new stdClass();
			$comment_data->idsender = $_POST['info'][1];
			$comment_data->idstudent = $_POST['info'][2];
			$comment_data->idcourse = $_POST['info'][3];
			$comment_data->idcompetency = $_POST['info'][4];
			$comment_data->message = $_POST['info'][5];
			$comment_data->date = $date->getTimestamp();
			
			try {
				$resultat = $DB->insert_record('block_competency_iena_com', $comment_data, true);
			} catch (dml_exception $e) {
			}
			
			var_dump($resultat);
		}
		
		exit;
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
	
	$view = new view_competency_iena_user();
	echo $view->get_content();
	
	try {
		echo $OUTPUT->footer();
	} catch (coding_exception $e) {
	}
