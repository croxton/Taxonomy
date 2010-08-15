<script type="text/javascript" src="<?=$asset_path?>js/jquery.livequery.js"></script>
<script type="text/javascript" src="<?=$asset_path?>js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="<?=$asset_path?>js/jquery.autocomplete.min.js"></script>

<script type="text/javascript" src="<?=$asset_path?>js/taxonomy.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$asset_path?>css/taxonomy.css" />


	<!-- Little table for the entry search fancybox -->
	<div style='display: none;'>
		<div id='node_search'>	
			<?php		

				$this->table->set_template($cp_table_template);
				
				$this->table->add_row(
					lang('search'),
					"<input id='autocomplete_entries' type='text' />"
				);
				
				echo $this->table->generate();
				
				$this->table->clear(); // reset the table
			?>		
		</div>
	</div>


	<div id='edit_nodes'>

	<?=$add_node_table?>
		
	<?=$tree_table?>

	</div>