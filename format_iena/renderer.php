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
	
	require_once($CFG->dirroot . '/course/format/topics/renderer.php');
	
	/**
	 * format_iena_renderer
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	class format_iena_renderer extends format_topics_renderer
	{
		
		
		/**
		 * start_section_list
		 *
		 * @return string
		 */
		protected function start_section_list()
		{
			return html_writer::start_tag('ul', ['class' => 'iena']);
		}
		
		/**
		 * section_header
		 *
		 * @param stdclass $section
		 * @param stdclass $course
		 * @param bool $onsectionpage
		 * @param int $sectionreturn
		 * @return string
		 */
		protected function section_header($section, $course, $onsectionpage, $sectionreturn = null, $iena = false)
		{
			global $PAGE, $CFG;
			$o = '';
			$currenttext = '';
			$sectionstyle = '';
			if ($section->section != 0) {
				if (!$section->visible) {
					$sectionstyle = ' hidden';
				} elseif (course_get_format($course)->is_section_current($section)) {
					$sectionstyle = ' current';
				}
			}
			if ($PAGE->user_is_editing()) {
				$o .= html_writer::start_tag('li', ['id' => 'section-' . $section->section,
					'class' => 'section main clearfix' . $sectionstyle,
					'role' => 'region', 'aria-label' => get_section_name($course, $section)]);
			}
			$o .= html_writer::tag('span', $this->section_title($section, $course), ['class' => 'hidden sectionname']);
			$leftcontent = $this->section_left_content($section, $course, $onsectionpage);
			$o .= html_writer::tag('div', $leftcontent, ['class' => 'left side']);
			$rightcontent = $this->section_right_content($section, $course, $onsectionpage);
			$o .= html_writer::tag('div', $rightcontent, ['class' => 'right side']);
			$o .= html_writer::start_tag('div', ['class' => 'content']);
			$hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));
			$hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));
			$classes = ' accesshide';
			if ($hasnamenotsecpg || $hasnamesecpg) {
				$classes = '';
			}
			$sectionname = html_writer::tag('span', $this->section_title($section, $course));
			if ($course->showdefaultsectionname) {
				$o .= $this->output->heading($sectionname, 3, 'sectionname' . $classes);
			}
			if (!$iena)
				$o .= $sectionname;
			$o .= html_writer::start_tag('div', ['class' => 'summary']);
			$o .= $this->format_summary_text($section);
			$context = context_course::instance($course->id);
			$o .= html_writer::end_tag('div');
			$o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
			return $o;
		}
		
		public function get_completion_by_section($idSection)
		{
			global $COURSE, $USER;
			$ressources_entity = new course_format_iena_section_ressources();
			$setion_entity = new course_format_iena_sections();
			//$completions = $ressources_entity->get_completions_by_userid($USER->id, $COURSE->id);
			
			$modules = $ressources_entity->get_ressources_completion_on_by_id_section($idSection);
			if (count($modules) == 0) {
				return 999;
			}
			
			$valueI = 100 / count($modules);
			$valueI = round($valueI, 1);
			$valueTotal = 0;
			foreach ($modules as $module) {
				$complet = $ressources_entity->get_completions_by_module($USER->id, $COURSE->id, $module->id);
				if ($complet->completionstate != 0) {
					$valueTotal += $valueI;
				}
			}
			
			return $valueTotal;
		}
		
		public function get_view_breadcrum($nameSection, $idSection, $htmlsection, $completion_total)
		{
			global $COURSE, $USER;
			$ressources_entity = new course_format_iena_section_ressources();
			$setion_entity = new course_format_iena_sections();
			$completions = $ressources_entity->get_completions_by_userid($USER->id, $COURSE->id);
			$modinfo = get_fast_modinfo($COURSE->id);
			$content = false;
			$content .= "

<div class='flexible_iena'>
    <div class='col-md-1'>
     <ul class='flex-container'>
        <li class=\"flex-item number_percent  big_info\">$completion_total%</li>
        </ul>
    </div>
    <div class='col-md-11'>


<ul class=\"flex-container\">
  ";
			$i = 1;
			foreach ($htmlsection as $index) {
				if ($nameSection[$i]) {
					$date_section = $setion_entity->get_section_settings_by_id_section($idSection[$i]);
					$chaine = mb_strimwidth($nameSection[$i], 0, 20, "...");
					if ($this->get_completion_by_section($idSection[$i]) != 999) {
						$pourcentage_section = $this->get_completion_by_section($idSection[$i]);
					} else {
						$pourcentage_section = 0;
					}

// On récupère le nombre de status d'elements et on change la couleur de l'indicateur de la section en elseif
					foreach ($modinfo->get_cms() as $cm) {
						if ($cm->section == $idSection[$i] && $cm->completion != 0 && $cm->deletioninprogress == 0) {
							foreach ($completions as $completion) {
								if ($completion->coursemoduleid == $cm->id && $completion->completionstate != 0) {
									$completion_ok++;
									$color = "green_bis";
									break;
								}
							}
							if ($color == "") {
								if ($nbJours > 0) {
									$color = "blue_bis";
									$completion_pasfait++;
								} else if ($nbJours < 0) {
									$color = "orange_bis";
									$completion_encours++;
								} else {
									$color = "blue_bis";
									$completion_pasfait++;
								}
							}
						}
					}
					
					if ($completion_encours >= 1) {
						$couleur = "orange_bis";
					} else if ($completion_pasfait >= 1) {
						$couleur = "blue_bis";
					} else {
						$couleur = "green_bis";
					}
					
					$content .= "
                 <li>
                <ul class=\"flex-container flex-content indicateur_section\" data-part=\"$chaine\">
                   <li class=\"flex-item number_percent $couleur \"><p title='$nameSection[$i]'>
                " . $pourcentage_section . "%</p>
                  </li>
               ";
					// Traitement module
					
					$completion_ok = 0;
					$completion_encours = 0;
					$completion_pasfait = 0;
					
					foreach ($modinfo->get_cms() as $cm) {
						if ($cm->section == $idSection[$i] && $cm->completion != 0 && $cm->deletioninprogress == 0) {
							$dateUp = date_create($date_section->date_rendu);
							if ($date_section->date_rendu) {
								
								// On transforme les 2 dates en timestamp
								$date1 = $dateUp->getTimestamp();
								$date2 = date_create(date("Y-m-d H:i:s"))->getTimestamp();
								$nbJoursTimestamp = $date1 - $date2;
								$nbJours = $nbJoursTimestamp / 86400;
							} else {
								$nbJours = 0;
							}
							$color = "";
							foreach ($completions as $completion) {
								if ($completion->coursemoduleid == $cm->id && $completion->completionstate != 0) {
									$completion_ok++;
									$color = "green_bis";
									break;
								}
							}
							if ($color == "") {
								if ($nbJours > 0) {
									$color = "blue_bis";
									$completion_pasfait++;
								} else if ($nbJours < 0) {
									$color = "orange_bis";
									$completion_encours++;
								} else {
									$color = "blue_bis";
									$completion_pasfait++;
								}
							}
							$content .= "
                   <a href=\" $cm->url \" title=\" $cm->name \">
                   <li class=\"flex-item $color\"></li>
                   </a>";
						}
					}
					$content .= "</ul>
           </li>";
				}
				$i++;
			}
			$content .= "</ul>
   </ul>
";
			
			return $content;
		}
		
		public function get_view_teacher_message()
		{
			global $COURSE, $CFG;
			$id = $COURSE->id;
			$course_ctx = context_course::instance($COURSE->id);
			$teachers = get_enrolled_users($course_ctx, 'moodle/course:update', 0);
			//var_dump($teachers);die;
                        
                        $course_format_iena_groups_instance = new course_format_iena_groups();
                        $groups = $course_format_iena_groups_instance->get_groups_by_id_course($COURSE->id);
                        
			$render = false;
			$render .= "
                <!--Début du dropdown-->
<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>                
<script>
$(document).ready(function () {

    $('#select-group').on('change', function () {
            
        $('.nb_pers').hide();
        $('.'+this.value).show();
        
    });
    
});

window.onload = function()
{
    $('.nb_pers').hide();
    $('.id_groupe0').show();
}

function change_grouplink(obj)
{
    var classList = obj.classList;
    var id_groupe = classList[2].split('id_groupe');
    id_group = id_groupe[1]; 
    var t = obj.parentNode;
    var t_href = t.getAttribute(\"href\");
    t_href += \"&groupid=\" + id_group;
    t.setAttribute(\"href\", t_href);
}

</script>
<div class=\"dropdown d-inline\">";
                        if(has_capability('moodle/course:update', $course_ctx))
                        {
                            $render .= " 
            <span>
                <p style=\"float:left;\">Choix du groupe </p>
                <select style=\"float:left;\" class=\"select\" id=\"select-group\">";
                            foreach($groups as $group)
                            {
                                $checked="";
                                if($_GET['groupid']==$group->id)
                                {
                                    $checked="selected";
                                }
                                $render .="<option value=\"".$group->idnumber."\" ".$checked.">".$group->name."</option>";
                            }
                            $checked="";
                            if($_GET['groupid']=="")
                            {
                                $checked="selected";
                            }
                            $render .="<option value=\"id_groupe0\" ".$checked.">Tous les groupes</option>                   
                </select>
            </span>";
                        }
                        
        $render .="    
    <a href=\"#\" class=\"dropdown-toggle btn btn-default\" id=\"dropdown-2\" title=\"Actions\" role=\"button\" data-toggle=\"dropdown\"
       aria-haspopup=\"true\" aria-expanded=\"false\" >
        <!--        Icone du dropdown -->
        <i class=\"fa fa-envelope\"  aria-hidden=\"true\" aria-label=\"\"></i>
    </a>
    <!--    Contenu du dropdown-->
    <div class=\"dropdown-menu dropdown- menu align-tr-br\" id=\"action-menu-2-menu\" data-rel=\"menu-content\"
         aria-labelledby=\"action-menu-toggle-2\" role=\"menu\" data-align=\"tr-br\">
        <!--        Item dropdown (initial)-->
        <!--<div class=\"dropdown-item\">
            <a href=\"http://127.0.0.1/dossiers/modoo/course/edit.php?id=2\" id=\"action_link5aa261357c57416\" class=\"\"
               role=\"menuitem\"><i class=\"icon fa fa-cog fa-fw \" aria-hidden=\"true\" aria-label=\"\"></i>Paramètres</a>
        </div>-->
        <div class=\"contenu\">
            <div class='centered'>
                <h3>Enseignants</h3>

            </div>
            <ul class='bulle'>
            ";
			foreach ($teachers as $teacher) {
				$render .= "<li><a href='" . $CFG->wwwroot . "/message/index.php?id=" . $teacher->id . "'>" . $teacher->firstname . " " . $teacher->lastname . "</a></li>";
			}
			$render .= "
            </ul>
            <div class='centered'>
            <a href ='" . $CFG->wwwroot . "/message/index.php'>
            <button class='btn  btn-sm  btn_blue' >Messagerie</button></a>
            </div>
        </div>
    </div>
</div>";
			
			return $render;
		}
		
		public function get_view_link_appel()
		{
			global $COURSE, $CFG, $DB;
			$id = $COURSE->id;
			
			$param['module'] = 23;
			$param['course'] = $COURSE->id;
			$param['deletioninprogress'] = 0;
			$req = $DB->get_record('course_modules', $param);
			$render = false;
			if ($req->id) {
				$render .= "<a class=\"btn btn-default\" href=\"$CFG->wwwroot/mod/attendance/manage.php?id=$req->id\">
                <i class=\"fa fa-users \" style=\"color :#00bfb2\"></i>
                </a>";
			}
			
			return $render;
		}
		
		public function get_render_competences($diSection)
		{
			global $DB, $COURSE, $CFG, $USER;
			$section_entity = new course_format_iena_section_ressources();
			$modules = $section_entity->get_ressources_by_id_section($diSection);
			$id_competence = array();
			foreach ($modules as $module) {
				$requete = $DB->get_record('competency_modulecomp', array('cmid' => $module->id));
				if ($requete) {
					$id_competence[$requete->id] = $requete->competencyid;
				}
			}
			//$requete = $DB->get_records('competency_coursecomp',array('courseid' => $COURSE->id));
			$competences = array();
			foreach ($id_competence as $val) {
				$req = $DB->get_record('competency', array('id' => $val));
				$competences[$val] = $req->shortname;
			}
			if (count($competences) == 0) {
				return "";
			}
			$render = false;
			$render .= "<!--Début du dropdown-->
<div class=\"dropdown d-inline\">
    <a href=\"#\" class=\"dropdown-toggle\" id=\"dropdown-2\" title=\"Actions\" role=\"button\" data-toggle=\"dropdown\"
       aria-haspopup=\"true\" aria-expanded=\"false\" style=\"color : white\">
        <!--        Icone du dropdown -->
        <i class=\"fa fa-lightbulb-o\" aria-hidden=\"true\"></i>

    </a>
    <!--    Contenu du dropdown-->
    <div class=\"dropdown-menu dropdown- dropdown-menu-right menu align-tr-br\" id=\"action-menu-2-menu\" data-rel=\"menu-content\"
         aria-labelledby=\"action-menu-toggle-2\" role=\"menu\" data-align=\"tr-br\">
        <!--        Item dropdown (initial)-->
        <!--<div class=\"dropdown-item\">
            <a href=\"http://127.0.0.1/dossiers/modoo/course/edit.php?id=2\" id=\"action_link5aa261357c57416\" class=\"\"
               role=\"menuitem\"><i class=\"icon fa fa-cog fa-fw \" aria-hidden=\"true\" aria-label=\"\"></i>Paramètres</a>
        </div>-->
        <div class=\"contenu\">
        <div class='centered'>
                    <h3>Compétences</h3>

            </div>
            <ul class='bulle'>
            ";
			foreach ($competences as $key => $competence) {
				$render .= "<li><a href='" . $CFG->wwwroot . "/admin/tool/lp/user_competency_in_course.php?courseid=" . $COURSE->id .
					"&competencyid=" . $key . "&userid=" . $USER->id . "'> $competence </a></li>";
			}
			$render .= "
            </ul>
            <div class='centered'>
            <a href ='" . $CFG->wwwroot . "/admin/tool/lp/coursecompetencies.php?courseid=" . $COURSE->id . "'>
            <button class='btn  btn-sm  btn_blue' >Compétence du cours</button></a>
            </div>
        
        </div>
    </div>
</div>";
			
			return $render;
		}
		
		public function get_view_iena($course, $htmlsection, $nameSection, $introSection, $idSection, $completion_total)
		{
			global $CFG, $COURSE, $USER;
			$section_entity = new course_format_iena_sections();
			//var_dump($param_section);die;
			$percent = 40;
			$nb_pers = 4;
			$titre = "Titre";
			$date = "1er janvier";
			$sectionName = "Section 1";
			$sectionIntro = "Section intro";

     ////////////////////////////// Orange Indicator ///////////////////////////////////////////////////
			$course_sections_instance = new course_format_iena_section_ressources();
			
			$course_sections = new course_format_iena_sections();
			$liste_sections = $course_sections->get_sections_by_id_course($COURSE->id);
                        
                        $course_format_iena_groups_instance = new course_format_iena_groups();
                        $groups = $course_format_iena_groups_instance->get_groups_by_id_course($COURSE->id);
                        //$students_group = $course_format_iena_groups_instance->get_students_group($course->id);
   		                   
                        $tab_group_indicateur=array();
                        foreach($groups as $group)
                        {
                            $tab_student_completion = array();
                            $tab_section_indicateur = array();
                            foreach ($liste_sections as $section_ligne) 
                            {
				//récupère les modules pour une section
				$modules_states_section = $course_sections->get_hidden_modules_by_section($section_ligne->id);
				
				// si lignes vides dans la BDD => vérifier compteur modules de l'étudiant
				$nb_modules = 0;
				foreach ($modules_states_section as $mod) {
					if ($mod->hide == 1) {
						$nb_modules++;
					}
				}
				
				// Compter chaque étudiant du groupe qui n'a pas tout validé dans la section
				foreach ($group->list_userid as $student) 
                                {
                                    //récupération du tableau de completion des modules de l'étudiant pour ce cours (vérifier ensuite si module dans section)
                                    $tab_student_completion = $course_sections_instance->get_completions_by_userID($student->userid, $COURSE->id);
					
                                    // si aucune info completion de l'étudiant n'est dans la BDD
                                    if (empty($tab_student_completion)) {
					$indicateur = 1;
                                    } else {
					$compteur = 0;
					foreach ($modules_states_section as $mod) {
                                                if ($mod->hide == 1) {
                                                        $break_one = 0;
                                                        foreach ($tab_student_completion as $module_completion) {
                                                                if ($module_completion->coursemoduleid == $mod->cmid) {
                                                                        $compteur++;
                                                                        if ($module_completion->completionstate == 0) {
                                                                                $indicateur = 1;
                                                                                $break_one = 1;
                                                                                break;
                                                                        } else {
                                                                                $indicateur = 0;
                                                                        }
                                                                }
                                                        }
                                                        if ($break_one == 1) {
                                                                break;
                                                        }
                                                }
					}
					if ($compteur < $nb_modules) {
                                                if ($indicateur == 0) {
							$indicateur = 1;
						}
					}
                                    }
                                    $tab_section_indicateur[$section_ligne->id] += $indicateur;
				}
                            }
                            $tab_group_indicateur[$group->idnumber]=$tab_section_indicateur;
                            
                        }
                        
                        $course_ctx = context_course::instance($COURSE->id);
			$students = get_enrolled_users($course_ctx, 'mod/assignment:submit', 0);
			$tab_student_completion = array();
                        $tab_section_indicateur = array();
			foreach ($liste_sections as $section_ligne) {
				//récupère les modules pour une section
				$modules_states_section = $course_sections->get_hidden_modules_by_section($section_ligne->id);
				
				// si lignes vides dans la BDD => vérifier compteur modules de l'étudiant
				$nb_modules = 0;
				foreach ($modules_states_section as $mod) {
					if ($mod->hide == 1) {
						$nb_modules++;
					}
				}
				
				// Compter chaque étudiant du cours qui n'a pas tout validé dans la section
				foreach ($students as $student) {
					//récupération du tableau de completion des modules de l'étudiant pour ce cours (vérifier ensuite si module dans section)
					$tab_student_completion = $course_sections_instance->get_completions_by_userID($student->id, $COURSE->id);
					
					// si aucune info completion de l'étudiant n'est dans la BDD
					if (empty($tab_student_completion)) {
						$indicateur = 1;
					} else {
						$compteur = 0;
						foreach ($modules_states_section as $mod) {
							if ($mod->hide == 1) {
								$break_one = 0;
								foreach ($tab_student_completion as $module_completion) {
									if ($module_completion->coursemoduleid == $mod->cmid) {
										$compteur++;
										if ($module_completion->completionstate == 0) {
											$indicateur = 1;
											$break_one = 1;
											break;
										} else {
											$indicateur = 0;
										}
									}
								}
								if ($break_one == 1) {
									break;
								}
							}
						}
						if ($compteur < $nb_modules) {
							if ($indicateur == 0) {
								$indicateur = 1;
							}
						}
					}
					$tab_section_indicateur[$section_ligne->id] += $indicateur;
				}
			}
                        $tab_group_indicateur['id_groupe0']=$tab_section_indicateur;

////////////////////////////end Orange Indicator//////////////////////////////////////////////////

			
			
			$view = "";
			if ($course->viewbreadcrum == 1) {
				
				$view .= $this->get_view_breadcrum($nameSection, $idSection, $htmlsection, $completion_total);
			}
			$view .= $this->get_view_link_appel();
			
			if ($course->viewiconmessage == 1) {
				$view .= $this->get_view_teacher_message();
			}
			$i = 0;
			$link = $CFG->wwwroot . "/course/format/iena/suivi_unit.php?courseid=" . $COURSE->id;
			$view .= "<!-- <script defer src=\"https://use.fontawesome.com/releases/v5.0.8/js/all.js\"></script>  -->
<style>
.centered {
display:flex;justify-content:center;align-items:center;
}
.contenu {
    min-width: 15rem;
}
ul.bulle {
    list-style: none;
    padding:5%;
}

.bulle {
    list-style: none;
}

.bulle > li {
    list-style: none;
    font-weight: normal;
    font-size: 0.8rem;
    line-height: 1rem;
    padding-top: 5%;
}
</style>";
			
			foreach ($htmlsection as $section) {
				$presence = "";
				if (!$section) {
					continue;
				}
				$param_section = $section_entity->get_section_settings_by_id_section($idSection[$i]);
				if ($param_section->presence && $i != 0) {
					if ($param_section->presence == 1) {
						$presence = "En présence";
					} else if ($param_section->presence == 2) {
						$presence = "A distance";
					}
				}
				
				if ($param_section->date_rendu) {
					$dateUp = date_create($param_section->date_rendu);
					$date = $dateUp->format("j/m H:i");
					$dateUp = $dateUp->getTimestamp();
				} else {
					$date = "";
				}
				
				$titre = $nameSection[$i];
				$sectionIntro = $introSection[$i];
				//If section is hidden continue
				if ($titre == null && !(has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id))) {
					$i++;
					continue;
				}
				$view .= "<section class=\"section\">
    <div class=\"card card_block\">
        <div class=\"heading set_height\">";
				if ($this->get_completion_by_section($idSection[$i]) != 999) {
					$view .= "<div class=\"percent set_height\">
                " . $this->get_completion_by_section($idSection[$i]) . "%
            </div>";
				}
				if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id) && $i != 0) {
					$view .= "
            <a href='$link&sectionid=".$idSection[$i]."' style=\"color : white\">";
            foreach($tab_group_indicateur as $group_indicateur => $val)
            {//var_dump($tab_group_indicateur);die;
                //$view .="<div onclick='change_grouplink(this)' class=\"nb_pers set_height ".$group_indicateur."\" style=\"display:";
		$view .="<div onclick='change_grouplink(this)' class=\"nb_pers set_height ".$group_indicateur."\">";
                foreach ($val as $key => $value) 
                {
                    if ($idSection[$i] == $key) 
                    {
			$view .= $value;
                    }
                    
//                     if ($idSection[$i] == $key) 
//                    {
//                        if($group_indicateur == "id_groupe0")
//                        {
//                            $view .= "block;\">".$value;
//                        }
//                        else
//                        {
//                            $view .= "none;\">".$value;
//                        }
//			
//                    }
                    
                    
                }
		$view .= "</div>";
            }
                
                $view .="
            </a>";
				}
				$view .= "<div class=\"titre_section set_height\">
                <p>$titre</p>
            </div>
             <div class=\"right_info\">
             ";
				if ($presence) {
					$view .= "
                <div class=\"label_item\">
                    $presence
                </div>";
				}
				if ($date) {
					$link_date = $CFG->wwwroot . "/calendar/view.php?view=month&time=" . $dateUp . "&course=" . $COURSE->id;
					
					$view .= "
                <a href='$link_date'> <div class=\"label_item\">
                    $date
                </div></a> ";
				}
				$view .= "
                <div class=\"titre_section set_height\">
                

                ";
				
				$view .= $this->get_render_competences($idSection[$i]);
				$view .= "</div>
                
                ";
				$link_param = $CFG->wwwroot . "/course/format/iena/param_section.php?courseid=" . $COURSE->id . "&sectionid=" . $idSection[$i];
				if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id) && $i != 0) {
					$view .= "<div class=\"titre_section set_height\">
                    <a href='$link_param' style=\"color : white\">
                        <i class=\"fa fa-cog \" aria-hidden=\"true\" ></i>
                    </a>
                </div>";
				}
				$view .= "</div>

        </div>
        ";
				$view .= "<div class=\"wrapper\">
        <div class=\"description\">";
				if (strpos($sectionIntro, "</p>")) {
					$view .= "
         <div class=\"small\">
                <p>$sectionIntro</p>
            </div>
        </div>
        <a href=\"#\">Voir la description complète</a>
    ";
				}
				$view .= "</div>
    <div class=\"wrapper\">
            $section
    </div >
    
     </div>
    
    </section>";
				$i++;
			}
			
			
			return $view;
		}
		
		
		public function print_iena_section_pages($course)
		{
			global $PAGE, $USER;
			
			//$courses = enrol_get_my_courses('*', 'fullname ASC');
			$coursesprogress = [];
			
			$context = context_course::instance($course->id);
			$course = course_get_format($course)->get_course();
			
			$completion = new \completion_info($course);
			
			// First, let's make sure completion is enabled.
			/*if (!$completion->is_enabled()) {
				continue;
			}*/
			
			$proges = new \core_completion\progress();
			$percentage = $proges->get_course_progress_percentage($course);
			if (!is_null($percentage)) {
				$percentage = floor($percentage);
			}
			
			$coursesprogress[$course->id]['completed'] = $completion->is_course_complete($USER->id);
			$coursesprogress[$course->id]['progress'] = $percentage;
			
			//var_dump($coursesprogress[$course->id]["progress"]);die;
			$completion_total = $coursesprogress[$course->id]["progress"];
			
			$modinfo = get_fast_modinfo($course);
			$course = course_get_format($course)->get_course();
			$context = context_course::instance($course->id);
			$completioninfo = new completion_info($course);
			if (isset($_COOKIE['sectionvisible_' . $course->id])) {
				$sectionvisible = $_COOKIE['sectionvisible_' . $course->id];
			} elseif ($course->marker > 0) {
				$sectionvisible = $course->marker;
			} else {
				$sectionvisible = 1;
			}
			$htmlsection = false;
			$nameSection = false;
			$idSection = false;
			$introSection = false;
			foreach ($modinfo->get_section_info_all() as $section => $thissection) {
				//Nom de la section
				//var_dump($thissection->name);
				$htmlsection[$section] = '';
				/*if ($section == 0) {
					//$section0 = $thissection;
					//continue;
					$nameSection[$section] = "Section 0";
					$idSection[$section] =  $section;
				}*/
				if ($section > $course->numsections) {
					continue;
				}
				/* if is not editing verify the rules to display the sections */
				if (!$PAGE->user_is_editing() && (!has_capability('moodle/course:update', $context = context_course::instance($course->id), $USER->id))) {
					if ($course->hiddensections && !(int)$thissection->visible) {
						continue;
					}
					if (!$thissection->available && !empty($thissection->availableinfo)) {
						$htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
						continue;
					}
					if (!$thissection->uservisible || !$thissection->visible) {
						$htmlsection[$section] .= $this->section_hidden($section, $course->id);
						continue;
					}
				}
				
				
				//Affiche le nom de la section en mode propre sans lien
				$idSection[$section] = $thissection->id;
				$nameSection[$section] .= $this->section_title_without_link($thissection, $course);
				if ($PAGE->user_is_editing())
					$htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
				//$htmlsection[$section] .= $this->section_title($thissection,$course);
				//$introSection[$section] .= $this->section_header($thissection, $course, false, 0);
				$introSection[$section] .= $this->section_header($thissection, $course, false, 0, true);
				if ($thissection->uservisible) {
					// Renvoie le lien du cours avec icone
					$htmlsection[$section] .= $this->courserenderer->course_section_cm_list($course, $thissection, 0);
					$htmlsection[$section] .= $this->courserenderer->course_section_add_cm_control($course, $section, 0);
				}
				$htmlsection[$section] .= $this->section_footer();
				
				
			} //ENDFOREACH
			echo $completioninfo->display_help_icon();
			echo $this->output->heading($this->page_title(), 2, 'accesshide');
			echo $this->course_activity_clipboard($course, 0);
			echo $this->start_section_list();
			//traitement section 0
			/*if ($section0->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
				$htmlsection0 = $this->section_header($section0, $course, false, 0);
				$htmlsection0 .= $this->courserenderer->course_section_cm_list($course, $section0, 0);
				$htmlsection0 .= $this->courserenderer->course_section_add_cm_control($course, 0, 0);
				$htmlsection0 .= $this->section_footer();
			}*/
			//A ce stade on à toutes les activité déja prête en HTML par contre il manque le nom des sections
			// Il se trouve dans : $thissection->name
			//var_dump($htmlsection);
			//var_dump($htmlsection0);
			if ($PAGE->user_is_editing()) {
				echo $completioninfo->display_help_icon();
				echo $this->output->heading($this->page_title(), 2, 'accesshide');
				echo $this->course_activity_clipboard($course, 0);
				echo $this->start_section_list();
			}
			if ($course->sectionposition == 0 and isset($htmlsection0)) {
				if ($PAGE->user_is_editing())
					echo html_writer::tag('span', $htmlsection0, ['class' => 'above']);
			}
			//echo $this->get_button_section($course, $sectionvisible);
			//ici on affiche toutes les activité
			//Si on est en mode etition alors on affiche le page de "BASE" Sinon notre model
			if (!$PAGE->user_is_editing()) {
				echo $this->get_view_iena($course, $htmlsection, $nameSection, $introSection, $idSection, $completion_total);
			} else {
				foreach ($htmlsection as $current) {
					echo $current;
				}
			}
			//A ce stage rien n'est encore afficher mise à part l'icone : Votre progression
			//Mais si on vas en mode édition ça marche je pense que le js ou autre chose cache le reste
			//C'est dans le CSS
			if ($course->sectionposition == 1 and isset($htmlsection0)) {
				if ($PAGE->user_is_editing())
					echo html_writer::tag('span', $htmlsection0, ['class' => 'below']);
			}
			
			//Ajoute le + et - à la fin de la page à voir quelle partie du code il faut vraiment garder
			if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
				foreach ($modinfo->get_section_info_all() as $section => $thissection) {
					if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
						continue;
					}
					echo $this->stealth_section_header($section);
					echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
					echo $this->stealth_section_footer();
				}
				echo $this->end_section_list();
				echo html_writer::start_tag('div', ['id' => 'changenumsections', 'class' => 'mdl-right']);
				$straddsection = get_string('increasesections', 'moodle');
				$url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
					'increase' => true, 'sesskey' => sesskey()]);
				$icon = $this->output->pix_icon('t/switch_plus', $straddsection);
				echo html_writer::link($url, $icon . get_accesshide($straddsection), ['class' => 'increase-sections']);
				if ($course->numsections > 0) {
					$strremovesection = get_string('reducesections', 'moodle');
					$url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
						'increase' => false, 'sesskey' => sesskey()]);
					$icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
					echo html_writer::link(
						$url,
						$icon . get_accesshide($strremovesection),
						['class' => 'reduce-sections']
					);
				}
				echo html_writer::end_tag('div');
			} else {
				echo $this->end_section_list();
			}
			echo html_writer::tag('style', '.course-content ul.iena #section-' . $sectionvisible . ' { display: block; }');
			if (!$PAGE->user_is_editing()) {
				$PAGE->requires->js_init_call('M.format_iena.init', [$course->numsections]);
			}
			
		}
	}
