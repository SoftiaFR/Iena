<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 15/03/18
	 * Time: 11:33
	 */

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
	 * Class course_format_iena_cron_action
	 * This class hide and show section with setting present in table format_iena
	 * This class send message with setting present in table format_iena
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille / Michaël Lebeau
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	include('../../lib.php');
	include('../../../../config.php');
	
	
	class course_format_iena_cron_action
	{
		
		/**
		 * @param $requete
		 * @param $nbJours
		 * @throws coding_exception
		 */
		private function hide_section($requete, $nbJours)
		{
			if ($nbJours < 0) {
				set_section_visible($requete->course, $requete->section, 0);
				//Log for task
				echo get_string('hide_section', 'format_iena') . $requete->name;
			} else {
				set_section_visible($requete->course, $requete->section, 1);
				//Log for task
				echo get_string('show_section', 'format_iena') . $requete->name;
			}
		}
		
		/**
		 * @param $date
		 * @return float|int
		 */
		private function calcul_nb_jours($date)
		{
			$date_now = date_create(date("Y-m-d H:i:s"))->getTimestamp();
			$nbJoursTimestamp = $date_now - $date;
			// Calcul number of day
			$nbJours = $nbJoursTimestamp / 86400;
			return $nbJours;
		}
		
		/**
		 * @throws dml_exception
		 */
		public function cron_hide_section()
		{
			global $DB;
			
			$sections = $DB->get_records('format_iena');
			
			foreach ($sections as $section) {
				$requete = $DB->get_record('course_sections', array('id' => $section->id_section));
				
				if ($section->hide == 1) {
					set_section_visible($requete->course, $requete->section, 1);
					echo get_string('show_section', 'format_iena') . $requete->name;
				}
				
				if ($section->hide == 2) {
					$date_rendu = date_create($section->date_rendu);
					//We calcul date with timeStamp
					$date_rendu = $date_rendu->getTimestamp();
					$this->hide_section($requete, $this->calcul_nb_jours($date_rendu));
					
				} else if ($section->hide == 3) {
					if ($section->day_before && $section->nb_days_before) {
						$date_before = date('Y-m-d H:i:s', strtotime($section->date_rendu . " - " . $section->nb_days_before . " days"));
						$date_before = date_create($date_before)->getTimestamp();
						$this->hide_section($requete, $this->calcul_nb_jours($date_before));
					} else if ($section->day_same) {
						$date_rendu = date_create($section->date_rendu);
						$date_rendu = $date_rendu->getTimestamp();
						$this->hide_section($requete, $this->calcul_nb_jours($date_rendu));
					} else if ($section->day_after && $section->nb_days_after) {
						$date_after = date('Y-m-d H:i:s', strtotime($section->date_rendu . " + " . $section->nb_days_after . " days"));
						$date_after = date_create($date_after)->getTimestamp();
						$this->hide_section($requete, $this->calcul_nb_jours($date_after));
					}
				}
				
			}
		}
		
		/**
		 * @throws dml_exception
		 * @throws coding_exception
		 */
		public function cron_message()
		{
			global $DB, $USER, $CFG;
			$sections = $DB->get_records('format_iena');
			foreach ($sections as $section) {
				if ($section->day_before || $section->day_same || $section->day_after) {
					$requete = $DB->get_record('course_sections', array('id' => $section->id_section));
					$date_notif = $this->is_notif($section);
					if ($date_notif == false) {
						continue;
					}
					$this->iena_send_message($requete, $section);
				} else {
					continue;
				}
			}
		}
		
		/**
		 * @param $section
		 * @return bool
		 */
		private function is_notif($section)
		{
			$date_notif = null;
			$date_now = date_create(date("Y-m-d"));
			if ($section->day_before && $section->nb_days_before) {
				$date_notif = date('Y-m-d', strtotime($section->date_rendu . " - " . $section->nb_days_before . " days"));
				$date_notif = date_create($date_notif);
				if ($date_notif == $date_now) {
					return true;
				}
			}
			if ($section->day_same) {
				$date_notif = date_create(date('Y-m-d', strtotime($section->date_rendu)));
				if ($date_notif == $date_now) {
					return true;
				}
			}
			if ($section->day_after && $section->nb_days_after) {
				$date_notif = date('Y-m-d', strtotime($section->date_rendu . " + " . $section->nb_days_after . " days"));
				$date_notif = date_create($date_notif);
				if ($date_notif == $date_now) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * @param $requete
		 * @param $section
		 * @throws coding_exception
		 * @throws dml_exception
		 */
		private function iena_send_message($requete, $section)
		{
			global $DB, $CFG, $USER;
			
			$course_ctx = context_course::instance($requete->course);
			$students = get_enrolled_users($course_ctx);
			$course = $DB->get_record('course', array('id' => $requete->course), '*', MUST_EXIST);
			
			$messageContent = false;
			//Here we can change value
			$messageContent .= "<br> Cour : " . $course->fullname;
			$messageContent .= "<br> Section : " . $requete->name;
			$messageContent .= "<br> Date de la Séance : " . $section->date_rendu;
			$messageContent .= "<br> Lien : <a href='" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . "'>" . $course->fullname . "</a>";
			//Create message
			$message = new \core\message\message();
			$message->component = 'moodle';
			$message->name = 'instantmessage';
			$message->userfrom = $USER;
			$message->notification = '0';
			foreach ($students as $student) {
				echo get_string('snd_msg_to', 'format_iena') . $student->firstname . ' ' . $student->lastname;
				$message->userto = $student;
				$message->smallmessage = $messageContent;
				$message->subject = $messageContent;
				message_send($message);
			}
		}
		
		
	}