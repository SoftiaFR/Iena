<?php
	
	class view_competency_iena_competencies
	{
		
		public function get_content($studentid)
		{
			
			global $COURSE, $USER, $CFG;
			
			$studentI = new block_competency_iena_student();
			$students = $studentI->get_all_students_by_course($COURSE->id);
			$studentI->get_student_by_id($studentid);

			$content = false;
			$link = $CFG->wwwroot . '/blocks/competency_iena/competency_iena_competencies.php?courseid=' . $COURSE->id . '&studentid=';
			$content .= "
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css\">
    
        <h3>$studentI->lastname $studentI->firstname </h3>
        
        ";
			
			if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
				$content .= "
    <div class='centered'>
    <span class=\"myarrow\"><input list=\"browsers\" id=\"myBrowser\" name=\"myBrowser\" placeholder='$studentI->lastname $studentI->firstname' /></span>
    <datalist id=\"browsers\">
    ";
				foreach ($students as $item) {
					$content .= "<option data-value='$link$item->id'  value='$item->lastname $item->firstname'></option>";
				}
				
				$content .= "
    </datalist>
    </div>";
			}
			$competencies = new block_competency_iena_competency();
			$tabcompetencies = $competencies->get_competencies_by_userID($studentid);
			
			$module_instance = new block_competency_iena_module();
			$tab_aff_grade = array();
			$tab_aff_proficient = array();
			foreach ($tabcompetencies as $competency) {
				$data = $competencies->get_data($studentid, $competency->id, $COURSE->id);
				if ($data->usercompetencysummary->usercompetencycourse->gradename != '-') {
					array_push($tab_aff_grade, $data->usercompetencysummary->usercompetencycourse->gradename);
				} else {
					array_push($tab_aff_grade, get_string('no_evalued', 'block_competency_iena'));
				}
				array_push($tab_aff_proficient, $data->usercompetencysummary->usercompetencycourse->proficiencyname);
			}
			$tab_aff_grade = array_unique($tab_aff_grade);
			$tab_aff_proficient = array_unique($tab_aff_proficient);
			
			$content .= "

    
    ";
			
			$content .= "
    <table id=\"example\" class=\"display\" style=\"width:100%\">
        <thead>
            <tr>
                <th>" . get_string('competency', 'block_competency_iena') . "</th>
                <th>" . get_string('status', 'block_competency_iena') . "</th>
                <th>" . get_string('competent', 'block_competency_iena') . "</th>
                <th>" . get_string('eval', 'block_competency_iena') . "</th>
                <th>" . get_string('modules', 'block_competency_iena') . "</th>
                <th>" . get_string('action', 'block_competency_iena') . "</th>
            </tr>
        ";
			
			$content .= "
    
    <tr> <div class=\"form-group\">
    <th></th>
    <th></th>
    <th><select class='form-control' style='width:auto; height:auto;' id=\"filter-competent\">
<option value=\"\">" . get_string('all', 'block_competency_iena') . "</option>
";
			foreach ($tab_aff_proficient as $value) {
				$content .= "<option>$value</option>";
			}
			$content .= "
</select></th>
    <th><select  class='form-control' id=\"filter-evaluation\" style='width:auto; height:auto;'>
<option value=\"\">" . get_string('all', 'block_competency_iena') . "</option>
";
			foreach ($tab_aff_grade as $value) {
				$content .= "<option>$value</option>";
			}
			$content .= "
</select></th>
    <th><select  class='form-control' id=\"filter-module\" style='width:auto; height:auto;'>
    <option value=\"\">" . get_string('all', 'block_competency_iena') . "</option>
    <option value=\"0\">0%</option>
    <option value=\"25\">< 50%</option>
    <option value=\"50\">50%</option>
    <option value=\"100\">100%</option>
    </select>
    </th>
    <th></th>
   </div> </tr>
   </thead>
        <tbody>
    
    ";
			foreach ($tabcompetencies as $competency) {
				$data = $competencies->get_data($studentid, $competency->id, $COURSE->id);
				$status = $competencies->get_status_by_competenceid($competency->id, $studentid);
				$competency->student_proficiency = $data->usercompetencysummary->usercompetencycourse->proficiencyname;
				$competency->student_grade = $data->usercompetencysummary->usercompetencycourse->gradename;
				if ($competency->student_grade == '-') {
					$competency->student_grade = get_string('no_evalued', 'block_competency_iena');
				}
				//var_dump($data->usercompetencysummary->competency->competency->path);echo '<hr>';
				$nb_position = explode('/', $data->usercompetencysummary->competency->competency->path);
				$indic = 0;
				$signe = "";
				foreach ($nb_position as $value) {
					if ($value != "") {
						$indic++;
						$signe .= ">";
					}
				}
				$module_comp = $module_instance->get_modules_by_competencyID($competency->id);
				$nbmoduleOk = $module_instance->get_nb_module_ok_by_userid_competenceid($competency->id, $studentid, $COURSE->id);
				$nb_comp = count($module_comp);
				
				$pourcent = $nbmoduleOk * 100 / $nb_comp;
				if (is_nan($pourcent)) {
					$pourcent = 0;
				}
				if ($pourcent > 0 && $pourcent < 50) {
					$pourcent = 25;
				}
				$linkStudentsCompetence = $CFG->wwwroot . "/blocks/competency_iena/competency_iena_competency_students.php?courseid=" . $COURSE->id . "&competencyid=" . $competency->id;
				$linkCompetence = $CFG->wwwroot . "/blocks/competency_iena/competency_iena_user.php?courseid=" . $COURSE->id . "&competencyid=" . $competency->id;
				$competency->shortname = str_replace('"', "", $competency->shortname);
				$competency->shortname = str_replace("'", "", $competency->shortname);
				$content .= "<tr class='pourcent-$pourcent all-pourcent'>
                <td>$signe $competency->shortname</td>
                <td><span class=\"label label-success\">$status</span></td>
                <td>$competency->student_proficiency</td>
                <td>$competency->student_grade</td>
                <td>$nbmoduleOk/$nb_comp</td>
                <td>
                <a href=\"$linkCompetence\">
                       <i class=\"fa fa-user\" style=\"margin-right: 25px; color: black; font-size:24px\"></i>
                </a>
                ";
				if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
					$content .= "
                <a href=\"$linkStudentsCompetence\">
                       <i class=\"fa fa-users\" style=\"margin-right: 25px;color: black;font-size:24px\"></i>
                </a> ";
				}
				$content .= "
                </td>
                </tr>
                ";
			}
			$content .= "
         </tbody>
       </table>
       <script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>
       <script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/ui/1.12.0/jquery-ui.min.js\"></script>
       <script type=\"text/javascript\" charset=\"utf8\" src=\"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
               <script>
        $(document).ready(function() {
        var table = $('#example').DataTable( {
            \"language\": {
                \"url\": \"https://cdn.datatables.net/plug-ins/1.10.16/i18n/French.json\",
            },
            \"ordering\": false
        } );
        
        $( '#filter-module' ).on( 'change', function () {
            console.log($(this).val());
	        if ($(this).val()){
	            $('.all-pourcent').hide();
	            $('.pourcent-'+$(this).val()).show();
	        } else {
	            $('.all-pourcent').show();
	        }
	    } );

        //We wait 0.5 secondes for datatable load
	    setTimeout(function() {
		    $('#example_filter').hide();
		    $('#example_length').hide();
		    $('#example_info').hide();
	    }, 500);
	    
	    $( '#filter-competent' ).on( 'change', function () {
	        table.column(2).search($(this).val()).draw();
	    } );
	    
	    $( '#filter-evaluation' ).on( 'change', function () {
	        console.log($(this).val());
	        if ($(this).val()){
	        table.column(3).search('^'+$(this).val()+'$',true,false).draw(); 
	        } else {
	        table.column(3).search($(this).val()).draw();
	        }
	    });
        $(\"#myBrowser\").on('input', function () {
          var val = this.value;
          if($('#browsers option').filter(function(){
              return this.value === val;        
          }).length) {
                var value2send = document.querySelector(\"#browsers option[value='\"+this.value+\"']\").dataset.value;
                window.location.replace(value2send);
            }
        });
        } );
        </script>
        ";
			return $content;
			
		}
		
	}

?>

