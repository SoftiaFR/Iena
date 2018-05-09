<?php
	/**
	 * Created by PhpStorm.
	 * User: softia
	 * Date: 26/02/2018
	 * Time: 16:53
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
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	global $COURSE, $DB;
	$id = $COURSE->id;
	$course_ctx = context_course::instance($course->id);
	$students = get_enrolled_users($course_ctx, 'mod/assignment:submit', 0);
	$modinfo = get_fast_modinfo($course->id);
	
	$modules_completion_infos = $DB->get_records_sql('SELECT cmc.id, cmc.coursemoduleid, cmc.userid, cmc.completionstate,
                                                        cm.course, cm.section, cm.module, cm.idnumber,
                                                        u.firstname, u.lastname,u.id as userID, m.name
                                                  FROM  {course_modules_completion} as cmc
                                                  inner join {course_modules} as cm on cm.id = cmc.coursemoduleid
                                                  inner join {user} as u on u.id = cmc.userid
                                                  inner join {modules} as m on m.id = cm.module
                                                  where cm.course = ? and cm.deletioninprogress = 0
                                                  order by section, coursemoduleid asc', array($course->id));
        
        $course_format_iena_groups_instance = new course_format_iena_groups();
        $groups = $course_format_iena_groups_instance->get_groups_by_id_course($course->id);
        $students_group = $course_format_iena_groups_instance->get_students_group($course->id);

//	Get modules with status hide
	$view_param_indicateur = new view_param_indicateur();
	$sections = $view_param_indicateur->get_course_sections_modules();
	$Tab_hide_indic_modules = array();
	$i = 0;
	
	foreach ($sections as $section) {
		$Tab_hide_indic_modules[$section->id] = $view_param_indicateur->get_ressource_hide_indicator_new($section->id);
		$i++;
	}
?>

<!--Scripts -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
<script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
<script src="<?= $CFG->wwwroot; ?>/course/format/iena/js/file.js"></script>



<!--Contenu de la page-->

<section class="section" id="params">
	<span style="float: right;">
            <span>
                <p style="float:left;">Choix du groupe </p>
                <select style="float:left;" class="select" id="select-group">
                    <?php
                        foreach($groups as $group){
                            $checked="";
                            if($_GET['groupid']==$group->id)
                            {
                                $checked="selected";
                            }
                            echo "<option value=\"".$group->idnumber."\" ".$checked.">".$group->name."</option>";
                        }
                        $checked="";
                        if($_GET['groupid']=="")
                        {
                            $checked="selected";
                        }
                        echo "<option value=\"groupAll\" ".$checked.">Tous les groupes</option>";
                    ?>
                </select>
            </span>
            <a id="molette"
	   href='<?= $CFG->wwwroot; ?>/course/format/iena/param_indicateur.php?courseid=<?php echo $COURSE->id ?>'
	   style="color : black; float: right;">
		<i class="fa fa-cog" aria-hidden="true" style="font-size: 25px;"></i>
            </a>
        </span>    
	<div class="followed_for">
		<p><?= get_string('modules_for', 'format_iena') ?></p>
		<select class="select" id="select-section">
			<?php
				foreach ($sections as $section) {
					$checked = "";
					
					if ($section->id == $_GET['sectionid']) {
						$checked = "selected";
					}
					echo "<option value=\"section-" . $section->id . "\"  " . $checked . ">" . $section->name . "</option>";
				}
				echo "<option value=\"Cours\">" . get_string('all_course', 'format_iena') . "</option>";
			?>
		</select>
	</div>
	<div class="followed_for">
		<p><?= get_string('students', 'format_iena') ?></p>
		<select class="select" id="select-student">
			<option value="studentFilter"><?= get_string('not_done', 'format_iena') ?></option>
			<option value="student"><?= get_string('all_students', 'format_iena') ?></option>
		</select>
	</div>


</section>
<!--Tableau d'affichage -->
<input type='hidden' id="courseID" value='<?= $COURSE->id; ?>'>
<section class="section">
	<table id="example" class="dataTables_wrapper no-footer display nowrap" width="100%" cellspacing="0">
		<thead>
		<tr>
			<th id="white"></th>
			<th id="white" class="clickMe"><?= get_string('student', 'format_iena') ?></th>
			<th style='display: none;'></th>
			
			
			<?php
				foreach ($sections as $section) {
					foreach ($Tab_hide_indic_modules[$section->id] as $hide_indic_module) {
						
						if ($hide_indic_module->hide == 1) {
							$moduleTool = new course_format_iena_section_ressources();
							$moduleTool->get_ressource_by_id($hide_indic_module->cmid);
							echo "<th  class='section-" . $section->id . " sectionH' id='black'>" . $moduleTool->name . "</th>";
						}
					}
				}
				
				
				foreach ($modinfo->get_cms() as $cm) {
					$enter = 0;
					if ($cm->deletioninprogress === 0) {
						echo "<th class='courseAllH' id='black'>" . $cm->name . "</th>";
					}
				}
			?>
		</tr>
		</thead>

		<tbody>
		<?php
			$course_sections_instance = new course_format_iena_section_ressources();
			$course_sections = new course_format_iena_sections();
			$liste_sections = $course_sections->get_sections_by_id_course($COURSE->id);
			
			foreach ($students as $student) {
				// état des modules par étudiant
				$tab_student_completion = $course_sections_instance->get_completions_by_userID($student->id, $COURSE->id);
				
				echo "<tr class='groupAll ";
                                foreach($students_group as $student_group)
                                {
                                    if($student->id == $student_group->userid)
                                    {
                                        echo $student_group->idnumber." ";
                                        break;
                                    }
                                }
                                echo "sectionAll ";
				foreach ($liste_sections as $the_section) {
					//récupère les modules pour une section
					$modules_states_section = $course_sections->get_hidden_modules_by_section($the_section->id);
					
					// si lignes vides dans la BDD => vérifier compteur modules de l'étudiant
					$nb_modules = 0;
					foreach ($modules_states_section as $mod_compt) {
						if ($mod_compt->hide == 1) {
							$nb_modules++;
						}
					}
					
					$compteur_mod = 0;
					// on affiche les modules qui ont un status hide ==1
					foreach ($modules_states_section as $module_state) {
						$break_one = 0;
						
						if ($module_state->hide == 1) {
							// on vérifie pour l'étudiant si il a complété ce module
							foreach ($tab_student_completion as $module_student_completion) {
								if ($module_state->cmid == $module_student_completion->coursemoduleid) {
									$compteur_mod++;
									if ($module_student_completion->completionstate != 0) {
										//l'étudiant a complété ce module donc on peut filtrer la section si il a complété tous les modules de la section
										$indicateur = "filtre ";
									} else {
										$indicateur = " ";
										//l'étudiant a au moins un module non complété donc on peut sortir et ne pas filtrer la section
										$break_one = 1;
									}
								}
							}
							if ($break_one == 1) {
								break;
							}
						}
					}
					
					if ($compteur_mod == $nb_modules) {
						echo "section-" . $the_section->id . $indicateur . " ";
					} else {
						$indicateur = " ";
						echo "section-" . $the_section->id . $indicateur . " ";
					}
					
				}
				
				echo "'>"
					. "
                    <td>";
				?>
				<button class="btn btn-secondary" onclick="w3.toggleShow('#tog_<?= $student->id ?>')">...</button>
				<?php
				echo "<div class='totoggle' id='tog_" . $student->id . "'>"
					. "<a href='" . $CFG->wwwroot . "/message/index.php?id=" . $student->id . "' class=\"btn btn-secondary\" title='Message via la plateforme'><i class=\"far fa-envelope\"></i></a>"
					. "<a href='" . $CFG->wwwroot . "/course/user.php?mode=grade&id=" . $COURSE->id . "&user=" . $student->id . "' class=\"btn btn-secondary\" title='Rapport de notes'><i class=\"fas fa-graduation-cap\"></i></a>"
					. "<a href='" . $CFG->wwwroot . "/report/outline/user.php?id=" . $student->id . "&course=" . $COURSE->id . "&mode=complete' class=\"btn btn-secondary\" title='Rapport complet'><i class=\"far fa-copy\"></i></a>"
					. "<a href='" . $CFG->wwwroot . "/report/outline/user.php?id=" . $student->id . "&course=" . $COURSE->id . "&mode=outline' class=\"btn btn-secondary\" title='Rapport résumé'><i class=\"far fa-file\"></i></a>"
					. "</div>"
					. "</td>"
					. "<td id=\"black\">"
					. $student->firstname . " " . $student->lastname
					. "</td>";
				
				echo "<td style='display: none;'>" . $student->id . "</td>";
				foreach ($sections as $section) {
					foreach ($Tab_hide_indic_modules[$section->id] as $cm) {
						
						$completion = 0;
						foreach ($modules_completion_infos as $mld) {
							
							if ($student->id == $mld->userid && $cm->cmid == $mld->coursemoduleid) {
								$completion = $mld->completionstate;
								
								break;
							}
							//var_dump($completion);
						}
						
						if ($cm->hide == 1) {
							if ($completion == 0) {
								echo "<td class='section-" . $cm->sectionid . " sectionH'><input type=\"checkbox\" onclick=\"return false;\" /> </td>";
							} else {
								echo "<td class='section-" . $cm->sectionid . " sectionH'><input type=\"checkbox\"  checked onclick=\"return false;\"/> </td>";
							}
						}
					}
				}
				
				foreach ($modinfo->get_cms() as $cm) {
					if ($cm->deletioninprogress === 0) {
						$completion = 0;
						foreach ($modules_completion_infos as $mld) {
							if ($student->id == $mld->userid && $cm->id == $mld->coursemoduleid) {
								$completion = $mld->completionstate;
								break;
							}
						}
						if ($completion == 0) {
							echo "<td class='courseAllH'><input type=\"checkbox\" onclick=\"return false;\" /> </td>";
						} else {
							echo "<td class='courseAllH'><input type=\"checkbox\"  checked onclick=\"return false;\"/> </td>";
						}
					}
				}
				
				echo "</tr>";
			}
		?>
		</tbody>
	</table>

	<button id="button" class="dt-button"
	        style="font-weight:bold;"><?= get_string('send_message', 'format_iena') ?> <i
				class="far fa-envelope"></i>
	</button>

</section>