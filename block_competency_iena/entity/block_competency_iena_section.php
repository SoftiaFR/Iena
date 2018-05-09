<?php
/**
 * The iena filter plugin transforms the moodle resource links
 * into a button that opens the resource in a modal
 * 
 * @package    block_competency_iena
 * @category   block
 * @copyright  2018 Softia/Université lorraine
 * @author     vrignaud camille/ faouzi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * block_competency_iena_section
 *
 *
 * @package    filter_iena
 * @copyright  2018 Softia/Université lorraine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_competency_iena_section {
    
    /** @var int Id of section */
    public $id;
    /** @var string name of section */
    public $name;
    /** @var int id of course */
    public $id_course;
    /** @var block_competency_iena_ressources array<Object> ressources*/
    public $ressources;
     /** @var string summary*/
    public $summary;
    /** @var int order of section*/
    public $orde;
    /** @var string intro of section*/
    public $intro;
    
    /**
     * Set $id, $name, $id_course and $summary
     * @param array $id_section
     *
     * @return void
     */
    public function get_section_by_id_section($id_section)
    {
        global $DB;
        $requete = $DB->get_record_sql('SELECT * FROM {course_sections} WHERE id = ?', array($id_section));
        $this->id = $requete->id;
        $this->name = $requete->name;
        $this->id_course = $requete->course;
        $this->summary = $requete->summary;
        $this->orde = $requete->section;
        $this->intro = $requete->summary;

        if (!$this->name)
        {
            $this->name = "Section ".$requete->section;
        }
    }
    
    /**
     * Get all sections in a course
     * return a array
     * @param array $id_course
     *
     * @return array<block_competency_iena_section> $sections
     */
    public function get_sections_by_id_course($id_course)
    {
        global $DB;
        $requete = $DB->get_records_sql('SELECT id FROM {course_sections} WHERE course = ?', array($id_course));
        $sections = array();
        $i = 0;
        foreach ($requete as $value) {
            $section = new block_competency_iena_section();
            $section->get_section_by_id_section($value->id);
            $sections[$i] = $section;
            $i++;
        }
        
       return $sections;
    }
    
}
