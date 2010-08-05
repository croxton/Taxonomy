
<script src="<?=$asset_path?>js/jquery.livequery.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?=$asset_path?>js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="<?=$asset_path?>js/jquery.autocomplete.min.js"></script>

<?php
	
	// this whole view needs rewritten, too much logic going on. (or lack of...)

	// build the index for autocomplete
	$entry_list = '';
	foreach($entries as $key => $entry)
	{
		$entry_list .= "{ id:'".$key."', entry:'".addslashes($entry)."'}, ";
	}
	$entry_list .= "{ id: '', entry: ''} ";

?>		

<script type="text/javascript">
	var entries = [<?=$entry_list?>];
</script>
<script type="text/javascript" src="<?=$asset_path?>js/taxonomy.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$asset_path?>css/taxonomy.css" />

	<div id='edit_nodes'>
		<div id='add_node'>

<?php
				
		// begin add node table
		print form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=add_node');
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(
				array('data' => "<span><img src='expressionengine/third_party/taxonomy/views/gfx/add_node.png' style='margin-right: 5px; vertical-align: bottom;' />&nbsp;".lang('create_node')."</span>", 'class' => 'create_node'),
				array('data' => "")
		);
	
		$select_parent_options = "<select name='parent_node_lft'>\n";
		
		foreach ($flat_tree as $value)
		{
			$select_parent_options .= "<option value='".$value['lft']."'>".str_repeat('-&nbsp;', $value['level']).$value['label']."</option>\n";
		}
		
		$select_parent_options .= "</select>\n";
		
		// select node name, 
		$this->table->add_row(
			lang('node_label'),
			form_hidden('tree', $tree, '').
			// form_hidden('extra', '', '').
			form_input('label', set_value('', ''), 'id="label", style="width: 60%;"')
		);
		
		$this->table->add_row(
			lang('parent_node'),
			$select_parent_options
		);
		
		// add properties
		
		$this->table->add_row(
			lang('internal_url'),
			''.form_dropdown('template_path', $templates['options'], '').
			" &nbsp; <div id='select_entry' style='display: inline;'>".
			form_dropdown('entry_id', $entries, '').
			"</div> <a href='#node_search' id='search_for_nodes' title='".lang('search_for_nodes')."'><img src='".$asset_path."gfx/search.png' alt='".lang('search_for_nodes')."' /></a>"
		);
		
		
		$this->table->add_row(
			lang('override_url'),
			form_input('custom_url', set_value('', ''), 'id="custom_url", style="width: 60%;"')
		);
		$this->table->add_row(
			'',
			form_submit(array('name' => 'submit', 'value' => lang('add'), 'class' => 'submit'))
		);
		
		echo $this->table->generate();
		
		$this->table->clear(); // reset the table
		
		print form_close();
		
?>		

	</div> <!-- /add_node -->
		
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
		
	<?= $tree_table ?>


</div>
</div>