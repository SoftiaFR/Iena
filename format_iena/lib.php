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
	 * format_iena
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	defined('MOODLE_INTERNAL') || die();
	require_once($CFG->dirroot . '/course/format/topics/lib.php');
	
	/**
	 * format_iena
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class format_iena extends format_topics
	{
		
		/**
		 * course_format_options
		 *
		 * @param bool $foreditform
		 * @return array
		 */
		public function course_format_options($foreditform = false)
		{
			global $PAGE;
			static $courseformatoptions = false;
			if ($courseformatoptions === false) {
				$courseconfig = get_config('moodlecourse');
				$courseformatoptions['numsections'] = array(
					'default' => $courseconfig->numsections,
					'type' => PARAM_INT,
				);
				$courseformatoptions['viewbreadcrum'] = array(
					'default' => 1,
					'type' => PARAM_INT,
				);
				$courseformatoptions['viewiconmessage'] = array(
					'default' => 1,
					'type' => PARAM_INT,
				);
			}
			if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
				$courseconfig = get_config('moodlecourse');
				$max = $courseconfig->maxsections;
				if (!isset($max) || !is_numeric($max)) {
					$max = 52;
				}
				$sectionmenu = array();
				for ($i = 0; $i <= $max; $i++) {
					$sectionmenu[$i] = "$i";
				}
				$courseformatoptionsedit['numsections'] = array(
					'label' => new lang_string('numberweeks'),
					'element_type' => 'select',
					'element_attributes' => array($sectionmenu),
				);
				$choiceTab = array();
				$choiceTab['1'] = get_string('yes', 'format_iena');
				$choiceTab['0'] = get_string('no', 'format_iena');
				$courseformatoptionsedit['viewbreadcrum'] = array(
					'label' => get_string('hide_bread_crum', 'format_iena'),
					'element_type' => 'select',
					'element_attributes' => array($choiceTab),
				);
				$courseformatoptionsedit['viewiconmessage'] = array(
					'label' => get_string('hide_icon_message', 'format_iena'),
					'element_type' => 'select',
					'element_attributes' => array($choiceTab),
				);
				$courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
			}
			return $courseformatoptions;
		}
		
		/**
		 * get_view_url
		 *
		 * @param int|stdclass $section
		 * @param array $options
		 * @return null|moodle_url
		 */
		public function get_view_url($section, $options = array())
		{
			global $CFG;
			$course = $this->get_course();
			$url = new moodle_url('/course/view.php', array('id' => $course->id));
			
			$sr = null;
			if (array_key_exists('sr', $options)) {
				$sr = $options['sr'];
			}
			if (is_object($section)) {
				$sectionno = $section->section;
			} else {
				$sectionno = $section;
			}
			if ($sectionno !== null) {
				if ($sr !== null) {
					if ($sr) {
						$usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
						$sectionno = $sr;
					} else {
						$usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
					}
				} else {
					$usercoursedisplay = 0;
				}
				if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
					$url->param('section', $sectionno);
				} else {
					if (empty($CFG->linkcoursesections) && !empty($options['navigation'])) {
						return null;
					}
					$url->set_anchor('section-' . $sectionno);
				}
			}
			return $url;
		}
	}
	
	/**
	 * Implements callback inplace_editable() allowing to edit values in-place
	 *
	 * @param string $itemtype
	 * @param int $itemid
	 * @param mixed $newvalue
	 * @return \core\output\inplace_editable
	 */
	function format_iena_inplace_editable($itemtype, $itemid, $newvalue)
	{
		global $DB, $CFG;
		require_once($CFG->dirroot . '/course/lib.php');
		if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
			$section = $DB->get_record_sql(
				'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
				array($itemid, 'iena'),
				MUST_EXIST
			);
			return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
		}
	}
