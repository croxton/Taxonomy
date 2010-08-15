
<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=configure')?>

<?php

	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
								array('data' => lang('setting'), 'style' => 'width:30%'),
								array('data' => lang('preference'), 'style' => 'width:70%')
							);

	$this->table->add_row(
	form_hidden('site_id', set_value('site_id', $site_id), '').
	lang('asset_path_config').
	form_hidden('id', ''),
	form_input('asset_path', set_value('asset_path', $preferences['asset_path']))									
	);

	echo $this->table->generate();
	$this->table->clear(); // needed to reset the table
?>

<input type="submit" class="submit" value="<?=lang('update')?>" />
<?=form_close()?>





