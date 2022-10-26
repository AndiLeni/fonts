<div class="panel panel-default">
	<div class="panel-heading">
		<?= rex_i18n::msg("fonts_list_select_variant"); ?>
		<b><?php echo $this->getVar("selected_font") ?></b>
	</div>
	<div class="panel-body">
		<form id="select_variants_form" method="POST">
			<?php echo $this->getVar("variant_options") ?>
		</form>
	</div>
	<div class="panel-footer">
		<button form="select_variants_form" class="btn btn-primary"
			type="submit"><?= rex_i18n::msg("fonts_install_selected") ?></button>
	</div>
</div>