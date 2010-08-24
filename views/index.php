
<p>Write whatever</p>
<?php if (count($trees) > 0): ?>
<?=form_open($action_url, '', $form_hidden)?>

<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		lang('tree_id'),
		lang('tree_label'),
		lang('edit_nodes_label'),
		lang('edit_tree_label'),
		form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"'));

	foreach($trees as $tree)
	{
		$this->table->add_row(
				$tree['id'],
				$tree['tree_label'],
				'<a href="'.$tree['edit_nodes_link'].'">'.lang('edit').'</a>',
				'<a href="'.$tree['edit_link'].'">'.lang('properties').'</a>',
				form_checkbox($tree['toggle'])
			);
	}

	echo $this->table->generate();

?>

<div class="tableFooter">
	<div class="tableSubmit">
		<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')).NBS.NBS.form_dropdown('action', $options)?>
	</div>
</div>	

<?=form_close()?>

<?php else: ?>
<?=lang('no_trees_exist')?>
<?php endif; ?>

