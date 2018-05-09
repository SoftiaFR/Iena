<?php

class block_competency_iena_module {
    
    public $moduleid;
 
    public $name;
    
    public $courseid;
    
    public $sectionid;
     
    // array<Object> competencies
    public $competencies;
    
    
    public function get_module_by_id($id_module)
    {
        global $COURSE;
        $modinfo = get_fast_modinfo($COURSE->id);
        foreach ($modinfo->get_cms() as $module) 
        {
            if($module->id == $id_module)
            {
                $this->moduleid = $module->id;
                $this->name = $module->name;
                $this->courseid = $module->course;
                $this->sectionid = $module->section; //$module->sectionnum
            }  
        }     
    }
    
    // Récupère les modules pour une compétence
    public function get_modules_by_competencyID($id_competence)
    {
        global $DB;
        $modules_id = $DB->get_records_sql('select cmid FROM {competency_modulecomp} WHERE competencyid = ?', array($id_competence));
        
        $modules = array();
        $i=0;
        foreach($modules_id as $row)
        {
            $module = new block_competency_iena_module();
            $module->get_module_by_id($row->cmid);
            $modules[$i] = $module;
            $i++;
        }
        
        return $modules; 
    }


    /*public function get_modules_by_competencyID_courseID_userID($id_competency,$id_course,$id_user)
    {
        global $DB;
        $modules_id = $DB->get_records_sql('select cmid FROM {competency_modulecompcourse} WHERE competencyid = ?
         AND userid = ?  AND courseid = ?', array($id_competency, $id_course, $id_user));

        $modules = array();
        $i=0;
        foreach($modules_id as $row)
        {
            $module = new block_competency_iena_module();
            $module->get_module_by_id($row->cmid);
            $modules[$i]=$module;
            $i++;
        }

        return $modules;
    }*/

    // Récupère les modules pour un cours
    public function get_modules_by_courseID($id_course)
    {
        global $DB;
        $modules_id = $DB->get_records_sql('select id FROM {course_modules} WHERE course = ? AND deletioninprogress =0', array($id_course));
        
        $modules = array();
        $i=0;
        foreach($modules_id as $row)
        {
            $module = new block_competency_iena_module();
            $module->get_module_by_id($row->id);
            $modules[$i]=$module;
            $i++;
        }
        
        return $modules; 
    }     
      
    
    // Retourne le nombre de modules dont les compétences sont acquises
    public function get_nb_modules_competency_on($id_student)  // $id_course ?
    {
        global $COURSE;
        
        // Récupération des modules du cours
        $modules = $this->get_modules_by_courseID($COURSE->id);
    
        
        $nb_modules_competency_on=0;
        $compteur=0;
        foreach($modules as $module)
        {
            // Récupération des compétences (avec grade et proficiency) pour chaque module
            $instance_competencies =new block_competency_iena_competency();
            $module->competencies = $instance_competencies->get_competencies_by_userID_and_moduleID($id_student, $module->moduleid);
            
            // vérifier si compétences acquises pour le module et compteur
            foreach($module->competencies as $competency)
            {
                if($competency->student_proficiency == 1)
                {
                    $compteur =1; }  else {
                    $compteur =0 ; 
                    break;
                }
            }
            $nb_modules_competency_on += $compteur;
        }
        return $nb_modules_competency_on;
    }


    public function get_completions_by_module($userId, $courseID,$moduleID)
    {
        global $DB;
        $modules_completion_infos = $DB->get_record_sql('SELECT cmc.id, cmc.coursemoduleid, cmc.userid, cmc.completionstate         
                                                  FROM  {course_modules_completion} as cmc
                                                  inner join {course_modules} as cm on cm.id = cmc.coursemoduleid
                                                  inner join {user} as u on u.id = cmc.userid
                                                  inner join {modules} as m on m.id = cm.module
                                                  where cm.course = ? and cm.deletioninprogress = 0
                                                  and cmc.userid = ?
                                                  and cmc.coursemoduleid = ?
                                                  order by section, coursemoduleid asc', array($courseID,$userId,$moduleID));

        return $modules_completion_infos;
    }

    public function get_nb_module_ok_by_userid_competenceid($competencyid,$userid,$courseid){
        $module_comp = $this->get_modules_by_competencyID($competencyid);
        $nbmoduleOk = 0;
        foreach ($module_comp as $value){
            $completion =  $this->get_completions_by_module($userid,$courseid,$value->moduleid);
            if ($completion->completionstate >= 1){
                $nbmoduleOk++;
            }
        }
        return $nbmoduleOk;
    }
}
