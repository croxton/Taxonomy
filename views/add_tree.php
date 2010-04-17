<p><?=lang('create_tree_instructions')?></p>
<br />
<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=update_trees')?>

<?php

	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
								array('data' => lang('tree_label'), 'style' => 'width:40%'),
								array('data' => lang('template_preferences'), 'style' => 'width:30%'),
								array('data' => lang('channel_preferences'), 'style' => 'width:30%')
							);

	
		$this->table->add_row(
		form_hidden('id[]', set_value('id', ''), '').
		form_input('label[]', set_value('label', ''), 'id="tree_label"'),
		form_multiselect('template_preferences[][]', $templates, ''),
		form_multiselect('channel_preferences[][]', $channels, '')	
											
		);

	echo $this->table->generate();
	$this->table->clear(); // needed to reset the table
?>

<input type="submit" class="submit" value="<?=lang('create_tree')?>" />
<?=form_close()?>



