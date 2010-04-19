<div class="pageContents">

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=update_trees')?>

		<?php
		
			// without the div above, the slide effect breaks the table widths
			$this->table->set_template($cp_table_template);
			$this->table->set_heading(
										array('data' => lang('id'), 'style' => 'width:5%'),
										array('data' => lang('tree_label'), 'style' => 'width:35%'),
										array('data' => lang('template_preferences'), 'style' => 'width:30%'),
										array('data' => lang('channel_preferences'), 'style' => 'width:30%')
									);

			// no results?  Give the "no files" message
			if (count($tree_info) == 0)
			{
				$this->table->add_row(array('data' => lang('no_trees'), 'colspan' => 3, 'class' => 'no_trees_warning'));
			}
			else
			{
				// Create a row for each file
				foreach ($tree_info as $tree)
				{
				
				$selected_weblogs = explode('|', $tree['template_preferences']);
				$selected_channels = explode('|', $tree['channel_preferences']);	
			
				$this->table->add_row(
				$tree['id'],
				form_hidden('id['.$tree['id'].']', $tree['id']).
				form_hidden('site_id['.$tree['id'].']', set_value('site_id', $site_id), '').
				form_input('label['.$tree['id'].']', set_value('label', $tree['label']), 'id="tree_label"'),
				form_multiselect('template_preferences['.$tree['id'].'][]', $template_preferences, set_value('template_preferences['.$tree['id'].']', $selected_weblogs)),
				form_multiselect('channel_preferences['.$tree['id'].'][]', $channel_preferences, set_value('channel_preferences['.$tree['id'].']', $selected_channels))									
													
				);
				
				// echo $tree['template_preferences'];
				// print_r ($selected_weblogs);
				
				}
			}
			echo $this->table->generate();
			$this->table->clear(); // needed to reset the table
		?>
		</div>

<input type="submit" class="submit" value="<?=lang('submit')?>" />
<?=form_close()?>


</div>
</div><!-- contents -->