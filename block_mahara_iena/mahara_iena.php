<?php

require_once('../../config.php');
require_once ('entity/block_mahara_iena_connexion.php');

global $COURSE, $DB, $USER, $CFG;

$courseid = required_param('courseid', PARAM_INT);
$studentid = optional_param('studentid',$USER->id,PARAM_INT);
$url = new moodle_url('/blocks/mahara_iena/mahara_iena.php',array('courseid' => $courseid, 'studentid' => $studentid));

$PAGE->set_pagelayout('course');
$PAGE->set_url($url);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
require_login($course, false, NULL);

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

//Here we check by email, if the student have the same email into mahara and moodle hi is add to group
foreach ($maharaUsers as $muser){
    foreach ($students as $student) {
        if ($student->email == $muser->email){
            $role = "member";
            //Each students is admin
            if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $student->id)) {
                $role = "admin";
            }
            array_push($usersTab,array(
                'id' => $muser->id,
                'username' => $muser->username,
                'role' => $role,
            ));
            break;
        }
    }
}

//All params used for mahara_group_create_groups her we can add/change some lines
$params = array (
    'groups' =>
        array (
            0 =>
                array (
                    'name' => 'moodle_'.$COURSE->fullname,
                    'description' => 'description',
                    'grouptype' => 'course',
                    'request' => true,
                    'public' => true,
		            'institution' => $CFG->instution_mahara,
                    'members' =>
                        $usersTab
                )
        )
);
//Convert arrays into string for send to mahara api
$params = http_build_query($params);
$function = "mahara_group_create_groups";

$connextion->create_url($function);
$resultPost = $connextion->httpPost($params);

// '@' is use because json_decode some time cause fatal error
$data = @json_decode($resultPost);
$result = $DB->get_records_sql('SELECT * FROM {block_mahara_iena} WHERE course = ?', array($COURSE->id));
// IF json_decode fail we stop all
if ($data == null){
    echo 'error';
} else {
    // if $data is array the api call is done (OK)
    if (is_array($data)){
        // if the query is empty we add new line
        if (!$result){
        $record = new stdClass();
        $record->course = $COURSE->id;
        $record->code = $data[0]->id;
        $DB->insert_record('block_mahara_iena',$record,false);
        echo "<div class=\"box\">".get_string('this_grp', 'block_mahara_iena')."".$COURSE->fullname."
        ".get_string('just_add_grp', 'block_mahara_iena')." 
        Lien : <a href='".$CFG->base_mahara."group/view.php?id=".$data[0]->id."'>
        ".get_string('mahara_grp', 'block_mahara_iena')."</a></div>";}
        // If the query is not empty we leave the choice to set the "code" value
    } elseif ($data->error) {
        echo "<div class=\"box errorbox \"> ".get_string('error', 'block_mahara_iena')." ".$data->error_message."</div>";
    }
}
echo "<br><a href=\"".$CFG->wwwroot."/course/view.php?id=".$COURSE->id." \" class=\"btn btn-primary\" 
      role=\"button\">".get_string('back_course', 'block_mahara_iena')."</a>";

echo $OUTPUT->footer();
