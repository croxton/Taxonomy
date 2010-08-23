
<?php

// output a breadcrumb to this node:
echo "<p><strong>".lang('path_to_here')."</strong> ";

$reverse_path = array_reverse($path);
$depth = 0;		
foreach($reverse_path as $crumb)
{
	echo $crumb['label'].' &rarr; ';
	$depth++;
}

if($depth == 0){
echo lang('this_is_root')."</p>  <br />";
}
else
{
echo $node['label']."</p>  <br />";
}
		
?>

	<?= $add_node_table ?>

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