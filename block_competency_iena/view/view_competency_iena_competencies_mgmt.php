<?php
	
	class view_competency_iena_competencies_mgmt
	{
		
		public function get_content($courseid)
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


<script>

$(document).ready(function() {
    $('#tab_mgmt').DataTable({
       responsive: true,
        
       \"language\": {
                \"url\": \"https://cdn.datatables.net/plug-ins/1.10.16/i18n/French.json\"
            },
       \"bSort\": false,
       scrollCollapse: true,
       fixedColumns: {
            leftColumns: 1
        }
    });
    
    setTimeout(function() {
		    $('#tab_mgmt_filter').hide();
		    $('#tab_mgmt_length').hide();
		    $('#tab_mgmt_info').hide();
	    }, 500);    
    
    $('.fram-iena').hide();
    $('#btn-comp-iena').hide();
    
    $( '#select-framework' ).on( 'change', function () {
	       $('.fram-iena').hide();
	       $('#fram-'+$(this).val()).show();
	    } );
    
    
    $('#btn-comp-iena').click(function(){
       var idcomp =  $('#id-comp-iena').val();
       var idcourse =  $('#id-course-iena').val();
       var value = [];
       value[0] = idcomp;
       value[1] = idcourse;
        $.ajax({
		    url: '/blocks/competency_iena/competency_iena_competencies_api.php?courseid='+idcourse,
		    type: 'POST',
		    timeout: 10000,
	        contentType: 'application/x-www-form-urlencoded',
	        data: {addcomp:value},
		    success: function(result) {
		        //console.log(result.trim());
		        if (result.trim() == \"true\"){
		            location.reload();
		        } else {
		            alert('La compétence est déja lié au cours');
		        }
		    }
		});
       
    });
    
    $('#filter-iena-competence').keyup(function(){
        
        var searchText = $(this).val();
        $('ul > li').each(function(){
            
            var currentLiText = $(this).text(),
                showCurrentLi = currentLiText.indexOf(searchText) !== -1;
                    if (searchText){
            $(this).toggle(showCurrentLi);} else {
                        
              $(this).hide(showCurrentLi);
              $('#fram-'+$( '#select-framework' ).val()).show();
            }
        });
    });
    
});

