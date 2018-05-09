<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 15/03/18
	 * Time: 16:22
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
	 * sync_task_iena_message lunch cron_hide_section()
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	namespace format_iena\task;
	
	class sync_task_iena_hide extends \core\task\scheduled_task
	{
		/**
		 * Get a descriptive name for this task (shown to admins).
		 *
		 * @return string
		 */
		
		public function get_name()
		{
			return "task_iena_hide_section";
		}
		
		
		public function execute()
		{
			global $CFG;
			require_once($CFG->dirroot . '/course/format/iena/entity/course_format_iena_cron_action.php');
			$cron_test = new \course_format_iena_cron_action();
			echo 'cron_hide_section start';
			$cron_test->cron_hide_section();
			echo 'cron_hide_section stop';
		}
		
	}