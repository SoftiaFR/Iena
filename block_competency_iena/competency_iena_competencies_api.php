<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 04/04/18
	 * Time: 14:59
	 */
	require_once('../../config.php');

// ENLEVER SI NON NECESSAIRE :
	require_once('entity/block_competency_iena_competency.php');
	require_once('entity/block_competency_iena_module.php');
	require_once('entity/block_competency_iena_ressource.php');
	require_once('entity/block_competency_iena_section.php');
	require_once('entity/block_competency_iena_student.php');
	require_once('entity/block_competency_iena_cron_roles.php');
	require_once('entity/block_competency_iena_cron_competency.php');
	require_once('entity/block_competency_iena_referentiel.php');
	require_once('view/view_competency_iena_competencies.php');
	
	
	global $COURSE, $DB, $USER;
	
	$courseid = required_param('courseid', PARAM_INT);
//$studentid = optional_param('studentid',$USER->id,PARAM_INT);
	$url = new moodle_url('/blocks/competency_iena/competency_iena_competencies_api.php', array('courseid' => $courseid));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	if ($_POST) {
		if (isset($_POST["idcompetence"])) {
			$idcompetence = htmlspecialchars($_POST["idcompetence"]);
			$compI = new block_competency_iena_competency();
			$compI->get_competency_by_id($idcompetence);
			$tab['shortname'] = $compI->shortname;
			$tab['description'] = $compI->description;
			$tab['id'] = $compI->id;
			echo json_encode($tab);
		}
		
		if (isset($_POST["addcomp"])) {
			$value = $_POST["addcomp"];
			$refI = new block_competency_iena_referentiel();
			$is_insert = $refI->add_competency_in_course($value[0], $value[1]);
			if ($is_insert) {
				echo 'true';
			} else {
				echo 'false';
			}
		}
//		updateTextRef
		if (isset($_POST["idref"])) {
			$idref = $_POST["idref"];
			$refI = new block_competency_iena_referentiel();
			$value = $refI->get_info_framework_by_id($idref);
			$tab['shortname'] = $value->shortname;
			$tab['description'] = $value->description;
			$tab['id'] = $value->id;
			echo json_encode($tab);
		}
		
		if (isset($_POST["delproof"])) {
			$idproof = htmlspecialchars($_POST["delproof"]);
			$DB->delete_records("competency_evidence", array('id' => $idproof));
			return 'true';
		}

		if (isset($_POST["askvalide"])) {
		    $idcompetence = $_POST["askidcomp"];
		    $iduser = $_POST["iduser"];
		    $status = $_POST["askvalide"];
            $id_usercomp = $DB->get_record_sql('select id FROM {competency_usercomp} 
                          WHERE competencyid = ? AND userid = ?', array($idcompetence,$iduser));
            $record = new stdClass();
            $record->id = $id_usercomp->id;
            $record->status = $status;
            $DB->update_record("competency_usercomp", $record, false);
        }

		if (isset($_POST["eval_comp"])){
		    $eval_comp = $_POST["eval_comp"];
		    $iduser = $_POST["userid"];
            $idcompetence = $_POST["compid"];
            $eval_comp++;
            $api = new \core_competency\external();
            var_dump($api::grade_competency_in_course($courseid,$iduser,$idcompetence,$eval_comp,null));
        }
	}
	