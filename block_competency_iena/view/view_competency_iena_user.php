<?php
// MICHAEL LEBEAU
// CAMILLE VRIGNAUD
	
	class view_competency_iena_user
	{
		public function get_competency_history($studentid, $competencyid)
		{
			global $COURSE, $DB, $USER;
			
			try {
				if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
					$tab_competency_history = $DB->get_records_sql('select * FROM {competency_evidence} as ce
                                                        inner join {competency_usercomp} as cuc on cuc.id = ce.usercompetencyid
                                                        WHERE cuc.userid = ? AND cuc.competencyid = ?', array($studentid, $competencyid));
				} else {
					$tab_competency_history = $DB->get_records_sql('select * FROM {competency_evidence} as ce
                                                        inner join {competency_usercomp} as cuc on cuc.id = ce.usercompetencyid
                                                        WHERE cuc.userid = ? AND cuc.competencyid = ?', array($USER->id, $competencyid));
				}
			} catch (coding_exception $e) {
			} catch (dml_exception $e) {
			}
			return $tab_competency_history;
			
		}
		
		public function get_content()
		{
			
			global $COURSE, $DB, $USER, $CFG;
			
			$competencyid = $_GET['competencyid'];
			//$id_student = $_GET['studentid'];
			
			if ($_GET['studentid']) {
				$tmpiduser = $_GET['studentid'];
			} else {
				$tmpiduser = $USER->id;
			}
			$competency_instance = new block_competency_iena_competency();
			$competencies = $competency_instance->get_competencies_by_courseID($COURSE->id);
			
			$competency_instance->get_competency_by_id($competencyid);
			$module_instance = new block_competency_iena_module();
			$module_comp = $module_instance->get_modules_by_competencyID($competencyid);
			
			$data = $competency_instance->get_data($tmpiduser, $competencyid, $COURSE->id);
            $scale = \grade_scale::fetch(array('id' => $data->usercompetencysummary->competency->framework->scaleid));
            $scale->load_items();
			//var_dump($scale->scale_items);die;
			
			$student_instance = new block_competency_iena_student();
			// FILTRER PROF
			$students = $student_instance->get_all_students_by_course($COURSE->id);
			$student_instance->get_student_by_id($USER->id);
			
			//$link2 = $CFG->wwwroot. $_SERVER['REQUEST_URI'];
			$link2 = $CFG->wwwroot . '/blocks/competency_iena/competency_iena_user.php?courseid=' . $COURSE->id . '&competencyid=' . $competencyid . '&studentid=';
			$link = $CFG->wwwroot . '/blocks/competency_iena/competency_iena_user.php?courseid=' . $COURSE->id . '&competencyid=';
			
			$instance = new view_competency_iena_user();
			$tab_competency_history = $instance->get_competency_history($tmpiduser, $competencyid);
			
			
			try {
				if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
					$tab_comments = $DB->get_records_sql('select * FROM {block_competency_iena_com}
                                                        WHERE idstudent = ? AND idcompetency = ?', array($tmpiduser, $_GET['competencyid']));
				} else {
					$tab_comments = $DB->get_records_sql('select * FROM {block_competency_iena_com}
                                                        WHERE idstudent = ? AND idcompetency = ?', array($USER->id, $_GET['competencyid']));
				}
			} catch (coding_exception $e) {
			} catch (dml_exception $e) {
			}
			
			?>
   
<!--VIEW-->

            <div class="row">
                <div class="col-md-6">
                    <section id="name" class="block_compet_param">
                        <div class="row">
                            <div class="titre_section">
                                <h2 style="max-width: 2%;"><?php echo $competency_instance->shortname ?></h2>
                            </div>
                        </div>
                        <div class="wrapper">

                            <div style="margin-left: 1rem;">
								<?php
									echo "<div class=\"small\">
                                <p>$competency_instance->description</p>
                                </div>
                        </div>
                        "; ?>
                                <!--   TODO -->
                                <div class="more">
                                    <a href="#">Afficher plus d'informations</a>
                                </div>
                            </div>

                    </section>
                </div>
                <div class="col-md-6">
                    <section id="change" class="">
						<?php
							try {
								if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
									try {
										echo
											"
                                <p>" . get_string('chang_student', 'block_competency_iena') . "</p>
                                <span class=\"myarrow\">
                                    <input list=\"student_browser\" id=\"myStudentBrowser\" name=\"myStudentBrowser\" placeholder=\"";
									} catch (coding_exception $e) {
									}
									if ($_GET['studentid']) {
										foreach ($students as $student) {
											if ($student->id == $tmpiduser) {
												echo $student->firstname . " " . $student->lastname;
											}
										}
									} else {
										echo $USER->firstname . " " . $USER->lastname;
									}
									echo "\">
                            </span>
                            <datalist id=\"student_browser\">";
									foreach ($students as $student) {
										echo "<option data-value=\"" . $link2 . $student->id . "\" value=\"" . $student->firstname . " " . $student->lastname . "\"></option>";
									}
									try {
										echo
											"</datalist>
                            
                            
                                <p>" . get_string('change_comp', 'block_competency_iena') . "</p>
                                <span class=\"myarrow\">
                                    <input list=\"competency_browser\" id=\"myCompetencyBrowser\" name=\"myCompetencyBrowser\" placeholder=\"" . $competency_instance->shortname . "\"/>
                                </span>
                                <datalist id=\"competency_browser\">";
									} catch (coding_exception $e) {
									}
									foreach ($competencies as $competency) {
										echo "<option data-value=\"" . $link . $competency->id . "&studentid=" . $tmpiduser . "\" value=\"" . $competency->shortname . "\"></option>";
									}
									echo
									"</datalist>
                        
                            ";
								} else {
									echo "
                        <div class=\"centered\">
                            <p>" . get_string('change_comp', 'block_competency_iena') . "</p>
                            <span class=\"myarrow\">
                                <input list=\"competency_browser\" id=\"myCompetencyBrowser\" name=\"myCompetencyBrowser\" placeholder=\"" . $competency_instance->shortname . "\" />
                            </span>
                            <datalist id=\"competency_browser\">";
									foreach ($competencies as $competency) {
										echo "<option data-value=\"" . $link . $competency->id . "\" value=\"" . $competency->shortname . "\"></option>";
									}
									echo
									"</datalist>
                        </div>
                            ";
								}
							} catch (coding_exception $e) {
							}
						?>

                    </section>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <section id="list_module" class="block_compet_param">
                        <h2 class="title_blue">
							<?php try {
								echo get_string('eval_2', 'block_competency_iena');
							} catch (coding_exception $e) {
							} ?>
                        </h2>
                        <div style="margin-left: 2rem;">
                            <div style="display: flex;"><h4>
                                    <?php try {
										echo get_string('comp_2', 'block_competency_iena');
									} catch (coding_exception $e) {
									} ?>
                                    <span style="color: #d68d01;"><?php echo $data->usercompetencysummary->usercompetencycourse->proficiencyname ?></span>
                                </h4></div>
                            <div style="display: flex;"><h4>
                                    <?php try {
										echo get_string('eval_3', 'block_competency_iena');
									} catch (coding_exception $e) {
									} ?>
                                    <span style="color: #d68d01;"><?php echo $data->usercompetencysummary->usercompetencycourse->gradename ?></span>
                                </h4></div>
                        </div>
                        <?php if (has_capability('moodle/course:update',
                            $context = context_course::instance($COURSE->id), $USER->id)) {
                            echo "<button type=\"button\"
                           class=\"btn btn-primary btn-block\" id='eval-show'>".get_string('evalute_iena', 'block_competency_iena')."</button>
                            <div id='eval-hide'>
                            <select class=\"form-control\" style='margin-left: 2rem; width: 70%; display: inline-block; margin-right: 2em;' id=\"slc-eval\">";
                            foreach ($scale->scale_items as $key => $item){
                                echo "<option value='$key'>$item</option>";
                            }
                            echo "</select>";
                            echo "
                                <input type='hidden' value='$tmpiduser' id='iduser_iena'>
                                <input type='hidden' value='$competencyid' id='compid_iena'>
                                <input type='hidden' value='$COURSE->id' id='courseid_iena'>
                                <input type='hidden' value='$CFG->wwwroot' id='wwwroot_iena'>
                                <button type='button' class='btn btn-primary btn' id='btn-eval'>Valider</button>
                                </div>";
                        } else {
                           echo " <button type=\"button\" onclick='AskValide(\"$competencyid\",\"$tmpiduser\",1,\"$COURSE->id\",\"$CFG->wwwroot\")' 
                           class=\"btn btn-primary btn-block\" id='show-valide'>".get_string('ask_demande', 'block_competency_iena')."</button>";
                            echo " <button type=\"button\" onclick='AskValide(\"$competencyid\",\"$tmpiduser\",0,\"$COURSE->id\",\"$CFG->wwwroot\")' 
                           class=\"btn btn-primary btn-block\" id='cancel-valide'>".get_string('cancel_demande', 'block_competency_iena')."</button>";
                         } ?>
                    </section>
                </div>
                <div class="col-md-6">
                    <section id="list_module" class="block_compet_param">
                        <h2 class="title_blue">
							<?php try {
								echo get_string('list_modules', 'block_competency_iena');
							} catch (coding_exception $e) {
							} ?>
                        </h2>
                        <div style="overflow-y: scroll; height:15rem;">
							<?php
								
								foreach ($module_comp as $module) {
									$moduleI = new block_competency_iena_ressource();
									$moduleI->get_ressource_by_id($module->moduleid);
									$completion = $module->get_completions_by_module($tmpiduser, $COURSE->id, $module->moduleid);
									$pictureC = "";
									if ($moduleI->completion == 0) {
										$pictureC = "";
									} else if (!$completion) {
										$pictureC = "<img class=\"icon\" src=\"$CFG->wwwroot/theme/image.php/boost/core/1/i/completion-manual-n\">";
									} else if ($completion->completionstate == 1) {
										$pictureC = "<img class=\"icon\" src=\"$CFG->wwwroot/theme/image.php/boost/core/1/i/completion-manual-y\">";
									} else if ($completion->completionstate == 2) {
										$pictureC = "<img class=\"icon\" src=\"$CFG->wwwroot/theme/image.php/boost/core/1/i/completion-auto-pass\">";
									}
									echo "<a href=\"$moduleI->link\" class=\"list-group-item list-group-item-action flex-column align-items-start\">
                    <div class=\"d-flex w-100 justify-content-between\">
                        <h5 class=\"mb-1\"><div class='row'><div class='col-md-11'><img class=\"\" alt=\"\" src=\"$CFG->wwwroot/theme/image.php/boost/$moduleI->type/1/icon>\">
                       $moduleI->name </div> <div class='col-md-1'> $pictureC</div></div></h5></div></a>";
								}
							
							?>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <section id="discussion" class="block_compet_param">
                        <h2 class="title_blue">
							<?php try {
								echo get_string('discus', 'block_competency_iena');
							} catch (coding_exception $e) {
							} ?>
                        </h2>


                        <div class="block_compet_param commentaire">
                            <div class="more">
                                <div data-region="comments" class="m-t-1">
                                    <div class="mdl-left" id="yui_3_17_2_1_1522940951276_214">
                                        <a style="cursor:pointer;" class="comment-link"
                                           id="comment-link-plancommentarea2" aria-expanded="true"
                                           onclick="open_comment();">
                                            <i class="icon fa fa-plus-square fa-fw " style="color:#1587bc;"
                                               aria-hidden="true" title="Commentaires" aria-label="Commentaires"></i>
                                            <span id="comment-link-text-plancommentarea1"><?php try {
		                                            echo get_string('comment', 'block_competency_iena');
	                                            } catch (coding_exception $e) {
	                                            } ?>
                                                (<?php echo count($tab_comments); ?>)</span>
                                        </a>
                                        <div id="comment-ctrl-plancommentarea2" class="comment-ctrl"
                                             style="display: block;">
                                            <ul id="comment-list-plancommentarea2" class="comment-list"
                                                style="display: none;">
												<?php
													foreach ($tab_comments as $comment) {
														echo "
                                            <li id=\"comment-3-plancommentarea2\">
                                                <div class=\"comment-message\">
                                                    <div class=\"comment-message-meta m-r-3\">
                                                        <span class=\"picture\">
                                                            <a href=\"" . $CFG->wwwroot . "/user/profile.php?id=" . $comment->idsender . "\">
                                                                <img src=\"" . $CFG->wwwroot . "/theme/image.php/boost/core/1522839783/u/f2\" alt=\"\" title=\"\" class=\"userpicture defaultuserpic\" width=\"18\" height=\"18\">
                                                            </a>
                                                        </span>
                                                        <span class=\"user\">
                                                            <a href=\"" . $CFG->wwwroot . "/user/view.php?id=2&amp;course=" . $comment->idcourse . "\">";
														
														try {
															$the_sender = $DB->get_record_sql('select * FROM {user} WHERE id = ?', array($comment->idsender));
														} catch (dml_exception $e) {
														}
														echo $the_sender->firstname . " " . $the_sender->lastname .
															
															"</a>
                                                        </span> - <span class=\"time\">"
															
															. date('Y-m-d H:i:s', $comment->date) .
															
															
															"</span>
                                                    </div>
                                                    <div class=\"text\"><div class=\"comment-delete\">
                                                        <a  style=\"cursor:pointer;\" onclick=\"delete_comment(this);\" role=\"button\" id=\"" . $comment->id . "\" title=\"Supprimer le commentaire de " . $comment->idsender . " écrit le " . date('m/d/Y', $comment->date) . "\">
                                                            <i class=\"icon fa fa-trash fa-fw \" aria-hidden=\"true\" title=\"Supprimer le commentaire de " . $comment->idsender . " écrit le " . date('m/d/Y', $comment->date) . "\" aria-label=\"Supprimer le commentaire de " . $comment->idsender . " écrit le " . date('m/d/Y', $comment->date) . "\"></i>
                                                        </a>
                                                        </div>
                                                        <div class=\"no-overflow\">
                                                            <div class=\"text_to_html\">" . $comment->message . "<br></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <hr>";
													}
												?>
                                            </ul>
                                            <div class="comment-area">
                                                <textarea name="context" id="input_message" rows="2"
                                                          class="fullwidth"></textarea>
                                                <div class="fd" id="">
                                                    <button class='btn' style="margin-top:2%;" id="
                                                    <?php
														try {
															if (has_capability('moodle/course:update', $context = context_course::instance($COURSE->id), $USER->id)) {
																if (!empty($_GET)) {
																	echo "sender" . $USER->id . "-student" .$tmpiduser . "-course" . $_GET['courseid'] . "-competency" . $_GET['competencyid'];
																}
															} else {
																echo "sender" . $USER->id . "-student" . $USER->id . "-course" . $_GET['courseid'] . "-competency" . $_GET['competencyid'];
															}
														} catch (coding_exception $e) {
														}
													?>
                                                   "
                                                            onclick='write_comment(this)'><?php echo get_string('save_comment', 'block_competency_iena'); ?></button>
                                                </div>
                                            </div>
                                            <div class="clearer"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                </div>


            

            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <section id="history" class="block_compet_param">
                        <h2 class="title_blue">
							<?php echo get_string('history_comp', 'block_competency_iena'); ?>
                        </h2>
						<?php
							$data = $competency_instance->get_data($tmpiduser, $competencyid, $COURSE->id);
							$taille_proof = count($data->usercompetencysummary->evidence) * 12.5;
							if ($taille_proof > 25) {
								$taille_proof = 25;
							}
							if ($taille_proof != 0) {
								?>
                                <div class="block_compet_param commentaire" id="proof_iena"
                                     style="height: <?php echo $taille_proof ?>rem">
                                    <div class="entete">
										
										<?php
											
											foreach ($data->usercompetencysummary->evidence as $value) {
												echo "<div class=\"well well-small evidence\" id=\"proof_iena_id_$value->id\">
                        <div class=\"pull-xs-right\">
                            <a href=\"#\" onclick='deleteProof(\"$value->id\",\"$COURSE->id\",\"$CFG->wwwroot\")' ><i class=\"icon fa fa-trash fa-fw \" aria-hidden=\"true\" aria-label=\"\"></i></a>
                        </div>
                    <div>
                        <span>
                            <a href=\"" . $value->actionuser->profileurl . "\" title=\"Consulter le profil\">
                            <img height=\"18\" src=\"" . $value->actionuser->profileimageurlsmall . "\" alt=\"\" role=\"presentation\">
                            <span>" . $value->actionuser->fullname . "</span>
                            </a>
                        </span>
                    </div>
                <strong><time>";
												date_default_timezone_set('Europe/Paris');
												echo strftime("%A %d %B %Y", $value->timemodified);
												echo "</time></strong>
                    <p><span class=\"tag tag-info\">$value->gradename</span></p>
                <p>$value->description</p>
                    <blockquote>$value->note</blockquote>
                </div>";
											} ?>

                                    </div>
                                </div>
								<?php if ($taille_proof == 25) { ?>
                                    <div class="align_center" id="extand_iena_proof" style="cursor: pointer;">
                         <span class="down_caret">
                                            <i class="fa fa-chevron-down"></i>
                    </span>
                                    </div> <?php }
							} else {
								try {
									echo "<p style='padding:1rem; '>". get_string('empty', 'block_competency_iena')."</p>";
								} catch (coding_exception $e) {
								}
							} ?>
                    </section>
                </div>

            </div>
            </div>
            
            <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
            <script type="text/javascript" charset="utf8"
                    src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
            <script>

                function delete_comment(obj) {
                    var url = window.location.href;

                    var t = obj.parentNode.parentNode.parentNode.parentNode;
                    var info = [];
                    info[0] = "delete";
                    info[1] = obj.id;


                    $.ajax({
                        type: "POST",
                        data: {info: info},
                        url: url,
                        timeout: 10000,
                        contentType: 'application/x-www-form-urlencoded',
                        success: function (data, status) {
                            if (status == "success") {
                                console.log(status);
                                console.log(data);
                                location.reload();
                            }
                        },
                        error: function (xhr, status, error) {
                            alert(status);
                        }
                    });


                }

                function open_comment() {
                    if (document.getElementById("comment-list-plancommentarea2").style.display === 'none') {
                        document.getElementById("comment-list-plancommentarea2").style.display = 'block';
                        console.log(document.getElementById("comment-list-plancommentarea2").style.display);
                    } else {
                        document.getElementById("comment-list-plancommentarea2").style.display = 'none';
                    }

                }


                function AskValide(competencyID, userId, value, idcourse,wwwroot){
                    $("#show-valide").toggle();
                    $("#cancel-valide").toggle();
                    console.log(competencyID,userId);
                     $.ajax({
                         url:  wwwroot+'/blocks/competency_iena/competency_iena_competencies_api.php?courseid='+idcourse,
                         type: 'POST',
                         timeout: 10000,
                         contentType: 'application/x-www-form-urlencoded',
                         data: {askvalide:value,askidcomp:competencyID,iduser:userId},
                         success: function(result) {
                         }
                     });
                }

                function write_comment(obj) {
                    var url = window.location.href;
                    console.log(url);
                    var input_message_id = obj.id;
                    console.log(input_message_id);
                    var idsender = input_message_id.split('-')[0];
                    idsender = idsender.replace(/[^\d.]/g, '');
                    console.log(idsender);
                    var idstudent = input_message_id.split('-')[1];
                    idstudent = idstudent.replace(/[^\d.]/g, '');
                    console.log(idstudent);
                    var idcourse = input_message_id.split('-')[2];
                    idcourse = idcourse.replace(/[^\d.]/g, '');
                    console.log(idcourse);
                    var idcompetency = input_message_id.split('-')[3];
                    idcompetency = idcompetency.replace(/[^\d.]/g, '');
                    console.log(idcompetency);
                    var message = document.getElementById("input_message").value;
                    console.log(message);
                    var date = new Date().getTime() / 1000;
                    console.log(date);

                    var info = [];
                    info[0] = "insert";
                    info[1] = idsender;
                    info[2] = idstudent;
                    info[3] = idcourse;
                    info[4] = idcompetency;
                    info[5] = message;
                    // info[6] = date ;

                    $.ajax({
                        type: "POST",
                        data: {info: info},
                        url: url,
                        timeout: 10000,
                        contentType: 'application/x-www-form-urlencoded',
                        success: function (data, status) {
                            if (status == "success") {
                                console.log(status);
                                console.log(data);
                                location.reload();
                            }

                        },
                        error: function (xhr, status, error) {
                            alert(status);
                        }
                    });


                }

                $(document).ready(function () {

                    $('#cancel-valide').hide();
                    $('#eval-hide').hide();
                    $('#eval-show').click(function () {
                        $('#eval-show').hide();
                        $('#eval-hide').toggle();
                    });

                    $('#btn-eval').click(function () {
                        $('#eval-hide').toggle();
                        $('#eval-show').show();
                        $.ajax({
                            url: $("#wwwroot_iena").val() + '/blocks/competency_iena/' +
                            'competency_iena_competencies_api.php?courseid=' + $("#courseid_iena").val(),
                            type: 'POST',
                            timeout: 10000,
                            contentType: 'application/x-www-form-urlencoded',
                            data: {eval_comp: $("#slc-eval").val(),userid : $("#iduser_iena").val(), compid : $("#compid_iena").val()},
                            success: function (result) {
                                location.reload();
                            }
                        });


                    });

                    $('.wrapper').find('a[href="#"]').on('click', function (e) {
                        e.preventDefault();
                        this.expand = !this.expand;
                        $(this).text(this.expand ? "Réduire" : "Voir la description compléte");
                        $(this).closest('.wrapper').find('.small, .big').toggleClass('small big');
                    });

                    $('#extand_iena_proof').click(function () {
                        $('#proof_iena').css('overflow', 'visible');
                        $('#proof_iena').css('height', 'auto');
                        $('#extand_iena_proof').hide();
                    });

                    $("#myCompetencyBrowser").on('input', function () {
                        var val = this.value;
                        if ($('#competency_browser option').filter(function () {
                            return this.value === val;
                        }).length) {
                            var value3send = document.querySelector("#competency_browser option[value='" + this.value + "']").dataset.value;
                            window.location.replace(value3send);
                        }
                    });


                    $("#myStudentBrowser").on('input', function () {
                        var val = this.value;
                        if ($('#student_browser option').filter(function () {
                            return this.value === val;
                        }).length) {
                            var value2send = document.querySelector("#student_browser option[value='" + this.value + "']").dataset.value;
                            window.location.replace(value2send);
                        }
                    });
                });

                function show_parag() {
                    if (document.getElementById("AffichePlusInfos").innerHTML == 'Réduire') {
                        document.getElementById("AffichePlusInfos").innerHTML = 'Afficher plus d\'informations';
                        document.getElementById("parag").classList.add('para_descript');
                    }
                    else {
                        document.getElementById("AffichePlusInfos").innerHTML = 'Réduire';
                        document.getElementById("parag").classList.remove('para_descript');
                    }

                }

                function deleteProof(value, idcourse, wwwroot) {
                    console.log(value);
                    $.ajax({
                        url: wwwroot + '/blocks/competency_iena/competency_iena_competencies_api.php?courseid=' + idcourse,
                        type: 'POST',
                        timeout: 10000,
                        contentType: 'application/x-www-form-urlencoded',
                        data: {delproof: value},
                        success: function (result) {
                            $('#proof_iena_id_' + value).hide();
                        }
                    });
                }


            </script>
			
			<?php
		}
	}

?>