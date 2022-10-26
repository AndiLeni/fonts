<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon"><i class="fa fa-search"></i></span>
		<input placeholder="Schrift suchen ..." class="form-control input-lg" id="input_filter" type="text"
			onkeyup="filter()">
		<span class="input-group-btn">
			<button id="btn_clear" class="btn btn-default btn-lg" type="button">&times;</button>
		</span>
	</div>
</div>

<form id="form_fonts" method="POST">
	<table id="fonts_table" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th><?= rex_i18n("fonts_list_name") ?>
				</th>
				<th><?= rex_i18n("fonts_list_select") ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->getVar('table_rows') ?>
		</tbody>
	</table>
</form>


<script>
	function filter() {
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("input_filter");
		filter = input.value.toUpperCase();
		table = document.getElementById("fonts_table");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	}

	document.getElementById("btn_clear").addEventListener("click", function() {
		document.getElementById("input_filter").value = "";
		filter();
	})
</script>