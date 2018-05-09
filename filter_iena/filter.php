<?php

// $id$
//////////////////////////////////////////////////////////////
// 
//  This filter allows making calls to moodle.
//
//////////////////////////////////////////////////////////////
/// This is the filtering function itself.  It accepts the 
/// courseid and the text to be filtered (in HTML form).
	/**
	 * The iena filter plugin transforms the moodle resource links
	 * into a button that opens the resource in a modal
	 *
	 * @package    filter_iena
	 * @category   filter
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	
	/**
	 * filter_iena
	 *
	 *
	 * @package    filter_iena
	 * @copyright  2018 Softia/Université lorraine
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class filter_iena extends moodle_text_filter
	{
		
		/** @var string This variable is used for delemiter */
		public $start = "[IENA]";
		/** @var string This variable is used for delemiter */
		public $end = "[/IENA]";
		/** @var string Contain button name, the button name is
		 * automatically the name of resource */
		public $btn_name;
		/** @var string Contain the type of button */
		public $btn_type;
		/** @var string Hex color of button is defined in settings ($CFG->color_btn) */
		public $color_btn = "#009487";
		/** @var string set empty, if the id does not exist then turn to "disabled" */
		public $disabled = "";
		
		/**
		 * Get name and type of resource with the id
		 * Set the $btn_name and $btn_type
		 * Set $disabled (if $disabled is empty the button is active)
		 * @param array $id_btn
		 *
		 * @return void
		 */
		public function filter_iena_get_info($id_btn)
		{
			
			global $DB;
			$this->btn_name = get_string('erreur_ressource', 'filter_iena');
			$this->disabled = "";
			$id_course_modules = $id_btn[0][0];
			if ($id_course_modules) {
				$requete = $DB->get_record_sql('SELECT * FROM {course_modules} WHERE id = ?', array($id_course_modules));
				$id_instance = $requete->instance;
				$id_module = $requete->module;
				if ($id_module) {
					$modules = $DB->get_record_sql('SELECT * FROM {modules} WHERE id = ?', array($id_module));
				}
				if ($modules->name) {
					$instance = $DB->get_record_sql('SELECT * FROM {' . $modules->name . '} WHERE id = ?', array($id_instance));
				}
				if ($instance->name) {
					$this->btn_name = $instance->name;
				} else {
					$this->disabled = "disabled";
				}
				
				$this->btn_type = $modules->name;
			}
		}
		
		/**
		 * add $start and $end on each ressource link
		 * @param array $pieces
		 *
		 * @return array $pieces
		 */
		public function filter_iena_add_delimiters($pieces)
		{
			
			global $CFG;
			
			for ($i = 1; $i < count($pieces); $i++) {
				// If the link is in iframe then it is not tranformer
				if ((strpos($pieces[$i], '</iframe>') !== false)) {
					$pieces[$i] = $CFG->wwwroot . '/mod' . $pieces[$i];
					continue;
				}
				if ((strpos($pieces[$i], '</a>') !== false)) {
					$pieces[$i] = $CFG->wwwroot . '/mod' . $pieces[$i];
					continue;
				}
				$pieces[$i] = $this->start . $CFG->wwwroot . '/mod' . $pieces[$i];
				$temp = strpos($pieces[$i], "id=") + 3;
				$nb_temp = '';
				while (is_numeric($pieces[$i][$temp])) {
					$nb_temp = $nb_temp . $pieces[$i][$temp];
					$temp++;
				}
				$pieces[$i] = substr_replace($pieces[$i], $nb_temp . $this->end, strpos($pieces[$i], "id=") + 3, strlen($nb_temp));
				$pieces[$i] = str_replace('&amp;iframe=true', '', $pieces[$i]);
				$pieces[$i] = str_replace('&amp;iframe=false', '', $pieces[$i]);
				$pieces[$i] = str_replace('&iframe=true', '', $pieces[$i]);
				$pieces[$i] = str_replace('&iframe=false', '', $pieces[$i]);
			}
			return $pieces;
		}
		
		/**
		 * @param $text
		 * @param array $options
		 * @return string
		 */
		function filter($text, array $options = array())
		{
			
			global $CFG;
			global $PAGE;
			
			if ($CFG->color_btn) {
				$this->color_btn = $CFG->color_btn;
			}

//			$PAGE->requires->js('/filter/iena/js/jquery-3.3.1.min.js');
			//$PAGE->requires->js('/filter/iena/js/iena.js');
			//We hide the menus and block if the iframe parameter is a true
			if (isset($_GET['iframe'])) {
				if ($_GET['iframe'] == 'true') {
					$PAGE->requires->js('/filter/iena/js/iframe_true.js');
				}
			}
			
			preg_match_all('/<a href="(.*?)">(.*?)<\/a>/s', $text, $matches);
			
			for ($i = 0; $i < count($matches[0]); $i++) {
				if (strcmp($matches[1][$i], $matches[2][$i]) == 0) {
					$text = str_replace($matches[0][$i], $matches[1][$i], $text);
				}
			}
			
			$pieces = explode($CFG->wwwroot . '/mod', $text);
			$pieces = $this->filter_iena_add_delimiters($pieces);
			
			for ($i = 0; $i < count($pieces); $i++) {
				$pieces[$i] = ' ' . $pieces[$i];
				$ini = strpos($pieces[$i], $this->start);
				$ini += strlen($this->start);
				if (strlen($pieces[$i]) <= $ini) {
					continue;
				}
				$len = strpos($pieces[$i], $this->end, $ini) - $ini;
				$parsed = substr($pieces[$i], $ini, $len);
				preg_match_all('/id=[\d]*/', $parsed, $matches);
				if ($matches[0]) {
					preg_match_all('/[\d]+/', $matches[0][0], $id_btn);
				}
				
				if (isset($id_btn[0])) {
					$this->filter_iena_get_info($id_btn);
				}
				$pieces[$i] = preg_replace("/(\\S+)\\[\/IENA\\]/", "<button type=\"button\" " . $this->disabled . " "
					. "class=\"btn \" data-toggle=\"modal\" data-target=\".modal" . $i . "\" style=\"background-color : " . $this->color_btn . "; "
					. "color : #FFF;\">"
					. "<img class=\"icon icon\" alt=\"\" src=\"" . $CFG->wwwroot . "/theme/image.php/boost/" . $this->btn_type . "/1/icon\">"
					. "" . $this->btn_name . " </button>"
					. "<div class=\"modal fade bd-example-modal-lg modal" . $i . " \" tabindex=\"-1\" "
					. "role=\"dialog\" aria-labelledby=\"myLargeModalLabel\" aria-hidden=\"true\">"
					. "<div class=\"modal-dialog modal-lg\">"
					. "<div class=\"modal-content\" id=\"iena-modal-content\" >"
					. "<div class=\"modal-header\">"
					. "<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>"
					. "<h4 class=\"modal-title\">"
					. "<img class=\"icon icon\" alt=\"\" src=\"" . $CFG->wwwroot . "/theme/image.php/boost/" . $this->btn_type . "/1/icon\">" . $this->btn_name . "</h4>"
					. "</div>"
					. "<div class=\"modal-body\" id=\"iena-modal-body\">"
					. "<iframe width=\"1048\" height=\"800\" src=\"" . $parsed . "&iframe=true\" frameborder=\"0\" allowfullscreen></iframe>"
					. "</div>"
					. "</div>"
					. "</div>"
					. "</div>"
					, $pieces[$i]);
			}
			return implode($pieces);
		}
		
	}
