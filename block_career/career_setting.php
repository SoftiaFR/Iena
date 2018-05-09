<?php
	
	ob_start();
	
	require_once('../../config.php');
	global $COURSE, $DB, $CFG;
	require_once("$CFG->libdir/formslib.php");
	require_once('entity/block_career_ressource.php');
	require_once('entity/block_career_section.php');
	require_once('view/view_career_setting.php');
	
	$id_course = required_param('course', PARAM_INT);
	$url = new moodle_url('/blocks/career/career_setting.php', array('course' => $id_course));
	
	$PAGE->set_pagelayout('course');
	$PAGE->set_url($url);
	
	$course = $DB->get_record('course', array('id' => $id_course), '*', MUST_EXIST);
	require_login($course, false, NULL);
	
	
	$PAGE->set_title(get_string('title_plugin', 'block_career'));
	$PAGE->set_heading($OUTPUT->heading($COURSE->fullname, 2, 'headingblock header outline'));
	echo $OUTPUT->header();
	$PAGE->requires->js("/blocks/career/js/jquery.min.js");
	$PAGE->requires->js("/blocks/career/js/file.js");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
	
	$content = new view_career_setting();
	echo $content->get_content();

	// Delete career
	if (isset($_GET["delete"]) && $_GET["delete"] == 1) {
		$DB->execute("DELETE FROM {block_career} WHERE id = ?", array($_GET["id"]));
		header("Location: $CFG->wwwroot/blocks/career/career_list.php?course=" . $_GET["course"]);
	}
	
	if (!empty($_POST["careerName"])) {
		
		$ressourses = "";
		
		foreach ($_POST["ressource"] as $value) {
			if ($value === end($_POST["ressource"])) {
				$ressourses .= "$value";
			} else {
				$ressourses .= "$value,";
			}
		}
		
		//$record is use for insert/update in database
		$record = new stdClass();
		$record->course = intval($_GET["course"]);
		$record->name = $_POST["careerName"];
		$record->description = $_POST["descriptionName"]["text"];
		
		if (isset($_FILES['imageName']['tmp_name'])) {
			$pathDir = "img/";
			$pathFile = $pathDir . basename($_FILES["imageName"]["name"]);
			move_uploaded_file($_FILES['imageName']['tmp_name'], $pathFile);
			$record->image = $pathFile;
		} else {
			$record->image = $_POST["imagePath"];
		}
		
		$record->ressources = $ressourses;
		
		if ($_POST["careerId"] != 0) {
			$record->id = intval($_POST["careerId"]);
			$lastinsertid = $DB->update_record('block_career', $record);
		} else {
			$lastinsertid = $DB->insert_record('block_career', $record);
		}
		
		if ($lastinsertid != 0) {
			header("Location: $CFG->wwwroot/blocks/career/career_list.php?course=" . $_GET["course"]);
		}
		
	}
	
	
	echo $OUTPUT->footer();
