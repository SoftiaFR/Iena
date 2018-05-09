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
	class course_format_iena_groups
	{
		/** @var int id of group */
		public $id;
		/** @var int id of course */
		public $id_course;
		/** @var string idnumber of group */
		public $idnumber;
		/** @var string name of group */
		public $name;
		/** @var string description of group */
		public $description;

		/** @var array<userid>  */
		public $list_userid;
		
		
		/**
		 * @param $id_course
		 * @return array
		 * @throws dml_exception
		 */
		public function get_groups_by_id_course($id_course)
		{
			global $DB;
			$requete = $DB->get_records_sql('SELECT id FROM {groups} WHERE courseid = ?', array($id_course));
			$groups = array();
			$i = 0;
			foreach ($requete as $value) {
				$group = new course_format_iena_groups();
				$group->get_group_by_id_group($value->id);
				$groups[$i] = $group;
				$i++;
			}
			
			return $groups;
		}
		
		
		/**
		 * @param $id_section
		 * @throws dml_exception
		 */
		public function get_group_by_id_group($id_group)
		{
			global $DB;
			$requete = $DB->get_record_sql('SELECT * FROM {groups} WHERE id = ?', array($id_group));
			$this->id = $requete->id;
                        $this->id_course = $requete->courseid;
			$this->idnumber = "id_groupe".$requete->id;
			$this->name = $requete->name;
                        $this->description = $requete->description;
                        $this->list_userid = $DB->get_records_sql('SELECT userid FROM {groups_members} WHERE groupid = ?', array($id_group));
			
		}
                
                /**
                 * 
                 * @gparam $id_course
                 * @return array
                 */
                public function get_students_group($id_course)
                {
                    global $DB;
                    $students_group = $DB->get_records_sql('SELECT gm.userid, gm.groupid, g.idnumber FROM {groups} as g 
                                               inner join {groups_members} as gm on gm.groupid=g.id 
                                               WHERE g.courseid = ?', array($id_course));
                    return $students_group;
                }
		
	}
