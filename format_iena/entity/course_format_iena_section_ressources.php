<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
	
	/**
	 *
	 * course_format_iena_section_ressources
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class course_format_iena_section_ressources
	{
		/** @var int Id of ressource */
		public $id;
		/** @var string name of ressource */
		public $name;
		/** @var string type of ressource */
		public $type;
		/** @var int module id of ressource */
		public $module;
		/** @var string intro of ressource */
		public $descrition;
		/** @var course_format_iena_section_ressources section */
		public $section;
		/** @var module hide indicator (from table format_iena_settings) */
		public $ressource_hide_indic;
		
		
		/**
		 * @param $id_module
		 * @return mixed
		 * @throws dml_exception
		 */
		public function get_ressource_hide_indicator($id_module)
		{
			global $DB;
			$module_state = $DB->get_record_sql('SELECT hide from {format_iena_settings} where cmid = ?', array($id_module));
			
			return $module_state->hide;
		}
		
		/**
		 * @param $id_section
		 * @return array
		 * @throws dml_exception
		 */
		public function get_ressources_by_id_section($id_section)
		{
			global $DB;
			
			$requete = $DB->get_records_sql('SELECT id FROM {course_modules} WHERE section = ? AND deletioninprogress = 0', array($id_section));
			$ressources = array();
			$i = 0;
			foreach ($requete as $value) {
				$ressource = new course_format_iena_section_ressources();
				$ressource->get_ressource_by_id($value->id);
				$ressources[$i] = $ressource;
				$i++;
			}
			
			return $ressources;
		}
		
		/**
		 * @param $id_section
		 * @return array
		 * @throws dml_exception
		 */
		public function get_ressources_completion_on_by_id_section($id_section)
		{
			global $DB;
			
			$requete = $DB->get_records_sql('SELECT id FROM {course_modules} WHERE section = ? AND deletioninprogress = 0
        AND completion != 0', array($id_section));
			$ressources = array();
			$i = 0;
			foreach ($requete as $value) {
				$ressource = new course_format_iena_section_ressources();
				$ressource->get_ressource_by_id($value->id);
				$ressources[$i] = $ressource;
				$i++;
			}
			
			return $ressources;
		}
		
		
		/**
		 * @param $id_course_modules
		 * @throws dml_exception
		 */
		public function get_ressource_by_id($id_course_modules)
		{
			
			global $DB;
			if ($id_course_modules) {
				$this->id = $id_course_modules;
				$requete = $DB->get_record_sql('SELECT * FROM {course_modules} WHERE id = ? AND deletioninprogress = 0', array($id_course_modules));
				$id_instance = $requete->instance;
				$id_module = $requete->module;
				if ($id_module) {
					$modules = $DB->get_record_sql('SELECT * FROM {modules} WHERE id = ?', array($id_module));
				}
				if ($modules->name) {
					$instance = $DB->get_record_sql('SELECT * FROM {' . $modules->name . '} WHERE id = ?', array($id_instance));
				}
				if ($instance->name) {
					$this->name = $instance->name;
				}
				$this->descrition = $instance->intro;
				$this->type = $modules->name;
				$this->module = $modules->id;
				// $this->section = new course_format_iena_section_ressources();
				$this->section = new course_format_iena_sections();
				$this->section->get_section_by_id_section($requete->section);
			}
		}
		
		/**
		 * @param $userId
		 * @param $courseID
		 * @return array
		 * @throws dml_exception
		 */
		public function get_completions_by_userid($userId, $courseID)
		{
			global $DB;
			$modules_completion_infos = $DB->get_records_sql('SELECT cmc.id, cmc.coursemoduleid, cmc.userid, cmc.completionstate
                                                  FROM  {course_modules_completion} as cmc
                                                  inner join {course_modules} as cm on cm.id = cmc.coursemoduleid
                                                  inner join {user} as u on u.id = cmc.userid
                                                  inner join {modules} as m on m.id = cm.module
                                                  where cm.course = ? and cm.deletioninprogress = 0
                                                  and cmc.userid = ?
                                                  order by section, coursemoduleid asc', array($courseID, $userId));
			
			return $modules_completion_infos;
		}
		
		/**
		 * @param $userId
		 * @param $courseID
		 * @param $moduleID
		 * @return mixed
		 * @throws dml_exception
		 */
		public function get_completions_by_module($userId, $courseID, $moduleID)
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
                                                  order by section, coursemoduleid asc', array($courseID, $userId, $moduleID));
			
			return $modules_completion_infos;
		}
		
	}
