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
 *  block_competency_iena_ressource
 *
 *
 * @package    filter_iena
 * @copyright  2018 Softia/Université lorraine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_competency_iena_ressource {
    
    /** @var int Id of ressource */
    public $id;
    /** @var string name of ressource */
    public $name;
    /** @var string link of ressource */
    public $link;
    /** @var string type of ressource */
    public $type;
    /** @var int module id of ressource */
    public $module;
    /** @var string intro of ressource */
    public $descrition;
    /** @var block_competency_iena_section section of ressource */
    public $section;

    public $completion;

    /**
     * Set $id, $name, $type
     * @param array $id_course_modules
     *
     * @return void
     */
    public function get_ressource_by_id($id_course_modules) {

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
            $this->section = new block_competency_iena_section();
            $this->section->get_section_by_id_section($requete->section);
            $this->completion = $requete->completion;
            $this->create_link();
        }
    }
    
    /**
     * Get all ressources in a section
     * return a array
     * @param array $id_section
     *
     * @return array<block_competency_iena_ressource> $ressources
     */
    public function get_ressources_by_id_section($id_section)
    {
        global $DB;
        $requete = $DB->get_records_sql('SELECT id FROM {course_modules} WHERE section = ? AND deletioninprogress = 0', array($id_section));
        $ressources = array();
        $i = 0;
        foreach ($requete as $value) {
            $ressource = new block_competency_iena_ressource();
            $ressource->get_ressource_by_id($value->id);
            $ressources[$i] = $ressource;
            $i++;
        }
        
       return $ressources;
    }
    
    /**
     * Create and SET ($link) a correct link with $CFG->wwwroot, $type and $id
     * @param void
     *
     * @return void
     */
    private function create_link(){
       
        global $CFG;
        $this->link = $CFG->wwwroot.'/mod/'.$this->type.'/view.php?id='.$this->id;
    }
    
}
