<?php

class block_competency_iena_student {
    
    public $studentid;
    
    public $firstname;
        
    public $lastname;
    
    // array<Object> competencies pour un cours (OU pour un module ?)
    public $competencies;
    
    
    public function get_students_by_competencyID($id_competency)
    {
        global $DB;
        $students_id = $DB->get_records_sql('select userid FROM {competency_usercompcourse} WHERE competencyid = ?', array($id_competency));
        $students = array();
        $i=0;
        foreach($students_id as $row)
        {
            $student = new block_competency_iena_student();
            $student->get_student_by_id($row->userid);
            $students[$i]=$student;
            $i++;
        }
        
        return $students;
    }
    
    
    public function get_student_by_id($id_student)
    {
        global $DB;
        $student = $DB->get_record_sql('select * FROM {user} WHERE id = ?', array($id_student));

        $this->studentid = $student->id;
        $this->firstname = $student->firstname;
        $this->lastname = $student->lastname;
        
    }

    public function get_all_students_by_course($id_course){
        $c1ctx = context_course::instance($id_course);
        $students = get_enrolled_users($c1ctx);
        return $students;
    }
    

    
  
    
}
