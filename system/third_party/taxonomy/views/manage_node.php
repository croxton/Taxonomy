<div id="taxonomy-add-node">
<?php
	// print_r($current_node);
	// fetch the current values if editing a node
	$label	 		= (isset($current_node['label'])) ? htmlspecialchars_decode($current_node['label']) : '';
	$node_id 		= (isset($current_node['node_id'])) ? $current_node['node_id'] : '';
	$entry_id		= (isset($current_node['entry_id'])) ? $current_node['entry_id'] : '';
	$template_path	= (isset($current_node['template_path'])) ? $current_node['template_path'] : '';
	$custom_url		= (isset($current_node['custom_url'])) ? $current_node['custom_url'] : '';
	$select_page_uri_option		= (isset($select_page_uri_option)) ? $select_page_uri_option : '';
	$templates		= (isset($templates) && $templates != array()) ? form_dropdown('template_path', $templates, $template_path) : '';
	$entries		= (isset($entries)) ? $entries : array();
		
	echo form_open($_form_base.AMP.'method=process_manage_node');
	
	echo form_hidden('tree_id', $tree_id);
	
	if(isset($root) == 'none')
	{
		echo form_hidden('is_root', '1');
		
		echo lang('root_node_notice');
		
	}
	
	// we're updating a node, so pass node_id
	if($node_id)
	{
		echo form_hidden('node_id', $node_id);
	}
	
	$this->table->set_template($cp_table_template);
	
	$this->table->set_heading(
			array('data' => "Add a node", 'class' => 'options'),
			array('data' => "")
		);
		
	$this->table->add_row(
		'Node Label:',
		form_input('label', $label, 'id="label", style="width: 60%;"')
	);
	
	if($select_parent_dropdown)
	{
		$this->table->add_row('Select Parent:',$select_parent_dropdown);
	}
	
	
	if($templates == '' && $select_entry_dropdown == '')
	{
		// no templates, no entries... just the url override it is...
	}
	elseif($templates == '' && $select_entry_dropdown != '')
	{
		// we got entries, no templates
		$this->table->add_row(
			lang('internal_url_no_templates'),
			$select_entry_dropdown
		);
	}
	else
	{
		// entries and templates
		$this->table->add_row(
			lang('internal_url'),
			"<div id='taxonomy_select_template'>".$templates."</div>".
			" &nbsp; ".
			$select_entry_dropdown
		);
	}
	
	// custom url
	$this->table->add_row(
		'URL Override:',
		form_input('custom_url', $custom_url, 'id="custom_url",  style="width: 60%; float:left;"').$select_page_uri_option
	);
	
	
	// loop through the fields
	if(isset($custom_fields) && is_array($custom_fields))
	{
		foreach($custom_fields as $custom_field)
		{
			// custom url
			switch($custom_field)
			{
				case($custom_field['type'] == 'text'):
					$this->table->add_row(
						$custom_field['label'],
						form_input('extra['.$custom_field['name'].']', $custom_url, 'id='.$custom_field['name'].',  style="width: 60%; float:left;"')
					);
					break;
				case($custom_field['type'] == 'checkbox'):
					$this->table->add_row(
						'&nbsp;',
						form_checkbox('extra['.$custom_field['name'].']', 1, FALSE).' &nbsp; '.$custom_field['label']
					);
					break;
			}
			
		}
	}
	//echo "<pre>"; print_r($custom_fields); echo "</pre>";
	
	// submit
	$this->table->add_row(
		'&nbsp;',
		'<input type="submit" name="submit" value="Submit" class="submit"  /> &nbsp; &nbsp; <a href="'.$_base.'&method=edit_nodes&tree_id='.$tree_id.'" class="taxononomy-cancel">Cancel</a>'
	);
	
	
	echo $this->table->generate();
	$this->table->clear();
	
	print form_close();
?>
	
</div>