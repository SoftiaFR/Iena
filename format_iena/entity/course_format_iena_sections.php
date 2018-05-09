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
	 * course_format_iena_sections
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille / Michaël Lebeau
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class course_format_iena_sections
	{
		/** @var int id of section */
		public $id;
		/** @var string name of section */
		public $name;
		/** @var int id of course */
		public $id_course;
		/** @var block_career_ressources array<Ressource> ressources */
		public $ressources;
		
		
		/**
		 * @param $id_course
		 * @return array
		 * @throws dml_exception
		 */
		public function get_sections_by_id_course($id_course)
		{
			global $DB;
			$requete = $DB->get_records_sql('SELECT id FROM {course_sections} WHERE course = ?', array($id_course));
			$sections = array();
			$i = 0;
			foreach ($requete as $value) {
				$section = new course_format_iena_sections();
				$section->get_section_by_id_section($value->id);
				$sections[$i] = $section;
				$i++;
			}
			
			return $sections;
		}
		
		
		/**
		 * @param $id_section
		 * @throws dml_exception
		 */
		public function get_section_by_id_section($id_section)
		{
			global $DB;
			$requete = $DB->get_record_sql('SELECT * FROM {course_sections} WHERE id = ?', array($id_section));
			$this->id = $requete->id;
			$this->name = $requete->name;
			$this->id_course = $requete->course;
			
			if (!$this->name) {
				$this->name = get_string('section', 'format_iena') . " " . $requete->section;
			}
		}
		
		/**
		 * @param $id_section
		 * @return mixed
		 * @throws dml_exception
		 */
		public function get_section_settings_by_id_section($id_section)
		{
			global $DB;
			$requete = $DB->get_record_sql('SELECT * FROM {format_iena} WHERE id_section = ?', array($id_section));
			return $requete;
		}
		
		
		/**
		 * @param $id_section
		 * @param $id_user
		 * @return array
		 * @throws dml_exception
		 */
		public function get_completion_by_sectionID_userID($id_section, $id_user)
		{
			global $DB;
			$requete = $DB->get_records_sql('SELECT cmc.id as moduleid, cm.course, cm.section, cmc.userid, cmc.completionstate
                                        FROM {course_modules} as cm
                                        inner join  {course_modules_completion} as cmc on cmc.coursemoduleid = cm.id
                                        WHERE cm.section= ? AND cmc.userid= ?
                                        order by cm.section, cmc.userid', array($id_section, $id_user));
			return $requete;
		}
		
		/**
		 * @param $id_section
		 * @return array
		 * @throws dml_exception
		 */
		public function get_hidden_modules_by_section($id_section)
		{
			global $DB;
			$requete = $DB->get_records_sql('SELECT *
                                        FROM {format_iena_settings}
                                        WHERE sectionid =?', array($id_section));
			return $requete;
		}
		
	}
