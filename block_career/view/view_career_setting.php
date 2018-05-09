<?php

class view_career_setting extends moodleform
{
    public function definition()
    {
        // TODO: Implement definition() method.
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

              //Default value
    }

    public function get_content()
    {
        global $DB, $CFG;

        $mform = $this->_form;
        $careerId = optional_param('id', NULL, PARAM_INT);
        $course = required_param('course', PARAM_INT);

        $name = "";
        $description = "";
        $ressourcesId = "";
        $contentButton = "Ajouter un parcours";
        $imagePath = "";

        if (isset($careerId) && !empty($careerId))
        {
            $requete = $DB->get_record_sql('SELECT * FROM {block_career} WHERE id = ?', array($careerId));
            $name = $requete->name;
            $description = $requete->description;
            $ressourcesId = explode(",", $requete->ressources);
            $contentButton = "Modifier le parcours";
            $imagePath = $requete->image;

        }


        $mform->addElement('text','careerName',get_string('titleaddname_plugin', 'block_career'));
        $mform->addRule('careerName', get_string('error'), 'required', null, null, false, false);
        $mform->setDefault('careerName',$name);

        $mform->addElement('editor', 'descriptionName', get_string('titleadddesc_plugin', 'block_career'));
        $mform->setType('descriptionName', PARAM_RAW);
        $mform->addRule('descriptionName', get_string('error'), 'required', null, null, false, false);
        $mform->setDefault('descriptionName',array('text'=>$description));

        //FilePicker IMAGE
        //$mform->addElement('filepicker', 'imageName', get_string('titleaddimg_plugin', 'block_career'), null);


        $content = "<h1>" . get_string('title_plugin', 'block_career') . "</h2>";
        $content .= "<p>" . get_string('heading_plugin', 'block_career') . "</p>";

        $temp = $mform->toHtml();


        $temp = substr($temp,(strpos($temp,'>')+1));
        $temp = substr($temp,0, -7);
        $content .= '<div class=""><form action="career_setting.php?course=' . $course . '" method="post" enctype="multipart/form-data">';

        $content .= $temp;
        $content .= ' <section class="section"><h3>' . get_string('titleaddimg_plugin', 'block_career') . '</h3>
                    <input type="file" class="input" name="imageName" accept="image/*" /></section>';


        $content .= ' <section class="section"><h3>' . get_string('titleaddelem_plugin', 'block_career') . '</h3>
	            <p>' . get_string('titleaddelemdesc_plugin', 'block_career') . '</p>
	            <div class="left_course_elements">
	                <div class="title">Cours</div>
		            <div class="subject-info-box-1">
                        <select multiple="multiple" id="lstBox1" class="form-control">';

        $sections = block_career_section::get_sections_by_id_course($course);

        foreach ($sections as $section)
        {
            $section->ressources = block_career_ressource::get_ressources_by_id_section($section->id);
        }

        foreach ($sections as $section)
        {
            $content .= '<optgroup label="'.$section->name.'" value="'.$section->id.'">';

            foreach ($section->ressources as $ressource)
            {
                $content .= '<option label="'.$ressource->name.'" value ="'.$ressource->id.'" name="'.$ressource->id.'">'.$ressource->name.'</option>';
            }
            $content .= '</optgroup>';
        }

        $content .='</select></div></div>';

        $content .= '<div class="middle_elements">
                        <div class="title">Actions</div>
                            <div class="subject-info-arrows text-center">
                                <input type="button" id="btnAllRight" value=">>" class="btn btn-default" /><br />
                                <input type="button" id="btnRight" value=">" class="btn btn-default" /><br />
                                <input type="button" id="btnLeft" value="<" class="btn btn-default" /><br />
                                <input type="button" id="btnAllLeft" value="<<" class="btn btn-default" />
                            </div>
                        </div>';

        $content .= '<div class="right_course_elements">
                        <div class="title">Parcours</div>
                            <div class="subject-info-box-2">
                            <select multiple="multiple" id="lstBox2" name="ressource[]" class="form-control" required>';


        foreach ($ressourcesId as $value)
        {
            $res = new block_career_ressource();
            $res->get_ressource_by_id($value);

            if ($careerId != 0)
                $content .= '<option label="'.$res->name.'" value ="'.$res->id.'">';
        }

        
        $content .=  '</select>
                        </div>
                        <div class="clearfix"></div>
                     </div>
                  </section>';

        $content .= '<script>
                        function selectAll()
                        {
                            selectBox = document.getElementById("lstBox2");
                            
                            var checkValue = 0;
                            
                            for (var i = 0; i < selectBox.options.length; i++)
                            { 
                                selectBox.options[i].selected = true;
                            }

                            $("#lstBox2 :selected").map(function(i, el) {
                                
                                if (checkValue == $(el).val())
                                    $(el).remove();
                                    
                                checkValue = $(el).val();
                            });
                        }
                    </script>';

        if ($careerId != 0) {
            $content .= '<input type="hidden" name="imagePath" value="'.$imagePath.'">';
        }

        $content .= '<section class="section">
                        <div class="row">  
                            <div class="col-lg-3 col-md-3 padding_column">
                                <input type="hidden" name="careerId" value="'.$careerId.'">
                                <button type="submit" onclick="selectAll();" class="btn btn-primary btn-lg btn-block btn-block-tide"><i class="fa fa-plus"></i> '.$contentButton.'</button>
                            </div>
                        </div>
                     </section>';


        $content .= '</div></form>';

        if ($careerId != 0)
            $content .= "<a href='$CFG->wwwroot/blocks/career/career_setting.php?course=$course&delete=1&id=$careerId' class='btn btn_red'> Supprimer </a>";

        $content .= "<a href='$CFG->wwwroot/blocks/career/career_list.php?course=$course' class='btn btn_reset'> Annuler </a>";

        return $content;
    }

}