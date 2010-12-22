<?=form_open($_form_base.AMP.'method=update_trees')?>


<?php
	
	
	
	$selected_templates = explode('|', $tree_info['template_preferences']);
	$selected_channels = explode('|', $tree_info['channel_preferences']);
	
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
								array('data' => lang('option'), 'style' => 'width:200px'),
								array('data' => lang('value'), 'style' => '')
							);

	$this->table->add_row(
	form_hidden('id', $tree_info['id']).
	lang('tree_label'),
	form_input('label', set_value('label', $tree_info['label'] ), 'id="tree_label"')								
	);
	
	$this->table->add_row(
	lang('template_preferences'),
	form_multiselect('template_preferences[]', $templates, $selected_templates, 'class="taxonomy-multiselect"')
	);
	
	$this->table->add_row(
	lang('channel_preferences'),
	form_multiselect('channel_preferences[]', $channels, $selected_channels, 'class="taxonomy-multiselect"')	
	);

	echo $this->table->generate();
	$this->table->clear(); // needed to reset the table
?>

<br />
<div class="taxonomy-advanced-settings">
<h3>Advanced settings: Tree Custom Fields</h3>
	<p>Custom fields are optional, and will appear to publishers when editing nodes via the module interface.<br />
	By selecting "Display on publish?" the field will appear on the Taxonomy Fieldtype too.</p>
	


<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
								array('data' => lang('order'), 'style' => 'width:40px'),
								array('data' => lang('custom_field_label'), 'style' => 'width:200px'),
								array('data' => lang('custom_field_short'), 'style' => 'width:200px'),
								array('data' => lang('type'), 'style' => ''),
								array('data' => lang('display_on_publish'), 'style' => '')
							);
							
	$field_options = array('text'  => 'Text Input', 'textarea'  => 'Textarea',  'checkbox'  => 'Checkbox',);					
	
	
	
	
	// move all this crap out of the view
	// add an additional row after save
	
	$i = 1;
	
	if(count($tree_info['extra']) > 0 && is_array($tree_info['extra']))
	{	
		// print_r($tree_info['extra']);
		
		foreach($tree_info['extra'] as $key => $field_row)
		{
	
			$order 	= (isset($field_row['order'])) ? $field_row['order'] : '';
			$label 	= (isset($field_row['label'])) ? $field_row['label'] : '';
			$name 	= (isset($field_row['name'])) ? $field_row['name'] : '';
			$type 	= (isset($field_row['type'])) ? $field_row['type'] : '';
			$show_on_publish = (isset($field_row['show_on_publish'])) ? $field_row['show_on_publish'] : FALSE;
			
			$this->table->add_row(
				form_input('field['.$i.'][order]', $order, 'class="taxonomy-number-input"'),
				array('data' => form_input('field['.$i.'][label]', $label, 'class="taxonomy-field-input"'), 'class' => 'foo'),
				form_input('field['.$i.'][name]', $name, 'class="taxonomy-field-input"'),
				form_dropdown('field['.$i.'][type]', $field_options, $type),
				form_checkbox('field['.$i.'][show_on_publish]', '1', $show_on_publish)
			);
			
			$i++;
			
		}
		
	}
	
	// add our last blank row
	$order = 0;
	$label = '';
	$name = '';
	$type = '';
	$show_on_publish = FALSE;
	
	$this->table->add_row(
				form_input('field['.$i.'][order]', $order, 'class="taxonomy-number-input"'),
				array('data' => form_input('field['.$i.'][label]', $label, 'class="taxonomy-field-input"'), 'class' => 'foo'),
				form_input('field['.$i.'][name]', $name, 'class="taxonomy-field-input"'),
				form_dropdown('field['.$i.'][type]', $field_options, $type),
				form_checkbox('field['.$i.'][show_on_publish]', '1', $show_on_publish)
			);
			
	
	echo $this->table->generate();
	$this->table->clear(); // needed to reset the table
?>


</div>
<br />
<input type="submit" class="submit" value="<?=lang('save_settings')?>" />
<?=form_close()?>
