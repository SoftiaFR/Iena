<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 28/03/18
 * Time: 14:04
 */

require_once('block_competency_iena_competency.php');
require_once('block_competency_iena_module.php');
require_once('block_competency_iena_ressource.php');
require_once('block_competency_iena_section.php');
require_once('block_competency_iena_student.php');

class block_competency_iena_cron_competency
{
    public function attribute_competency_iena(){

        global $DB, $USER, $CFG;
        echo 'Start attribute_competency_iena';
        $time_start = microtime(true);
        $courses = $DB->get_records('course');

        foreach ($courses as $course){
            if ($course->id == 1){
                continue;
            }
            $course_ctx = context_course::instance($course->id);
            $students = get_enrolled_users($course_ctx);
            foreach ($students as $student){
                $idplan = 0;
                $have_dynamic_plan = $DB->get_record_sql('SELECT * FROM {competency_plan} 
                                  WHERE userid = ? AND name = ?', array($student->id,$CFG->parcour_iena));
                if (!$have_dynamic_plan->id){
                   $idplan = $this->insert_competency_plan_iena($student);
                } else {
                    $idplan = $have_dynamic_plan->id;
                }
                $competencies = new block_competency_iena_competency();
                $tabcompe = $competencies->get_competencies_by_courseID($course->id);
                foreach ($tabcompe as $competency) {
                    $is_competency_in_plan = $DB->get_record_sql('SELECT count(*) FROM {competency_plancomp}
                                             WHERE planid = ? AND competencyid = ?', array($idplan, $competency->id));
                    if ($is_competency_in_plan->count == 0){
                        $this->insert_competency_plancomp_iena($idplan,$competency);
                    }
                }

            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo 'Durée : '.$time.' secondes';
    }


    private function insert_competency_plan_iena($student){
        global $DB, $USER, $CFG;
        $record = new stdClass();
        $record->name = $CFG->parcour_iena;
        $record->description = '<p>Plan créer dynamiquement</p>';
        $record->descriptionformat = 1;
        $record->userid = $student->id;
        $record->status = 1;
        $record->duedate = 0;
        $date = new DateTime();
        $record->timecreated = $date->getTimestamp();
        $record->timemodified = $date->getTimestamp();
        $record->usermodified = $USER->id;

        $id_plan = $DB->insert_record('competency_plan',$record,true);
        return $id_plan;
    }

    private function insert_competency_plancomp_iena($idplan, $competency){
        global $DB, $USER;

        $record = new stdClass();
        $record->planid = $idplan;
        $record->competencyid = $competency->id;
        $record->sortorder = 0;
        $date = new DateTime();
        $record->timecreated =  $date->getTimestamp();
        $record->timemodified = $date->getTimestamp();
        $record->usermodified = $USER->id;
        $DB->insert_record('competency_plancomp',$record,false);
    }

}