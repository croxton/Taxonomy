<div id="taxonomy-add-node-container" class="expanded">
	<div class="inset">

<?php
		echo form_open($_form_base.AMP.'method=add_root');
		
		$this->table->set_template($taxonomy_table_template);
		$this->table->set_heading(
				array('data' => lang('insert_a_root'), 'class' => 'options', 'style' => 'width: 100px;'),
				array('data' => "")
			);

	
		// select node name, and parent node
		$this->table->add_row(
			lang('title'),
			form_hidden('tree_id', $tree_id).
			form_input('label', set_value('', ''), 'id="label", style="width: 60%;"')
		);
		
		// add properties

		$this->table->add_row(
			lang('internal_url'),
			form_dropdown('template_path', $templates, '').
			" &nbsp; ".
			form_dropdown('entry_id', $entries, '')
		);
		
		
		$this->table->add_row(
			lang('override_url'),
			form_input('custom_url', set_value('', ''), 'id="custom_url", style="width: 60%;"').$select_page_uri
		);
		$this->table->add_row(
			'',
			form_submit(array('name' => 'submit', 'value' => lang('add'), 'class' => 'submit'))
		);
		
		echo $this->table->generate();
		
		$this->table->clear(); // reset the table
		
		print form_close();
				
?>
	</div>
</div>