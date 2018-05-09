<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 26/04/18
 * Time: 14:19
 */

class view_competency_iena_info
{
    public function get_content($courseid, $ref)
    {
        global $COURSE, $CFG, $USER;
        $competency_instance = new block_competency_iena_competency();
        $competencies = $competency_instance->get_competencies_by_courseID($courseid);  // ($COURSE->id);

        $module_instance = new block_competency_iena_module();
        $modules = $module_instance->get_modules_by_courseID($courseid);

        $section_instance = new block_competency_iena_section();
        $sections = $section_instance->get_sections_by_id_course($courseid);

        $link = $CFG->wwwroot . '/blocks/competency_iena/competency_iena_competency_mgmt.php?courseid=' . $COURSE->id;
        $content = false;

        $content .= "
			
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
			<script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js\"></script>
			
			<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css\">
			
			<form action='" . $CFG->wwwroot . "/blocks/competency_iena/competency_iena_competency_mgmt.php?courseid=" . $COURSE->id . "' method='POST'>
				<h1>Demande d'accès pour modification</h1>
				<input type='hidden' value='" . $ref[1]->id . "' id='ref_mod_valid' name='ref_mod_valid'>
				<p style='font-style:italic'>Référentiel : " . $ref[1]->shortname . " </p>
				
				 <p>Voulez-vous demander l'accès pour modifier le référentiel ?</p>
		        <div class='align_center'>
			        <a onclick=\"window.history.go(-1); return false;\"   class='btn btn-secondary' >Annuler</a>
			        <button type='submit'  id='" . $ref[1]->id . "' class='btn btn-success' style='margin-left: 2rem '>Oui</button>
				</div>
			
			</form>";

        return $content;
    }
}