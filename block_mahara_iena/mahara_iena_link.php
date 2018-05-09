<?php

require_once('../../config.php');
require_once ('entity/block_mahara_iena_connexion.php');

global $COURSE, $DB, $USER, $CFG;

$courseid = required_param('courseid', PARAM_INT);
$studentid = optional_param('studentid',$USER->id,PARAM_INT);
$url = new moodle_url('/blocks/mahara_iena/mahara_iena_link.php',array('courseid' => $courseid, 'studentid' => $studentid));

$PAGE->set_pagelayout('course');
$PAGE->set_url($url);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
require_login($course, false, NULL);


if ($_POST){
    $result =  $DB->get_record_sql('SELECT * FROM {block_mahara_iena} WHERE course = ?', array($COURSE->id));
    // We have no line into block_mahara_iena for this course id (insert)
    if (!$result){
        $record = new stdClass();
        $record->course = $COURSE->id;
        $record->code = $_POST["slc_iena"];
        $DB->insert_record('block_mahara_iena',$record,false);
    }
    // We have one line into block_mahara_iena for this course id (update)
    else {
        $record = new stdClass();
        $record->course = $COURSE->id;
        $record->code = $_POST["slc_iena"];
        $record->id = $result->id;
        $DB->update_record('block_mahara_iena',$record,false);
    }
    // Redirect to the previous page
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}

if (!has_capability('moodle/course:update', $context = context_course::instance($courseid), $USER->id)) {
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}

$PAGE->set_title(get_string('title_plugin', 'block_mahara_iena'));
$PAGE->set_heading($OUTPUT->heading($COURSE->fullname, 2, 'headingblock header outline'));
echo $OUTPUT->header();
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";

$connextion = new block_mahara_iena_connexion($CFG->wstoken,$CFG->base_mahara."webservice/rest/server.php");
//Get all students
$course_ctx = context_course::instance($course->id);
$students = get_enrolled_users($course_ctx);

$usersTab = array();
$maharaUsers = $connextion->getMaharaUsers();

$result = $DB->get_records_sql('SELECT * FROM {block_mahara_iena} WHERE course = ?', array($COURSE->id));
        // if the query is empty we add new line

        $groups = $connextion->getMaharaGroups();
        $select_groups = false;
        $select_groups .= "<select name='slc_iena' class='form-control'  size='5'>";
        $groupeMaharaNow = "";
        foreach ($groups as $group) {
            if (reset($result)->code == $group->id){
                $groupeMaharaNow = $group->name;
            }
            $select_groups .= "<option value='".$group->id."'>".$group->name."</option>";
        }
        $select_groups .= "</select>";

        if ($result){
            echo "<br>".get_string('mahara_actualy', 'block_mahara_iena')." ".$COURSE->fullname." 
            ".get_string('link_mahara', 'block_mahara_iena')." ".$groupeMaharaNow;
        }
        echo "<form method='POST'>";
        echo get_string('chose_manu_grp', 'block_mahara_iena');
        echo $select_groups;
        echo "<button class='btn' type='submit'>".get_string('send', 'block_mahara_iena')."</button>";
        echo "</form>";

echo "<br><a href=\"".$CFG->wwwroot."/course/view.php?id=".$COURSE->id." \" class=\"btn btn-primary\" 
      role=\"button\">".get_string('back_course', 'block_mahara_iena')."</a>";

echo $OUTPUT->footer();
