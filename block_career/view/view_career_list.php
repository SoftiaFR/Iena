<?php
	
	
	class view_career_list
	{
		
		/**
		 * @return string
		 */
		public function get_content()
		{
			global $DB, $CFG;
			
			$content = "<h2>" . get_string('title_plugin', 'block_career') . "</h2>";
			$content .= "<p>" . get_string('heading_plugin', 'block_career') . "</p>";
			
			$request = $DB->get_records_sql('SELECT * FROM {block_career} WHERE course = ?', array($_GET["course"]));
			
			$image = "";
			
			foreach ($request as $value) {
				
				if (file_get_contents($value->image) != null) {
					$image = "<img src='$value->image' class='img_moodle_course'/>";
				}
				
				$content .= "<div class='card card_block'>
                    <div class='row'>
                        <div class='col-lg-1 col-md-1 padding_column align_center img_center' >$image</div>
                        <div class='col-lg-10 col-md-10 padding_column'><h3>$value->name</h3>$value->description</div>
                        <div class='col-lg-1 col-md-1 padding_column'><a style='color:black' href='$CFG->wwwroot/blocks/career/career_setting.php?course=" . $_GET["course"] . "&id=$value->id'><i class=\"fa fa-cog fa-2x\"></a></i></div>
                   </div>
                </div>";
			}
			
			if (empty($request)) {
				$content .= "<h4>" . get_string('any_carrer', 'block_career') . "</h4>";
			}
			// Button for adding course to the list
			$content .= "<div class='row'><div class='col-lg-3 col-md-3 padding_column'><a href='$CFG->wwwroot/blocks/career/career_setting.php?course=" . $_GET["course"] . "' class='btn btn-primary btn-lg btn-block btn-block-tide'><i class='fa fa-plus'></i>  " . get_string('add_course', 'block_career') . "</a></div></div>";
			
			
			return $content;
			
		}
		
	}