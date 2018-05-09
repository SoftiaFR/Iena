<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 28/03/18
 * Time: 10:35
 */

class block_competency_iena_cron_roles
{

    public function attribute_roles_iena_complete(){
        global $DB, $CFG;
        $idrole = $DB->get_record_sql('SELECT id FROM {role} WHERE shortname = ?', array($CFG->role_iena));
        $idrole = $idrole->id;
        $DB->delete_records('role_assignments', array('roleid' => $idrole ));
        $this->attribute_roles_iena();
    }

    public function attribute_roles_iena(){
        global $DB, $USER, $CFG;
        echo 'Start attribute_roles_iena';
        $time_start = microtime(true);
        $courses = $DB->get_records('course');
        $idrole = $DB->get_record_sql('SELECT id FROM {role} WHERE shortname = ?', array($CFG->role_iena));
        $idrole = $idrole->id;
        foreach ($courses as $course){
            if ($course->id == 1){
                continue;
            }
            $course_ctx = context_course::instance($course->id);
            $teachers = get_enrolled_users($course_ctx,'moodle/course:update');
            foreach ($teachers as $teacher){
                $students = get_enrolled_users($course_ctx);
                foreach ($students as $student){
                    if ($student->id == $teacher->id){
                        continue;
                    }
                    $contextid = context_user::instance($student->id)->id;
                    $is_connecte = $DB->get_record_sql('SELECT count(*) FROM {role_assignments} 
                                  WHERE roleid = ? AND contextid = ? AND userid = ?', array($idrole,$contextid,$teacher->id));
                    if(!$is_connecte->count > 0){
                        $this->insert_role_assignements_iena($idrole,$contextid,$teacher);
                    }
                }
            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo 'Durée : '.$time.' secondes';
    }

    private function insert_role_assignements_iena($idrole,$contextid,$teacher){
        global $DB, $USER;
        $record = new stdClass();
        $record->roleid = $idrole;
        $record->contextid = $contextid;
        $record->userid = $teacher->id;
        $date = new DateTime();
        $record->timemodified = $date->getTimestamp();
        $record->modifierid = $USER->id;
        $record->component = '';
        $record->itemid = 0;
        $record->sortorder = 0;
        $DB->insert_record('role_assignments',$record,false);
    }

}