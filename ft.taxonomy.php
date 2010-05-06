<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2010 Iain Urquhart - shout@iain.co.nz

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
 */

	// @todo - language

	class Taxonomy_ft extends EE_Fieldtype
	{
		var $info = array(
			'name'		=> 'Taxonomy',
			'version'	=> '0.1'
		);

		public function Taxonomy_ft()
		{
			parent::EE_Fieldtype();
			require PATH_THIRD.'taxonomy/libraries/mpttree.php';

		}	

		public function display_field($data)
		{

			$mpttree = new MPTtree;

			// a bit hacky @todo
			$tree = $this->settings['field_list_items'];
			
			// call the tree
			$mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));

			// check the tree table exists
			if (!$this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
			{
				return 'No such tree exists! Please check field configuration';
			}

			$this->EE->db->where_in('id', $tree);
			$query = $this->EE->db->get('taxonomy_trees');

			// grab the Taxonomy preference values
			foreach ($query->result() as $row)
			{
				$usertemplates 	=  $row->template_preferences;
				$userchannels	=  $row->channel_preferences;
			}	

			if($usertemplates == 0)
			{
				$usertemplates = array();
			}
			else
			{
				$usertemplates = array("template_id" => explode('|',$usertemplates));
			}
	
			// Get Templates
	        $this->EE->load->model('template_model');
	        $tquery = $this->EE->template_model->get_templates($this->EE->config->item('site_id'), array(), $usertemplates);

	        $templates = array();

	        // give a null value for template pulldown
			$templates['options'][0] = '--';

			// remove /index label from each template group
			foreach($tquery->result_array() as $template)
			{
				if($template['template_name'] =='index')
				{
					$templates['options'][$template['template_id']] = '/'.$template['group_name'].'/';
				}
				else
				{
					$templates['options'][$template['template_id']] = '/'.$template['group_name'].'/'.$template['template_name'].'/';
				}
			}

			$text_direction = ($this->settings['field_text_direction'] == 'rtl') ? 'rtl' : 'ltr';

			// template dropdown
			$template = form_dropdown($this->field_name.'[template]', $templates['options'], $data, 'dir="'.$text_direction.'" id="'.$this->field_id.'"' );

			// node label
			$label = form_input(array(
									'name'	=> $this->field_name.'[label]',
									'id'	=> $this->field_id,
									'value'	=> $data
								));

			// fetch the nodes
			$taxonomy_nodes = $mpttree->get_flat_tree_v2();

			// are there nodes in this tree?
			if( ! $taxonomy_nodes)
			{
				// @todo
				return '<p>This tree has no nodes, please add at least a root node first!</p>';
			}
			
			// build the select parent pulldown
			$parent_node_options = "<select name='".$this->field_name."[parent_node_lft]'>";
			$parent_node_options .= "<option value=''>--</option>";

			foreach ($taxonomy_nodes as $node)
			{
				$parent_node_options .= "<option value='".$node['lft']."'>".str_repeat ('-&nbsp;', $node['level']) . $node['label']."</option>";
			}
			
			$parent_node_options .= "</select>";

			// we'll presume this is a new node for now.
			$submission_type = 'new';

			//output the field table
			$return = '';

			// are we editing an entry?
			if($this->EE->input->get('entry_id'))
			{

				$existing_entry = $this->EE->input->get('entry_id');

				// find if it exists in the tree already, and grab its values from taxonomy
				$this->EE->db->where_in('entry_id', $existing_entry);

				// we're making a presumption here that this entry only exists once in the tree (?!)
				$query = $this->EE->db->get('exp_taxonomy_tree_'.$tree, 1);

				// grab the Taxonomy values for this node
				foreach ($query->result() as $row)
				{
					// flag it as an edit for the save process
					$submission_type = 'edit';

					$label = form_input(array(
											'name'	=> $this->field_name.'[label]',
											'id'	=> $this->field_id,
											'value'	=> $row->label
										));

					// rebuilt the select parent entry select menu
					$parent_node_options = "<select name='".$this->field_name."[parent_node_lft]'>";
					$parent_node_options .= "<option value=''>--</option>";

					$parent = $mpttree->get_parent($row->lft,$row->rgt);

					foreach ($taxonomy_nodes as $node)
					{
						$selected = '';
						$disabled = '';
						
						// select the existing option
						if($node['lft'] == $parent['lft'])
						{
							$selected = " selected='selected'";
						}
						
						// disable the node itself so the user can't select itself as a parent
						if($node['lft'] == $row->lft)
						{
							// might have to use jquery here, IE6/7 horror.
							$disabled = " disabled='disabled'";
						}
						
						$parent_node_options .= "<option value='".$node['lft']."'".$selected.$disabled.">".str_repeat ('-&nbsp;', $node['level']) . $node['label']."</option>";
					}

					$parent_node_options .= "</select>";

					// rewrite template select option with selected attribute
					$template = str_replace('value="'.$row->template_path.'">', 'value="'.$row->template_path.'" selected="selected">', $template);

				}

			}

			// add the hidden field that flags if this is new or an edit
			$return .= form_hidden($this->field_name.'[submission_type]', $submission_type, '');
			
			$return .= '
					<table class="mainTable" border="0" cellspacing="0" cellpadding="0">

							<tr>
								<td style="width: 100px;">Menu Label</td>
								<td>'.$label.'</td>
							</tr>
							<tr>
								<td>Parent Node</td>
								<td>'.$parent_node_options.'</td>
							</tr>
							<tr>
								<td>Template</td>
								<td>'.$template.'</td>
							</tr>
					</table>';
			
			return $return;

		}
		
		
		public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
		{

		}
		
		public function save($data)
		{
				//print_r($data);
				$this->cache['data'][$this->settings['field_id']] = $data;
		}
		
		function post_save($data)
		{

			$data = $this->cache['data'][$this->settings['field_id']];
			
			// print_r($data);
			
			if(!$data)
			{
				return NULL;
			}
			
			$mpttree = new MPTtree;
			
			// bit hacky @todo
			$tree = $this->settings['field_list_items'];
			
			// call the tree
			$mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));

			// check tree exists
			if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
			{
				// need to think about this
				return NULL;
			}	

			$parent_node_lft = $data['parent_node_lft'];

			$taxonomy_data = array(
							'node_id'			=> '',
							'label'				=> htmlentities($data['label']),
							'entry_id'			=> $this->settings['entry_id'],
							'template_path'		=> $data['template']
							);

			$taxonomy_data = $this->EE->security->xss_clean($taxonomy_data);

			// are we adding a new node?				
			if($data['submission_type'] =='new' && $data['label'] != '')
			{
				// easy, just insert it
				$mpttree->append_node($parent_node_lft,$taxonomy_data);
			}

			// or are we editing
			if($data['submission_type'] =='edit' && $data['label'] != '')
			{

				// fetch the node
				$node = $mpttree->get_node_by_entryid($this->settings['entry_id']);

				// what is the existing parent value
				$existing_parent = $mpttree->get_parent($node['lft'],$node['rgt']);

				$taxonomy_data['node_id'] = $node['node_id'];

				// update/insert the values
				$mpttree->update_node($node['lft'],$taxonomy_data);

				// check if the submitted parent is different
				if($parent_node_lft != $existing_parent['lft'])
				{
					// if it is, then hold your breath and cross your fingers
					$mpttree->move_node_append_last($node['lft'],$parent_node_lft);
				}

			}

		}
		
		public function validate($data)
		{
			return TRUE;
		}
		
		public function save_settings($data)
		{
			return array();
		}

		public function display_settings($data)
		{
		
			$this->EE->table->add_row(
			$this->EE->lang->line('tree_id_label'),
			form_input(array('field_list_items','name'=>'field_list_items', 'class'=>'fullfield', 'value'=>$data['field_list_items']))
		);
		}

		function install()
		{
			//nothing
		}

		function unsinstall()
		{
			//nothing
		}
	}
	//END CLASS
	
/* End of file ft.taxonomy.php */
