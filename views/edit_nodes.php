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
<script type="text/javascript">
$(document).ready(function() {
	$("#edit_nodes a.delete_node").livequery('click', function() { 
   			var url = $(this).attr("href")+" #edit_nodes";
   			var answer = confirm("Are you sure you want to delete this node?")
		    if (answer){
		        $("#edit_nodes").load(url);
		    }	
			return false;
	});	
	
	$("#edit_nodes a.delete_nodes").livequery('click', function() { 
   			var url = $(this).attr("href")+" #edit_nodes";
   			var answer = confirm("Are you sure you want to delete this node and all it's children?")
		    if (answer){
		        $("#edit_nodes").load(url);
		    }	
			return false;
	});	
});	
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
				array('data' => "&nbsp;<img src='expressionengine/third_party/taxonomy/views/gfx/add_node.png' style='margin-right: 5px; vertical-align: bottom;' />&nbsp;".lang('create_node'), 'class' => 'create_node', 'style' => 'width:30%'),
				array('data' => "", 'class' => 'create_node', 'style' => 'width:70%')
		);
	
		$select_tree_options = "<select name='parent_node_lft'>";
		
		foreach ($flat_tree as $value)
		{
			$select_tree_options .= "<option value='".$value['lft']."'>".str_repeat ('-&nbsp;', $value['level']) . $value['label']."</option>";
		}
		
		$select_tree_options .= "</select>";
		
		// select node name, 
		$this->table->add_row(
			lang('node_label'),
			form_hidden('tree', $tree, '').
			// form_hidden('extra', '', '').
			form_input('label', set_value('', ''), 'id="label", style="width: 60%;"')
		);
		
		$this->table->add_row(
			lang('parent_node'),
			$select_tree_options
		);
		
		// add properties
		
		$this->table->add_row(
			lang('internal_url'),
			''.form_dropdown('template_path', $templates['options'], '').
			" &nbsp; <div id='select_entry' style='display: inline;'>".
			form_dropdown('entry_id', $entries, '').
			"</div> <a href='#node_search' id='search_for_nodes' title='Search for nodes'><img src='".$asset_path."gfx/search.png' alt='Search' /></a>"
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
		
<?php		
		

				
		echo "<div id='edit_table_inner'>";
		
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(
									array('data' => lang(''), 'style' => 'width: 40px;'),
									array('data' => lang(''), 'style' => 'width: 30px;'),
									array('data' => lang('name'), 'style' => ''),
									array('data' => lang('properties'), 'style' => ''),
									array('data' => lang('Delete'), 'style' => 'width:20px')
								);
	
		//check we have the tree id
		if ((isset ($tree) && is_numeric($tree)))
		{
		
			$treeCount = count ($flat_tree);
				
							
			for ($i = 0; $i < $treeCount; $i++)
			{	
				$root_spcr = '<img src="'.PATH_CP_GBL_IMG.'clear.gif" border="0"  width="12" height="14" alt="" title="" />';
				$spcr = '<img src="'.PATH_CP_GBL_IMG.'clear.gif" border="0"  width="24" height="14" alt="" title="" />';
				$indent = $spcr.'<img src="'.PATH_CP_GBL_IMG.'cat_marker.gif" border="0"  width="18" height="14" alt="" title="" /> ';
				
				// establish indentation
				if ( $flat_tree[$i]['level'] == 0 ) 
				{
					$spacer = $root_spcr;
				}
				else 
				{
					$spacer = str_repeat($spcr, $flat_tree[$i]['level']-1);
					$spacer .= $indent; 
				}
				
				// get the mess um.. messsy?
				$node_label = $flat_tree[$i]['label'];
				$node_id 	= $flat_tree[$i]['node_id'];
				$custom_url = $flat_tree[$i]['custom_url'];
				$template_path = $flat_tree[$i]['template_path'];
				$level = $flat_tree[$i]['level'];
				
				$entry_id 	= $flat_tree[$i]['entry_id'];
				if ($entry_id == 0)
				{
					$entry_id = '';
				}
				
				$node_link_base = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy';
				
				// define buttons for manipulating node heirarchy must cleanup.... @todo
				$move_left = "<a href='".$node_link_base.AMP."method=node_move".AMP."direction=up".AMP."node_id=".$node_id.AMP."tree=".$tree."' class='fancypants'><img src='".$asset_path."gfx/arw_left.png' /></a>";
				
				$move_right = "<a href='".$node_link_base.AMP."method=node_move".AMP."direction=down".AMP."node_id=".$node_id.AMP."tree=".$tree."' class='fancypants'><img src='".$asset_path."gfx/arw_right.png' /></a>";
				
				
				$move_up = "<a href='".$node_link_base.AMP."method=node_move".AMP."direction=left".AMP."node_id=".$node_id.AMP."tree=".$tree."' class='fancypants'><img src='".$asset_path."gfx/arw_up.png' style='vertical-align: bottom; margin-left: -5px;' /></a>";
				
				$move_down = "<a href='".$node_link_base.AMP."method=node_move".AMP."direction=right".AMP."node_id=".$node_id.AMP."tree=".$tree."' class='fancypants'><img src='".$asset_path."gfx/arw_down.png' style='vertical-align: bottom; margin-right: -5px;' /></a> ";

				
	
				// does the node have children, if so change the icons.
				if ($flat_tree[$i]['childs'] == 1)
				{
					$node_icon 	= "<img src='".$asset_path."gfx/page.png'  style='margin-right: 5px; vertical-align: bottom;' />";
					$trash_icon = "<a href='".$node_link_base.AMP."method=delete_node".AMP."node_id=".$node_id.AMP."tree=".$tree."'   class='delete_node'>
					<img src='".$asset_path."gfx/trash.png' style='margin-right: 5px; vertical-align: bottom;' /></a>";
				}
				else
				{
					$node_icon = "<img src='".$asset_path."gfx/folder.png' style='margin-right: 5px; vertical-align: bottom;' />";
					$trash_icon = "<a href='".$node_link_base.AMP."method=delete_branch".AMP."node_id=".$node_id.AMP."tree=".$tree.AMP."del_childs=yes' class='delete_nodes'>
					<img src='".$asset_path."gfx/trash-children.png' style='margin-right: 5px; vertical-align: bottom;' /></a>";
				}
				
				//onClick='if(confirm(\"".lang('branch_delete_question')."\")) return true; else return false'
				
				// root node can't have operations...
				if ($flat_tree[$i]['lft'] == 1)
				{
				
					$move_left = '';
					$move_right = '';
					$move_up = '';
					$move_down = '';
					$trash_icon = '';
				}
				
				$mask = '';
				
				$template = $flat_tree[$i]['template_path'];
				$selected_template_path = $templates['options'][$template];
				$custom_url = $flat_tree[$i]['custom_url'];
				
				
				if($custom_url)
				{
					$selected_template_path = '';
					$flat_tree[$i]['url_title'] = '';
					$node_icon = "<img src='".$asset_path."gfx/link.png' style='margin-right: 5px; vertical-align: bottom;' />";
					$mask = '?URL=';
				}
				
				$properties = $custom_url.$selected_template_path.$flat_tree[$i]['url_title'];
				
				$truncated_properties = substr($properties,0,30);
				
				if(strlen($properties) > 30)
				{
					$truncated_properties .= "&hellip;";
				}
				
				
				// build the table row!	
				$this->table->add_row(
							$move_left.$move_right,
							$move_up.$move_down,
							$spacer.$node_icon."<a href='".$node_link_base.AMP.'method=edit_node'.AMP.'node_id='.$node_id.AMP.'tree='.$tree."'>".$node_label."",
							"<span class='node_properties'><a href='".$url_prefix.$mask.$properties."' title='".lang('visit').$properties."'>".$truncated_properties."</a></span>",
							$trash_icon
						);	
			
				
			 
			}
		}
		
		// tree_id failed
		else
		{
			$this->table->add_row(
						"The requested Tree does not exist!",
						"1",
						"1",
						"1",
						"1",
						"1",
						"1"
					);
		}
		
				
		
		echo $this->table->generate();
		$this->table->clear(); // reset the table
		echo "</div>";
		echo "</div>";
?>