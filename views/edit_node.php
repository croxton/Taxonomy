<script src="<?=$asset_path?>js/jquery.livequery.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?=$asset_path?>js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="<?=$asset_path?>js/jquery.autocomplete.min.js"></script>

<?php
	// build the index for autocomplete
	$entry_list = '';
	foreach($entries as $key => $entry)
	{
		$entry_list .= "{ id:'".$key."', entry:'".$entry."'}, ";
	}
	$entry_list .= "{ id: '', entry: ''} ";

?>		

<script type="text/javascript">
	var entries = [<?=$entry_list?>];
</script>	

<script type="text/javascript" src="<?=$asset_path?>js/taxonomy.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$asset_path?>css/taxonomy.css" />

<? 

// print_r($node);
// print_r($path);
// print_r($templates);

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

// BEGIN ADD NODE TABLE
		print form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=update_node');
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(
				array('data' => lang('option'), 'class' => 'create_node', 'style' => 'width:30%'),
				array('data' => lang('value'), 'class' => 'create_node', 'style' => 'width:70%')
		);
		
		
		// select node name, and parent node
		$this->table->add_row(
			lang('node_label'),
			form_hidden('tree', $tree, '').
			form_hidden('id', $node['node_id'], '').
			// form_hidden('extra', '', '').
			form_input('label', set_value($node['label'], html_entity_decode($node['label'])), 'id="label", style="width: 60%;"')
		);
		
		// add properties
		
		$this->table->add_row(
			lang('internal_url'),
			form_dropdown('template_path', $templates['options'], $node['template_path']).
			" &nbsp; <div id='select_entry' style='display: inline;'>".
			form_dropdown('entry_id', $entries, $node['entry_id']).
			"</div> <a href='#node_search' id='search_for_nodes' title='Search for nodes'><img src='".$asset_path."gfx/search.png' alt='Search' /></a>"
		);
		
		
		$this->table->add_row(
			lang('override_url'),
			form_input('custom_url', set_value($node['custom_url'], $node['custom_url']), 'id="custom_url", style="width: 60%;"')
		);
		$this->table->add_row(
			'',
			form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))
		);
		
		echo $this->table->generate();
		
		$this->table->clear(); // reset the table
		
		print form_close();
		
		// END ADD NODE TABLE
		
		
		

?>


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