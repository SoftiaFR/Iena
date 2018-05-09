<?php

    define('NO_OUTPUT_BUFFERING', true);
	require_once('../../config.php');

// ENLEVER SI NON NECESSAIRE :
    require_once("$CFG->libdir/formslib.php");
	require_once('entity/block_competency_iena_competency.php');
	require_once('entity/block_competency_iena_module.php');
	require_once('entity/block_competency_iena_ressource.php');
	require_once('entity/block_competency_iena_section.php');
	require_once('entity/block_competency_iena_student.php');
	require_once('entity/block_competency_iena_referentiel.php');
	require_once('view/view_competency_iena_competency_mgmt.php');
	require('view/view_competency_iena_competency_validation.php');
	
	
	global $COURSE, $DB, $USER;

	
	try {
		$courseid = required_param('courseid', PARAM_INT);
	} catch (coding_exception $e) {
	}
	
	try {
		$url = new moodle_url('/blocks/competency_iena/competency_iena_competencies_form.php', array('courseid' => $courseid));
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
	
	
	/**
	 * @param $id
	 * @return array
	 */
	function get_referential($id){
		$referentiel = htmlspecialchars($id);
		$ref = new block_competency_iena_referentiel();
		$ref_data = $ref->get_referentiels_informations($referentiel);
		return $ref_data;
	}
	
//$PAGE->requires->js("/blocks/competency_iena/js/jquery.min.js");
//$PAGE->requires->js("/blocks/competency_iena/js/file.js");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";

	$view = new view_competency_iena_competency_mgmt();
	$view2 = new view_competency_iena_competency_validation();
	/** @var TYPE_NAME $view */
	
	
	if (!empty($_POST)) {
		// Si on arrive depuis la modale de validation
		if (!empty($_POST['ref_mod']) && $_POST['ref_mod'] != '' && $_POST['ref_mod']) {
			$ref_data = get_referential($_POST['ref_mod']);
			echo $view->get_content($courseid, $ref_data);
		}
		// si on valide le formulaire de la page, changement de vue
		else {
			$ref_data = get_referential($_POST['ref_mod_valid']);

			// A changer pour le sender TODO
			$to      =  $CFG->mail_iena;
			$subject = "Demande d'accès pour modification du référentiel" ;
			$message = 'Demande d\'accès pour modification du référentiel : '.$ref_data[1]->shortname;
                        $message .= $_POST['textarea_message']['text'];
			$message .= '<br /> De la part de  : '.$USER->firstname . " " . $USER->lastname . " ( " . $USER->username. " / ID = " . $USER->id . " ) ";
			$headers = 'From: admin@softia.fr' . "\r\n" .
				'Reply-To: admin@softia.fr' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			
			// Sending confirm mail to
			mail($to, $subject, htmlspecialchars($message), $headers);
			echo $view2->get_content($courseid);
		}
		
	} else {
		$referentiel = 0;
		echo "Vous n'avez pas envoyé de formulaire, retour ... ";
	}

//require_once('view/view_competency_iena_unit.php');
	
	try {
		echo $OUTPUT->footer();
	} catch (coding_exception $e) {
	}