function showFilter(obj)
{
    var p = obj.parentNode;
    var c = p.children;

    c[0].setAttribute(\"style\", \"display:none\");
    c[1].setAttribute(\"style\", \"display:block\");
    c[2].setAttribute(\"style\", \"display:none\");
  
}

function supprimer_lien(obj)
{
    var t = obj.parentNode;
    var p = t.parentNode;   
    var c = p.children;

    c[0].setAttribute(\"style\", \"display:none\");
    c[1].setAttribute(\"style\", \"display:none\");
    c[2].setAttribute(\"style\", \"display:block\");
    
    console.log(c[0]);
    
    var div_id = c[0].id;
    console.log(div_id);
    
    var module_id = div_id.split('-')[0];
    module_id = module_id.replace ( /[^\d.]/g, '' );

    var competency_id = div_id.split('-')[1];
    competency_id = competency_id.replace ( /[^\d.]/g, '' );

    var url = window.location.href;
    console.log(url);
    
    var info = [];
    info[0] = module_id ;
    info[1] = competency_id ;
    info[2] = \"delete\" ;
    
    console.log(info);


    $.ajax({
        type: \"POST\",
        data: {info:info},
        url: url,
        timeout: 10000,
        contentType: 'application/x-www-form-urlencoded',
        success: function (data, status) 
        {
            if (status == \"success\")
            {
                console.log(status);
                console.log(data);
                changeColor(p); 
            }
        },
        error: function (xhr, status, error) 
        {
            alert(status);
        }
    });



}


function select_update(obj)
{
    var t = obj.parentNode;
    var p = t.parentNode;   
    var c = p.children;

    c[0].setAttribute(\"style\", \"display:block\");
    c[1].setAttribute(\"style\", \"display:none\");
    c[2].setAttribute(\"style\", \"display:block\");
    
    var tab0_elem = c[1].children;
    var select = tab0_elem[0];
    
    var choix_ruleoutcome =select.value;
    
    var select_id =select.id;
    
    var module_id = select_id.split('-')[0];
    module_id = module_id.replace ( /[^\d.]/g, '' );

    var competency_id = select_id.split('-')[1];
    competency_id = competency_id.replace ( /[^\d.]/g, '' );

    var url = window.location.href;
    
    var info = [];
    info[0] = module_id ;
    info[1] = competency_id ;
    info[2] = choix_ruleoutcome ;
    
    $.ajax({
        type: \"POST\",
        data: {info:info},
        url: url,
        timeout: 10000,
        contentType: 'application/x-www-form-urlencoded',
        success: function (data, status) 
        {
            if (status == \"success\")
            {
                //console.log(status);
                //console.log(data);
                var renderer = data.split(\"/\")[1];
                var switchColor = data.split(\"/\")[0];
                
                if (!(switchColor.trim() == \"update\"))
                {
                    changeColor(p);
                }
                
                var div_id = c[0].id;
                document.getElementById(div_id).firstChild.nextSibling.textContent = renderer;
            }
                
        },
        error: function (xhr, status, error) 
        {
            alert(status);
        }
    });


}    


function changeColor(obj)
{
    newColor = (obj.style.backgroundColor == '') ? '#28afa3' : '';
    obj.style.backgroundColor = newColor;
}


</script>


<h3> " . get_string('manage_comp', 'block_competency_iena') . " </h3>
<p>" . get_string('add_comment', 'block_competency_iena') . "</p>


<table id=\"tab_mgmt\" class=\"table-bordered display dataTable\" style=\"width:100%\" >

<thead>
<tr>
<th></th>";
			foreach ($competencies as $competency) {
				$linkCompetence = $CFG->wwwroot . "/blocks/competency_iena/competency_iena_user.php?courseid=" . $COURSE->id . "&competencyid=" . $competency->id;
				$content .= "
<th style=\"max-width: 70px; overflow: hidden;\">
 <a href='$linkCompetence' style='color: black;'><p style=\"word-wrap: break-word;\">" . wordwrap($competency->shortname, 25, "<br>\n") . "</p></a></th>";
			}
			
			$content .= "
</tr>
</thead>


<tbody>";
			
			foreach ($sections as $section) {
				$content .= "
    <tr>
        <td bgColor=\"#4EA6CC\">" . $section->name . "</td>";
				foreach ($competencies as $competency) {
					$content .= "<td class=\"td_section\"></td>";
//                dynamic class suffix to put in verse if all green td_modules
				}
				$content .= "
    </tr>";
				
				foreach ($modules as $module) {
					if ($module->sectionid == $section->id) {
						$moduleI = new block_competency_iena_ressource();
						$moduleI->get_ressource_by_id($module->moduleid);
						$content .= "
    <tr>
        <td><a href='$moduleI->link;' style='color: black;'>" . $module->name . "</a></td>";
						
						$module_competencies = $competency_instance->get_competencies_by_moduleID($module->moduleid);
						foreach ($competencies as $competency) {
							$indic = false;
							foreach ($module_competencies as $module_competency) {
								if ($competency->id == $module_competency->id) {
									$content .= "
                            <td style=\"background-color: #65BEB6;\" align=\"right\">
                                <div style=\"display:block\" id=\"divmodule" . $module->moduleid . "-competence" . $competency->id . "\">
                                    <p>" . $module_competency->module_ruleoutcomestring . "</p>";
									
									$indic = true;
									break;
								}
							}
							if (!$indic) {
								$content .= "
                            <td align=\"right\">
                                <div style=\"display:none\" id=\"divmodule" . $module->moduleid . "-competence" . $competency->id . "\">
                                    <p></p>";
							}
							$content .= "
                                        <i class=\"fa fa-remove icon-red-iena\" style=\"font-size:24px;color:#dc493a\" onclick='supprimer_lien(this)'>
                                        </i>
                                   
                                </div> 
                                <div style=\"display:none\">
                                    <select id=\"selmodule" . $module->moduleid . "-competence" . $competency->id . "\">
                                        <option value=\"0\">" . get_string('nothing', 'block_competency_iena') . "</option>
                                        <option value=\"1\">" . get_string('add_proof', 'block_competency_iena') . "</option>
                                        <option value=\"2\">" . get_string('send_valide', 'block_competency_iena') . "</option>
                                        <option value=\"3\">" . get_string('mark_ok_competency', 'block_competency_iena') . "</option>
                                    </select>
                                   
                                        <i class=\"fa fa-check icon-green-iena\" onclick='select_update(this)'>
                                        </i>
                                   
                                </div>
                               
                                    <i class=\"fa fa-edit icon-blue-iena\" onclick='showFilter(this)'>
                                    </i>
                                
                            </td>";
						}
						
						$content .= "
    </tr>";
					}
				}
				
				
			}
			$content .= "
</tbody>

</table>
";
			
			$content .= "<button type=\"button\" style=\"margin-top: 2%;\" class=\"btn btn-lg\" data-toggle=\"modal\" data-target=\"#myModal\">" . get_string('add_comp', 'block_competency_iena') . "</button>";
			
			
			/* Here retrieve the list of repositories for a court
			 Retrieve the list of skills for a repository */
			
			$refI = new block_competency_iena_referentiel();
			$frameworks = $refI->get_referentiels_all();
			
			
			$content .= "<div class=\"modal fade\" id=\"myModal\" role=\"dialog\">
    <div class=\"modal-dialog modal-lg\">
    
      <!-- Modal content-->
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
          <h4 class=\"modal-title\">" . get_string('select_comp', 'block_competency_iena') . "</h4>
        </div>
        <div class=\"modal-body\">
        <div class='row'>       
            <div class='col-md-5 col-lg-5 form-group'>
          <h2>" . get_string('referentiel', 'block_competency_iena') . "</h2>
          </div>
          <div class='col-md-7 col-lg-7'>";
			
			if (has_capability('moodle/competency:competencymanage', $context = context_course::instance($COURSE->id), $USER->id)) {
				$content .= "<button style=' visibility: hidden;' class='btn' id='change_ref' data-toggle=\"modal\"  data-target=\"#myModalRef\">" . get_string('modify_ref', 'block_competency_iena') . "</button>";
			};
			
			$content .= "</div></div>
        
          <div class='row'>
            <div class='col-md-5 col-lg-5 form-group'>
                <input placeholder='Filtrer les référentiels' type='text' id='input-framework' class='form-control' style='margin-bottom: 25px;' onkeyup=\"filter()\">
                <select id='select-framework' size=\"6\" class='form-control'  style='margin-bottom: 25px;'>
                        ";
			foreach ($frameworks as $framework) {
				$content .= "<option onclick=\"updateTextRef('" . $framework->id . "','" . $COURSE->id . "','" . $CFG->wwwroot . "');\" value=" . $framework->id . ">" . $framework->shortname . "</option>";
			}
			$content .= "
                </select>
                <h2>" . get_string('competency', 'block_competency_iena') . "</h2>
                <input placeholder='Filtrer les compétences du référentiel' type='text' id='filter-iena-competence' class='form-control' style='margin-bottom: 25px;'>
                <div class=\"well well-lg\">
                    <ul id='tree3'>
                ";
			foreach ($frameworks as $framework) {
				$order_competency = $refI->get_competences_order_by_ref($framework->id);
				$content .= "<li class='fram-iena' id='fram-" . $framework->id . "'>" . $framework->shortname . "<ul>";
				foreach ($order_competency as $one) {
					$content .= "<li><a href='#' onclick=\"updateTextComp('" . $one->competency->id . "','" . $COURSE->id . "','" . $CFG->wwwroot . "');\">" . $one->competency->shortname . "</a><ul>";
					if ($one->children) {
						foreach ($one->children as $two) {
							$content .= "<li><a href='#' onclick=\"updateTextComp('" . $two->competency->id . "','" . $COURSE->id . "','" . $CFG->wwwroot . "');\">" . $two->competency->shortname . "</a><ul>";
							if ($two->children) {
								foreach ($two->children as $tree) {
									$content .= "<li><a href='#' onclick=\"updateTextComp('" . $tree->competency->id . "','" . $COURSE->id . "','" . $CFG->wwwroot . "');\">" . $tree->competency->shortname . "</a><ul>";
									if ($tree->children) {
										foreach ($tree->children as $four) {
											$content .= "<li><a href='#' onclick=\"updateTextComp('" . $four->competency->id . "','" . $COURSE->id . "','" . $CFG->wwwroot . "');\">" . $four->competency->shortname . "</a></li>";
										}
									}
									$content .= "</ul></li>";
								}
							}
							$content .= "</ul></li>";
						}
					}
					$content .= "</ul></li>";
				}
				$content .= "</ul></li>";
			}
			
			$content .= "
            </ul>
            </div>
            </div>
           
            
            <div class='col-md-7 col-lg-7'>
            <div class=\"well well-lg\"><h3 id='name_ref_iena'>" . get_string('info_ref', 'block_competency_iena') . "</h3>
            <p id='desc_ref_iena'></p>
            </div>
            <div class=\"well well-lg\"><h3 id='name_comp_iena'>" . get_string('info_comp', 'block_competency_iena') . "</h3>
            <p id='desc_comp_iena'></p>
            <input id='id-comp-iena' type='hidden'>
            <input id='id-course-iena' value='$COURSE->id' type='hidden'>
            <button id='btn-comp-iena' type=\"button\" class=\"btn btn-primary btn-block\">" . get_string('add_comp2', 'block_competency_iena') . "</button>
            </div>
            </div>
            </div>
        </div>
      </div>
      
    </div>
  </div>";
			
			$content .= "<div class=\"modal fade\" id=\"myModalRef\" role=\"dialog\">
    <div class=\"modal-dialog modal-lg\" style='top: 33%;max-width: 25%;'>
    
      <!-- Modal content-->
      <div class=\"modal-content\" style='border: 5px solid rgba(0,0,0,.2);'>
        <div class=\"modal-header\">
          <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
          <h4 class=\"modal-title\">" . get_string('confirm', 'block_competency_iena') . "</h4>
        </div>
        <div class=\"modal-body\">
        <p>" . get_string('ask_confirm', 'block_competency_iena') . "</p>
        <div class='align_center'>
	        <button class='btn btn-danger' data-dismiss=\"modal\">" . get_string('no', 'block_competency_iena') . "</button>
	        <form action='" . $CFG->wwwroot . "/blocks/competency_iena/competency_iena_competency_mgmt.php?courseid=" . $COURSE->id . "' method='POST'>
	            <input type='hidden' id='ref_mod' value='' name='ref_mod'>
	            <button type='submit'  id='' class='btn btn-success' style='margin-left: 2rem '>" . get_string('yes', 'block_competency_iena') . "</button>
			</form>
		</div>
        
      
        </div>
        </div>
        </div>
        </div>
        ";
			
			
			$content .= " <script>$.fn.extend({
    treed: function (o) {
      
      var openedClass = 'glyphicon-minus-sign';
      var closedClass = 'glyphicon-plus-sign';
      
      if (typeof o != 'undefined'){
        if (typeof o.openedClass != 'undefined'){
        openedClass = o.openedClass;
        }
        if (typeof o.closedClass != 'undefined'){
        closedClass = o.closedClass;
        }
      };
      
        //initialize each of the top levels
        var tree = $(this);
        tree.addClass(\"tree\");
        tree.find('li').has(\"ul\").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend(\"<i class='indicator fa \" + closedClass + \"'></i>\");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + \" \" + closedClass);
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
      tree.find('.branch .indicator').each(function(){
        $(this).on('click', function () {
            $(this).closest('li').click();
        });
      });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
});

$('#tree3').treed({openedClass:'fa-caret-right', closedClass:'fa-caret-down'});
</script>";
			
			
			return $content;
		}
		
		
	}

?>

