<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 03/04/18
	 * Time: 15:38
	 */
	
	class block_competency_iena_referentiel
	{
		
		public $level;
		public $children;
		public $competency;
		
		
		public function fill($level, $children, $competency)
		{
			$this->level = $level;
			$this->children = $children;
			$this->competency = $competency;
		}
		
		public function add_competency_in_course($idcompetency, $courseid)
		{
			global $DB, $USER;
			try {
				$requete = $DB->get_record_sql('select count(*) as count FROM {competency_coursecomp}
		              WHERE competencyid = ? AND courseid = ? ', array($idcompetency, $courseid));
			} catch (dml_exception $e) {
			}
			
			try {
				$max = $DB->get_record_sql('select MAX(sortorder) as max FROM {competency_coursecomp}
	                  WHERE courseid = ? ', array($courseid));
			} catch (dml_exception $e) {
			}
			
			$max = $max->max + 1;
			if ($requete->count != 0) {
				return false;
			}
			$record = new stdClass();
			$record->courseid = $courseid;
			$record->competencyid = $idcompetency;
			$record->ruleoutcome = 1;
			$date = new DateTime();
			$record->timemodified = $date->getTimestamp();
			$record->timecreated = $date->getTimestamp();
			$record->usermodified = $USER->id;
			$record->sortorder = $max;
			try {
				$DB->insert_record('competency_coursecomp', $record, false);
			} catch (dml_exception $e) {
			}
			return true;
		}
		
		public function get_referentiels_by_course_id($courseid)
		{
			global $DB;
			try {
				$requete = $DB->get_records_sql('select * FROM {competency_coursecomp} WHERE courseid = ?', array($courseid));
			} catch (dml_exception $e) {
			}
			$tab_ref = array();
			foreach ($requete as $value) {
				try {
					$requete = $DB->get_record_sql('select * FROM {competency} WHERE id = ?', array($value->competencyid));
				} catch (dml_exception $e) {
				}
				array_push($tab_ref, $requete->competencyframeworkid);
			}
			$tab_ref = array_unique($tab_ref);
			$return_tab = array();
			foreach ($tab_ref as $ref) {
				try {
					$requete = $DB->get_record_sql('select * FROM {competency_framework} WHERE id = ?', array($ref));
				} catch (dml_exception $e) {
				}
				$return_tab[$ref] = $requete;
			}
			return $return_tab;
		}
		
		
		public function get_referentiels_all()
		{
			global $DB;
			$return_tab = array();
			$requete = $DB->get_records_sql('select * FROM {competency_framework}');
			foreach ($requete as $ref) {
				$return_tab[$ref->id] = $ref;
			}
			return $return_tab;
		}
		


		public function get_referentiels_informations($id)
		{
			global $DB;
			try {
				$requete = $DB->get_records_sql('select * FROM {competency_framework} WHERE id = ?', array($id));
			} catch (dml_exception $e) {
			}
			$tab_ref = array();
			
			
			return $requete;
		}
		
		public function get_competences_by_refs($tab_refs)
		{
			global $DB;
			$return_tab = array();
			foreach ($tab_refs as $ref) {
				try {
					$requete = $DB->get_records_sql('select * FROM {competency} WHERE competencyframeworkid = ?', array($ref->id));
				} catch (dml_exception $e) {
				}
				$tab_comp = array();
				foreach ($requete as $competence) {
					$compI = new block_competency_iena_competency();
					$compI->get_competency_by_id($competence->id);
					array_push($tab_comp, $compI);
				}
				$return_tab[$ref->id] = $tab_comp;
			}
			return $return_tab;
		}
		
		public function get_info_framework_by_id($id)
		{
			global $DB;
			try {
				$framework = $DB->get_record_sql('select * FROM {competency_framework} WHERE id = ?', array($id));
			} catch (dml_exception $e) {
			}
			return $framework;
		}
		
		public function get_info_framework($id)
		{
			global $DB;
			try {
				$framework = $DB->get_record_sql('select * FROM {competency_framework} WHERE id = ?', array($id));
			} catch (dml_exception $e) {
			}
			return $framework;
		}

		
		public function get_competences_order_by_ref($refid)
		{
			global $DB;
			try {
				$competences = $DB->get_records_sql('select * FROM {competency} WHERE competencyframeworkid = ?', array($refid));
			} catch (dml_exception $e) {
			}
			$lvlOne = array();
			foreach ($competences as $competence) {
				if ($competence->parentid == 0) {
					$object = new block_competency_iena_referentiel();
					$object->fill(1, null, $competence);
					array_push($lvlOne, $object);
				}
			}
			
			foreach ($lvlOne as $one) {
				$lvlTwo = array();
				foreach ($competences as $competence) {
					if ($one->competency->id == $competence->parentid) {
						$object = new block_competency_iena_referentiel();
						$object->fill(2, null, $competence);
						array_push($lvlTwo, $object);
					}
				}
				$one->children = $lvlTwo;
				foreach ($one->children as $two) {
					$lvlTree = array();
					foreach ($competences as $competence) {
						if ($two->competency->id == $competence->parentid) {
							$object = new block_competency_iena_referentiel();
							$object->fill(3, null, $competence);
							array_push($lvlTree, $object);
						}
					}
					$two->children = $lvlTree;
					foreach ($two->children as $tree) {
						$lvlFour = array();
						foreach ($competences as $competence) {
							if ($tree->competency->id == $competence->parentid) {
								$object = new block_competency_iena_referentiel();
								$object->fill(4, null, $competence);
								array_push($lvlFour, $object);
							}
						}
						$tree->children = $lvlFour;
					}
				}
			}
			
			return $lvlOne;
			
		}
		
	}