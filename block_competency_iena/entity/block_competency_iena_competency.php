<?php
	
	class block_competency_iena_competency
	{
		
		public $id;
		public $shortname;
		public $description;
		// array<Object> students
		public $students;
		// propriétés d'une compétence uniquement pour la compétence d'un étudiant//
		public $student_proficiency;
		public $student_grade;
		// propriété d'une compétence uniquement pour la compétence d'un module
		// 0, 1, 2 ou 3
		public $module_ruleoutcome;
		public $module_ruleoutcomestring;
		
		//  (0:Ne rien faire, 1:Joindre une preuve, 2:Envoyer pour validation, 3:Marquer la compétence comme atteinte)
		
		
		public function get_competency_by_id($id_competency)
		{
			global $DB;
			try {
				$competency = $DB->get_record_sql('select id, shortname, description  FROM {competency} WHERE id = ?', array($id_competency));
			} catch (dml_exception $e) {
			}
			$this->id = $id_competency;
			$this->shortname = $competency->shortname;
			$this->description = $competency->description;
		}
		
		public function get_competency_by_id_AND_moduleID($id_competency, $id_module)
		{
			global $DB;
			try {
				$competency = $DB->get_record_sql('select c.id, c.shortname, c.description, cm.ruleoutcome
	                                            FROM {competency} as c
	                                            inner join {competency_modulecomp} as cm on cm.competencyid = c.id
	                                            WHERE c.id = ? AND cm.cmid = ? ', array($id_competency, $id_module));
			} catch (dml_exception $e) {
			}
			$this->id = $id_competency;
			$this->shortname = $competency->shortname;
			$this->description = $competency->description;
			$this->module_ruleoutcome = $competency->ruleoutcome;
			switch ($competency->ruleoutcome) {
				case 0:
					$this->module_ruleoutcomestring = 'Ne rien faire';
					break;
				case 1:
					$this->module_ruleoutcomestring = 'Joindre une preuve';
					break;
				case 2:
					$this->module_ruleoutcomestring = 'Envoyer pour validation';
					break;
				case 3:
					$this->module_ruleoutcomestring = 'Marquer la compétence comme atteinte';
					break;
			}
			
		}
		
		public function get_competency_by_id_AND_userID($id_competency, $id_student)
		{
			global $DB;
			try {
				$competency = $DB->get_record_sql('select c.id, c.shortname, c.description, cu.proficiency, cu.grade
	                                            FROM {competency} as c
	                                            inner join {competency_usercompcourse} as cu on cu.competencyid = c.id
	                                            WHERE c.id = ? AND cu.userid = ?', array($id_competency, $id_student));
			} catch (dml_exception $e) {
			}
			
			$this->id = $id_competency;
			$this->shortname = $competency->shortname;
			$this->description = $competency->description;
			$this->student_proficiency = $competency->proficiency;
			$this->student_grade = $competency->grade;
		}
		
		//   retourne la liste des compétences (avec grade et proficiency) pour un étudiant
		public function get_competencies_by_userID($id_student)
		{
			global $DB, $COURSE;
			$competencies_id = $DB->get_records_sql('select competencyid FROM {competency_usercompcourse} WHERE userid = ?', array($id_student));
			$competencies = array();
			$i = 0;
			foreach ($competencies_id as $row) {
				try {
					$is_existe = $DB->get_record_sql('select count(*) as count FROM {competency_coursecomp} WHERE competencyid = ?
	            AND courseid = ?', array($row->competencyid, $COURSE->id));
				} catch (dml_exception $e) {
				}
				if ($is_existe->count == 0) {
					continue;
				}
				$competency = new block_competency_iena_competency();
				$competency->get_competency_by_id_AND_userID($row->competencyid, $id_student);
				$competencies[$i] = $competency;
				$i++;
			}
			return $competencies;
		}
		
		// retourne la liste des compétences pour un module avec les (grade et proficiency pour un étudiant)
		public function get_competencies_by_userID_and_moduleID($id_student, $id_module)
		{
			global $DB;
			try {
				$competencies_id = $DB->get_records_sql('select ucc.competencyid FROM {competency_usercompcourse} as ucc
	                                                    inner join {competency} as c on c.id = ucc.competencyid
	                                                    inner join {competency_modulecomp} as mc on mc.competencyid = c.id
	                                                    WHERE ucc.userid = ? AND mc.cmid =?', array($id_student, $id_module));
			} catch (dml_exception $e) {
			}
			
			$competencies = array();
			$i = 0;
			foreach ($competencies_id as $row) {
				$competency = new block_competency_iena_competency();
				$competency->get_competency_by_id_AND_userID($row->competencyid, $id_student);
				$competencies[$i] = $competency;
				$i++;
			}
			
			return $competencies;
		}
		
		//   retourne la liste des compétences pour un cours
		public function get_competencies_by_courseID($id_course)
		{
			global $DB;
			try {
				$competencies_id = $DB->get_records_sql('select competencyid FROM {competency_coursecomp} WHERE courseid = ?', array($id_course));
			} catch (dml_exception $e) {
			}
			$competencies = array();
			$i = 0;
			foreach ($competencies_id as $row) {
				$competency = new block_competency_iena_competency();
				$competency->get_competency_by_id($row->competencyid);
				$competencies[$i] = $competency;
				$i++;
			}
			
			return $competencies;
		}
		
		
		//  retourne la liste des compétences pour un module
		public function get_competencies_by_moduleID($id_module)
		{
			global $DB;
			try {
				$competencies_id = $DB->get_records_sql('select competencyid FROM {competency_modulecomp} WHERE cmid = ?', array($id_module));
			} catch (dml_exception $e) {
			}
			
			$competencies = array();
			$i = 0;
			foreach ($competencies_id as $row) {
				$competency = new block_competency_iena_competency();
				$competency->get_competency_by_id_AND_moduleID($row->competencyid, $id_module);
				$competencies[$i] = $competency;
				$i++;
			}
			
			return $competencies;
		}
		
		public function get_data($userID, $competenceId, $courseID)
		{
			global $CFG;
			$apiComp = new \tool_lp\output\user_competency_summary_in_course($userID, $competenceId, $courseID);
			try {
				$data = $apiComp->export_for_template(new renderer_base(new moodle_page(), 'autre'));
			} catch (invalid_parameter_exception $e) {
			}
			return $data;
		}
		
		
		public function get_status_by_competenceid($idcompetence, $iduser)
		{
			global $DB;
			try {
				$status = $DB->get_record_sql('select * FROM {competency_usercomp}
	                  WHERE competencyid = ? AND userid = ?', array($idcompetence, $iduser));
			} catch (dml_exception $e) {
			}
			$value = "";
			if ($status->status == 1) {
				$value = "En attente de validation";
			} else if ($status->status == 2) {
				$value = "En cours de validation";
			}
			
			return $value;
		}
		
		
	}
