<script type=\"text/javascript\" charset=\"utf8\" src=\"https://code.jquery.com/jquery-3.3.1.min.js\"></script>

<script>
    $(document).ready(function () {
        $('.sectionH').hide();
        $('.' + $('#select-section').val()).show();
        $('#select-section').on('change', function () {
            $('.sectionH').hide();
            $('.' + this.value).show();
        });
    });
</script>

<form action="/course/format/iena/param_indicateur.php?courseid=<?= $COURSE->id; ?>" method="post">
	<section class="section" id="params">
		<div class="\">
			<h2 class="param"><?php get_string('indic_suivi', 'format_iena') ?></h2>
			<select class="select" name='select-section' id="select-section">
				<?php
					foreach ($sections as $section) {
						$checked = "";
						if ($section->id == $_GET['sectionid']) {
							$checked = "selected";
						}
						$content .= "<option value=\"section-" . $section->id . "\" " . $checked . ">" . $section->name . "</option>";
					}
				?>
			</select>
		</div>
		<div class="\">
			<h2 class="param"><?php get_string('check_completion', 'format_iena') ?></h2>
			<p><?php get_string('for_section_select', 'format_iena') ?></p>
		</div>
	</section>
	<?php
		foreach ($sections
		
		as $section) {
		$hidden_modules = $this->get_ressource_hide_indicator_new($section->id);
	?>
	<section class="section-<?= $section->id ?> sectionH ">
		<div class="heading_title">
			<p><?= $section->name ?></p>
		</div>
		<?php
			
			foreach ($hidden_modules
			
			as $hidden_mod) {
		?>

		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="<?= $hidden_mod->cmid ?> section- <?= $hidden_mod->sectionid ?>
			<?php
						if ($hidden_mod->hide == 1) {
							echo "checked";
						}
					?>
			
			">
							<?php
						$moduleTools->get_ressource_by_id($hidden_mod->cmid);
						echo $moduleTools->name ?>
						</label>
					</div>
				</div>
				<?php
						} ?>
			</section>
			<?php
						}
						$link_annuler = $CFG->wwwroot . "/course/format/iena/suivi_unit.php?courseid=" . $COURSE->id . "&sectionid=" . $_GET['sectionid'];
					?>
	<section>
		<a id="button" href='<?= $link_annuler?>' class="btn btn_reset big_button" style="font-weight:bold">
					<i><?= get_string('cancel', 'format_iena')?> </i></a>
					<button id="button" class="btn btn_blue big_button" style="font-weight:bold;" type="submit"><?= get_string('save', 'format_iena')?> "</i>
					</button>
	</section>
</form>