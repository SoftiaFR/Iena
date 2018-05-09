<?php
	
	require_once('../../config.php');

// ENLEVER SI NON NECESSAIRE :
	require_once('entity/block_competency_iena_competency.php');
	require_once('entity/block_competency_iena_module.php');
	require_once('entity/block_competency_iena_ressource.php');
	require_once('entity/block_competency_iena_section.php');
	require_once('entity/block_competency_iena_student.php');
	require_once('entity/block_competency_iena_referentiel.php');
	require_once('view/view_competency_iena_competencies_mgmt.php');
	
	
	global $COURSE, $DB, $CFG;

	try {
		$courseid = required_param('courseid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	try {
		$url = new moodle_url('/blocks/competency_iena/competency_iena_competencies_mgmt.php', array('courseid' => $courseid));
	} catch (moodle_exception $e) {
	}
//$requete = $DB->get_record_sql('SELECT course FROM {block_competency_iena} WHERE id = ?', array($courseid));
	
	$PAGE->set_pagelayout('course');
	try {
		$PAGE->set_url($url);
	} catch (coding_exception $e) {
	}
	
	if ($_POST) {
		
		// Suppression du lien module - compétence dans la BDD
		if ($_POST['info'][2] == "delete") {
			$module_id = htmlspecialchars($_POST['info'][0]);
			$competency_id = htmlspecialchars($_POST['info'][1]);
			
			// vérification si lien module - compétence existe
			try {
				$data_exist = $DB->get_record_sql('select count(*) AS count FROM {competency_modulecomp} WHERE cmid=? AND competencyid =?', array($module_id, $competency_id));
			} catch (dml_exception $e) {
			}
			//var_dump($data_exist);
			if ($data_exist->count > 0) {
				//$DB->get_record_sql(' delete FROM {competency_modulecomp} WHERE cmid=? AND competencyid =?' , array($module_id, $competency_id));
				try {
					$DB->delete_records('competency_modulecomp', array('cmid' => $module_id, 'competencyid' => $competency_id));
				} catch (dml_exception $e) {
				}
				// vérifier si succes suppression
				echo 'true';
			} else {
				echo 'false';
			}
		} // Insert or update DB
		else {
			$module_id = htmlspecialchars($_POST['info'][0]);
			$competency_id = htmlspecialchars($_POST['info'][1]);
			$choix_ruleoutcome = htmlspecialchars($_POST['info'][2]);
			// check if link module - skill exists
			try {
				$data_exist = $DB->get_record_sql('select * FROM {competency_modulecomp} WHERE cmid=? AND competencyid =?', array($module_id, $competency_id));
			} catch (dml_exception $e) {
			}
			
			
			$date = new DateTime();
			$modulecomp_data = new stdClass();
			$modulecomp_data->cmid = $module_id;
			$modulecomp_data->competencyid = $competency_id;
			$modulecomp_data->ruleoutcome = $choix_ruleoutcome;
			$modulecomp_data->timemodified = $date->getTimestamp();
			$modulecomp_data->usermodified = $USER->id;
			
			// insert
			if (empty($data_exist)) {
				$modulecomp_data->sortorder = 0;
				$modulecomp_data->timecreated = $modulecomp_data->timemodified;
				try {
					$resultat = $DB->insert_record('competency_modulecomp', $modulecomp_data, true);
				} catch (dml_exception $e) {
				}
			} // update
			else {
				$modulecomp_data->id = $data_exist->id;
				try {
					$resultat = $DB->update_record('competency_modulecomp', $modulecomp_data, false);
				} catch (dml_exception $e) {
				}
				if ($resultat == true) {
					$resultat = "update";
				}
				
			}
			
			$response = array();
			$response[0] = $resultat;
			switch ($choix_ruleoutcome) //better with result of insert update queries
			{
				case 0:
					$response[1] = 'Ne rien faire';
					break;
				case 1:
					$response[1] = 'Joindre une preuve';
					break;
				case 2:
					$response[1] = 'Envoyer pour validation';
					break;
				case 3:
					$response[1] = 'Marquer la compétence comme atteinte';
					break;
				default:
					break;
			}
			echo $response[0] . '/' . $response[1];
			
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
//$PAGE->requires->js("/blocks/competency_iena/js/jquery.min.js");
	$PAGE->requires->js("/blocks/competency_iena/js/file.js");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	
	$view = new view_competency_iena_competencies_mgmt();
	echo $view->get_content(htmlspecialchars($courseid));
	
	
	try {
		echo $OUTPUT->footer();
	} catch (coding_exception $e) {
	}
