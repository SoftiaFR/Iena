<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 15/03/18
	 * Time: 16:34
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
	 * Set setting for cron
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	defined('MOODLE_INTERNAL') || die();
	
	
	$tasks = array(
		
		array(
			'classname' => 'format_iena\task\sync_task_iena_hide',
			'blocking' => 0,
			'minute' => '*/5',
			'hour' => '*',
			'day' => '*',
			'month' => '*',
			'dayofweek' => '*'
		),
		array(
			'classname' => 'format_iena\task\sync_task_iena_message',
			'blocking' => 0,
			'minute' => '0',
			'hour' => '22',
			'day' => '*/1',
			'month' => '*',
			'dayofweek' => '*'
		)
	
	);