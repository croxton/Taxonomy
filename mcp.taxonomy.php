<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Taxonomy Module for ExpressionEngine 2
 *
 * @package		ExpressionEngine
 * @subpackage	Taxonomy
 * @category	Modules
 * @author    	Iain Urquhart <shout@iain.co.nz>
 * @copyright 	Copyright (c) 2010 Iain Urquhart
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
*/


class Taxonomy_mcp {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Taxonomy_mcp()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

	}

	// --------------------------------------------------------------------

	/**
	 * Main Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('taxonomy_module_name'));
		
			$this->EE->cp->set_right_nav(array(
				'add_nodetree'		=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=add_tree'
			));

		// set some vars
		$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_trees';
		$vars['form_hidden'] = NULL;
		$vars['trees'] = array();
		$vars['options'] = array(
				'edit'  => lang('edit_selected'),
				'delete'    => lang('delete_selected')
				);
		
		$this->EE->javascript->output(array(
				'$(".toggle_all").toggle(
					function(){
						$("input.toggle").each(function() {
							this.checked = true;
						});
					}, function (){
						var checked_status = this.checked;
						$("input.toggle").each(function() {
							this.checked = false;
						});
					}
				);'
			)
		);
			
		$this->EE->javascript->compile();
		
		// grab the trees
		// $query = $this->EE->db->get('exp_taxonomy_trees');	
		$query = $this->EE->db->getwhere('exp_taxonomy_trees',array('site_id' => $this->EE->config->item('site_id')));
		
		//$this->EE->config->item('site_id')

		foreach($query->result_array() as $row)
		{
			// assign vars per result
			$vars['trees'][$row['id']]['id'] = $row['id'];
			$vars['trees'][$row['id']]['site_id'] = $row['site_id'];
			$vars['trees'][$row['id']]['tree_label'] = $row['label'];
			$vars['trees'][$row['id']]['edit_link'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_trees'.AMP.'id='.$row['id'];
			$vars['trees'][$row['id']]['edit_nodes_link'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$row['id'];
			
			$template = '';
			$template_preferences = explode('|', $row['template_preferences']);

			foreach ($template_preferences as $group_id)
			{
				$template .= (isset($template_preferences[$group_id])) ? $template_preferences[$group_id] : $group_id;
				$template .= ', ';
			}

			$vars['trees'][$row['id']]['template_preferences'] = rtrim($template, ', ');

			// Toggle checkbox
			$vars['trees'][$row['id']]['toggle'] = array(
															'name'		=> 'toggle[]',
															'id'		=> 'edit_box_'.$row['id'],
															'value'		=> $row['id'],
															'class'		=>'toggle'
															);
															
		}

		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	
	/**
	 * Create a node tree form
	 *
	 * @access	public
	 */
	function add_tree()
	{
	
		$this->EE->load->helper(array('form', 'string', 'url'));
		$this->EE->load->library('table');
		$this->EE->load->model('tools_model');
		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy',$this->EE->lang->line('taxonomy_module_name'));
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_tree'));
		
		// $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=add_tree';		
		
		$this->EE->javascript->compile();
		
		$vars['site_id'] = $this->EE->config->item('site_id');
		
		// get the templates available
		$this->EE->load->model('template_model');
        $templates = $this->EE->template_model->get_templates($this->EE->config->item('site_id'));
        
        // no templates?	
		if ($templates->num_rows() == 0)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_templates_exist'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
                
		foreach($templates->result_array() as $template)
		{
			$vars['templates']['options'][$template['template_id']] = '/'.$template['group_name'].'/'.$template['template_name'].'/';
		}
		
		// get the channels available
		$this->EE->load->model('channel_model');
		$channels = $this->EE->channel_model->get_channels($this->EE->config->item('site_id'));
		
		// no channels?	
		if ($channels->num_rows() == 0)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_channels_exist'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		foreach($channels->result_array() as $channel)
		{
			$vars['channels']['options'][$channel['channel_id']] = $channel['channel_title'];
		}
		
		return $this->EE->load->view('add_tree', $vars, TRUE);
	
	}
	
	
	/**
	 * Enter/update node tree data to exp_taxonomy_trees, if new - create new tree table to hold nested set.
	 *
	 * @access	public
	 */
	
	function update_trees()
	{

		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}

		$new = TRUE;

		$tree_names = $_POST['id'];
		$template_preferences = '';
		$channel_preferences = '';

		if ($tree_names == '')
		{
			// nothing for you here
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('choose_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=add_tree');
		}
		
		foreach($tree_names as $id => $tree)
		{
			if (isset($_POST['id'][$id]) && $_POST['id'][$id] != '')
			{
				$data['id'] = $_POST['id'][$id]; 
				$new = FALSE;
			}
			
			if (isset($_POST['template_preferences'][$id]))
			{
				if($new == TRUE)
				{
					$tp = $_POST['template_preferences'];
					foreach($tp as $preference)
					{
						$template_preferences .= implode('|', $preference).'|';
					}
				}
				else
				{
					$template_preferences = implode('|', $_POST['template_preferences'][$id]);
				}
				
			}
			else
			{
				$template_preferences = '1';
			}
			
			if (isset($_POST['channel_preferences'][$id]))
			{
				if($new == TRUE)
				{
					$cp = $_POST['channel_preferences'];
					foreach($cp as $preference)
					{
						$channel_preferences .= implode('|', $preference).'|';
					}
				}
				else
				{
				$channel_preferences = implode('|', $_POST['channel_preferences'][$id]);
				}
			}
			else
			{
				$channel_preferences = '1';
			}

						
			$data = array(
							'id'					=> $_POST['id'][$id],
							'site_id'				=> $_POST['site_id'][$id],
							'label'					=> $_POST['label'][$id],
							'template_preferences'	=> $template_preferences,
							'channel_preferences' 	=> $channel_preferences
							);
			
			
							
			// print_r($data);				
	
			/** ---------------------------------
			/**  Do our insert or update
			/** ---------------------------------*/
							
			if ($new)
			{
				
				$this->EE->db->query($this->EE->db->insert_string('exp_taxonomy_trees', $data));
				
				// unsure of how reliable this method is
				$last_tree_id = mysql_insert_id();
				
				// builds the taxonomy_tree_x table
				$fields = array(
						'node_id'			=> array('type' 		 => 'mediumint',
													'constraint'	 => '8',
													'unsigned'		 => TRUE,
													'auto_increment' => TRUE,
													'null' => FALSE),
																			
						'lft'				=> array('type'			=> 'mediumint',
													'constraint'	=> '8',
													'unsigned'	=>	TRUE),
													
						'rgt'				=> array('type'			=> 'mediumint',
													'constraint'	=> '8',
													'unsigned'	=>	TRUE),
													
						'moved'				=> array('type'			=> 'tinyint',
													'constraint'	=> '1',
													'null' => FALSE),
																				
						'label'				=> array('type' => 'varchar', 
													'constraint' => '255'),
													
						'entry_id'			=> array('type'			=> 'int',
													'constraint'	=> '10', 
													'null' => TRUE),
						'template_path'		=> array('type' => 'varchar', 
													'constraint' => '255'),							
													
						'custom_url'		=> array('type' => 'varchar', 
													'constraint' => '250', 
													'null' => TRUE),
						'extra'				=> array('type' => 'varchar', 
													'constraint' => '255')	
													
						);
					
						$this->EE->load->dbforge();
						$this->EE->dbforge->add_field($fields);
						$this->EE->dbforge->add_key('node_id', TRUE);
				
						$this->EE->dbforge->create_table('taxonomy_tree_'.$last_tree_id);
						
						unset($fields);
				
				$cp_message = $this->EE->lang->line('tree_added');
			}
			else
			{
				$this->EE->db->query($this->EE->db->update_string('exp_taxonomy_trees', $data, "id = '$id'"));
				$cp_message = $this->EE->lang->line('properties_updated');
			}
		}
		
		$this->EE->session->set_flashdata('message_success', $cp_message);
		
		//printf("Last inserted record has id %d\n", mysql_insert_id());
		
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=index');
	}
	
	
	function edit_trees()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}

		$this->EE->load->helper(array('form'));
		$this->EE->load->library('table');
		
		$vars['site_id'] = $this->EE->config->item('site_id');
		
		if ($this->EE->input->get_post('toggle'))
		{
			$trees = $this->EE->input->get_post('toggle');
		}
		else
		{
			$trees = $this->EE->input->get_post('id');
		}

		if ($trees === FALSE)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP
				.'M=show_module_cp'.AMP.'module=taxonomy');	
		}

		if ( ! is_array($trees))
		{
			$trees = array($trees);
		}
					
		$this->EE->db->where_in('id', $trees);
		$query = $this->EE->db->get('taxonomy_trees');
			
		if ($query->num_rows() == 0)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('invalid_trees'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');				
		}

		if ($this->EE->input->post('action') == 'delete')
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_trees'));
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy', $this->EE->lang->line('taxonomy_module_name'));


			foreach ($_POST['toggle'] as $key => $val)
			{
				$vars['deleted'][] = $val;
			}
			
			$vars['form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=delete_trees';

			return $this->EE->load->view('delete_confirm', $vars, TRUE);
			
		}
		else
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_trees'));
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy', $this->EE->lang->line('taxonomy_module_name'));

   			foreach ($query->result() as $row)
			{
				$vars['tree_info'][$row->id]['id'] = $row->id;
				$vars['tree_info'][$row->id]['site_id'] = $row->site_id;
				$vars['tree_info'][$row->id]['label'] = $row->label;
				$vars['tree_info'][$row->id]['template_preferences'] = $row->template_preferences;
				$vars['tree_info'][$row->id]['channel_preferences'] = $row->channel_preferences;
			}						

		}
		
		// get all templates
		$this->EE->load->model('template_model');
		$tquery = $this->EE->template_model->get_templates($this->EE->config->item('site_id'));
		
		$templates = array();
		
		foreach($tquery->result_array() as $template)
		{
			// hide /index label from groups
			if($template['template_name'] =='index')
			{
				$vars['template_preferences']['options'][$template['template_id']] = '/'.$template['group_name'].'/';
			}
			else
			{
				$vars['template_preferences']['options'][$template['template_id']] = '/'.$template['group_name'].'/'.$template['template_name'].'/';
			}
		}
		
		
		// get channels
		$this->EE->load->model('channel_model');
		$channels = $this->EE->channel_model->get_channels($this->EE->config->item('site_id'));
		foreach($channels->result_array() as $channel)
		{
			$vars['channel_preferences']['options'][$channel['channel_id']] = $channel['channel_title'];
		}

		return $this->EE->load->view('edit_trees', $vars, TRUE);
	}
	
	
	function delete_trees()
	{
		if ( ! $this->EE->input->post('delete'))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_trees'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}

		foreach ($_POST['delete'] as $key => $val)
		{
			$this->EE->db->or_where('id', $val);
		}

		$this->EE->db->delete('exp_taxonomy_trees');
		
		// drop the table containing this tree's nodes too
		foreach ($_POST['delete'] as $key => $val)
		{
			$this->EE->load->dbforge();
			$this->EE->dbforge->drop_table('taxonomy_tree_'.$val);
		}
	
		$message = (count($_POST['delete']) == 1) ? $this->EE->lang->line('nodes_deleted') : $this->EE->lang->line('nodes_deleted');

		$this->EE->session->set_flashdata('message_success', $message);
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		
	}
	
	function edit_nodes()
	{
	
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		// check the tree is being passed
		if ( ! $this->EE->input->get('tree'))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}

		$tree = $this->EE->input->get('tree');

		// check the tree table exists
		if (!$this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
						
		// get our poop together
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
		
		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy',$this->EE->lang->line('taxonomy_module_name'));

		$vars = array();
		
		$vars['site_url'] = $this->EE->functions->fetch_site_index();
		
		// Duplicate code starts here from edit_node()
		// fetch the user template and channel preferences for this tree
		$this->EE->db->where_in('id', $tree);
		$query = $this->EE->db->get('taxonomy_trees');
		
		// no results?	
		if ($query->num_rows() == 0)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_templates_assigned'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		// grab the preference values
		foreach ($query->result() as $row)
		{
			$usertemplates 	=  $row->template_preferences;
			$userchannels	=  $row->channel_preferences;
			$tree_label 	=  $row->label;
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
		$vars['templates']['options'][0] = '--';
		
		// remove /index label from each template group
		foreach($tquery->result_array() as $template)
		{
			if($template['template_name'] =='index')
			{
				$vars['templates']['options'][$template['template_id']] = '/'.$template['group_name'].'/';
			}
			else
			{
				$vars['templates']['options'][$template['template_id']] = '/'.$template['group_name'].'/'.$template['template_name'].'/';
			}
		}
				
		//grab the channel entries
		
		if($userchannels == 0)
		{
			$userchannels = array();
		}
		else
		{
			$userchannels = explode('|',$userchannels);
		}
		
		$this->EE->load->model('channel_entries_model');
		
		$fields_needed = array(
								"entry_id", "channel_id", "title"
								);

		$this->EE->load->model('channel_model');

		$channels = $this->EE->channel_model->get_channels($this->EE->config->item('site_id'));

		$channels_needed = array();
		foreach($channels->result_array() as $channel)
		{
			$channels_needed[$channel['channel_id']] = $channel['channel_title'];
		}

		// print_r($channels_needed);

		$entries = $this->EE->channel_entries_model->get_entries($userchannels, $fields_needed);
		
		// give a null value option for entries select
		$vars['entries'][0] = '--';
		foreach($entries->result_array() as $entry)
		{
			$vars['entries'][$entry['entry_id']] = '['.$channels_needed[$entry['channel_id']].'] &rarr; '.$entry['title'];
		}
		
		// sort alphabetically
		natcasesort($vars['entries']);
		
		// Duplicate code ENDS here
		
		$root_array = $this->EE->mpttree->get_root();
		$root_node = $root_array['node_id'];
		
		$vars['tree'] = $tree;
		$vars['root'] = $root_array;
			
		$vars['flat_tree'] = $this->EE->mpttree->get_flat_tree_v2(1);
		
		$vars['asset_path'] = 'expressionengine/third_party/taxonomy/views/';
		$vars['url_prefix'] = $this->EE->functions->fetch_site_index();
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_nodes').': '.$tree_label);

		// print_r($vars['root']);
		
		if($root_array === false)
		{
			// root doesn't exist, so stop the user here and have them enter one.
			$vars['root'] = 'none';
			$vars['add_root_form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=add_root';
			return $this->EE->load->view('add_root_node', $vars, TRUE);
		}
		else
		{
			return $this->EE->load->view('edit_nodes', $vars, TRUE);
		}
	}
	
	
	function add_root()
	{
		
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		// check the tree is being passed
		if ( ! $this->EE->input->post('tree'))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}

		$tree = $this->EE->input->post('tree');
	
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
		
		$data = array(
						'node_id'			=> '',
						'label'				=> isset($_POST['label']) ? htmlentities($_POST['label']) : '',
						'entry_id'			=> isset($_POST['entry_id']) ? $_POST['entry_id'] : '',
						'template_path'		=> isset($_POST['template_path']) ? $_POST['template_path'] : '',
						'custom_url'		=> isset($_POST['custom_url']) ? $_POST['custom_url'] : '',
						'extra'				=> isset($_POST['extra']) ? $_POST['extra'] : ''
						);
						
		$data = $this->EE->security->xss_clean($data);				
		
		$this->EE->mpttree->insert_root($data);
		
		$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('root_added'));
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree);
	
	}
	

	function add_node()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
	
		$tree = $this->EE->input->post('tree');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		$parent_node_lft = $this->EE->input->post('parent_node_lft');
	
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
		
		$data = array(
						'node_id'			=> '',
						'label'				=> isset($_POST['label']) ? htmlentities($_POST['label']) : '',
						'entry_id'			=> isset($_POST['entry_id']) ? $_POST['entry_id'] : '',
						'template_path'		=> isset($_POST['template_path']) ? $_POST['template_path'] : '',
						'custom_url'		=> isset($_POST['custom_url']) ? $_POST['custom_url'] : '',
						'extra'				=> isset($_POST['extra']) ? $_POST['extra'] : ''
						);
						
		$data = $this->EE->security->xss_clean($data);				
		
		$this->EE->mpttree->append_node_last($parent_node_lft,$data);
		
		// this messes up the jquery for some reason...
		//$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('node_added'));
		
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree.AMP.time());
			
	
	}
	
	
	
	
	// delete a single node, except the root...
	function delete_node()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		$tree = $this->EE->input->get('tree');
		$id = $this->EE->input->get('node_id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
										
		
		$node = $this->EE->mpttree->get_node_byid($id);
		
		$this->EE->mpttree->delete_node($node['lft']);
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree.AMP.'deleted=true');

	}
	
	// delete an entire branch (combine with above function when not 2am).
	function delete_branch()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		$tree = $this->EE->input->get('tree');
		$id = $this->EE->input->get('node_id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));

		$node = $this->EE->mpttree->get_node_byid($id);
		$this->EE->mpttree->delete_branch($node['lft']);
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree.AMP.'deleted=true');
		
	}
	
	// handles nudging nodes by main edit_nodes interface
	function node_move()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		$tree = $this->EE->input->get('tree');
		$id = $this->EE->input->get('node_id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));				
										
		if (isset ($_GET['direction'])) {
			switch ($_GET['direction']) {		
				case 'left':
					$this->EE->mpttree->move_left($id);
				break;
				case 'right':
					$this->EE->mpttree->move_right($id);
				break;
				case 'up':
					$this->EE->mpttree->move_up($id);
				break;
				case 'down':
					$this->EE->mpttree->move_down($id);
				break;
			}
		}								
										
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree);									
	}
	
	
	
	function edit_node()
	{
		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
	
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$tree = $this->EE->input->get('tree');
		$id = $this->EE->input->get('node_id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
						
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy',$this->EE->lang->line('taxonomy_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_node'));	
		
		$vars = array();
		
		$vars['tree'] = $tree;
		$vars['node'] = $this->EE->mpttree->get_node_byid($id);
		$vars['path'] = $this->EE->mpttree->get_parents($vars['node']['lft'],$vars['node']['rgt']);
		
		
		// Duplicate code starts here from edit_nodes()
		// yes, I'm a noob..
		// fetch the user template and channel preferences for this tree
		$this->EE->db->where_in('id', $tree);
		$query = $this->EE->db->get('taxonomy_trees');
		
		// no results?	
		if ($query->num_rows() == 0)
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('tree_doesnt_exist'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}
		
		// grab the preference values
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
		$vars['templates']['options'][0] = '--';
		
		foreach($tquery->result_array() as $template)
		{
			if($template['template_name'] =='index')
			{
				$vars['templates']['options'][$template['template_id']] = '/'.$template['group_name'].'/';
			}
			else
			{
				$vars['templates']['options'][$template['template_id']] = '/'.$template['group_name'].'/'.$template['template_name'].'/';
			}
		}
				
		//grab the channel entries
		
		if($userchannels == 0)
		{
			$userchannels = array();
		}
		else
		{
			$userchannels = explode('|',$userchannels);
		}
		
		$this->EE->load->model('channel_entries_model');
		
		$fields_needed = array(
								"entry_id", "channel_id", "title"
								);

		$this->EE->load->model('channel_model');

		$channels = $this->EE->channel_model->get_channels($this->EE->config->item('site_id'));

		$channels_needed = array();
		foreach($channels->result_array() as $channel)
		{
			$channels_needed[$channel['channel_id']] = $channel['channel_title'];
		}

		// print_r($channels_needed);

		$entries = $this->EE->channel_entries_model->get_entries($userchannels, $fields_needed);
		
		// give a null value option for entries select
		$vars['entries'][0] = '--';
		foreach($entries->result_array() as $entry)
		{
			$vars['entries'][$entry['entry_id']] = '['.$channels_needed[$entry['channel_id']].'] &rarr; '.$entry['title'];
		}

		natcasesort($vars['entries']);
		// Duplicate code ENDS here
		
		$vars['asset_path'] = 'expressionengine/third_party/taxonomy/views/';

		return $this->EE->load->view('edit_node', $vars, TRUE);							

	}
	
	
	
	function update_node()
	{

		if (! $this->EE->cp->allowed_group('can_access_content'))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		$tree = $this->EE->input->post('tree');
		$id = $this->EE->input->post('id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('no_such_tree'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy');
		}

		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
		
		$data = array(
						'node_id'			=> $id,
						'label'				=> isset($_POST['label']) ? htmlentities($_POST['label']) : '',
						'entry_id'			=> isset($_POST['entry_id']) ? $_POST['entry_id'] : '',
						'template_path'		=> isset($_POST['template_path']) ? $_POST['template_path'] : '',
						'custom_url'		=> isset($_POST['custom_url']) ? $_POST['custom_url'] : '',
						'extra'				=> isset($_POST['extra']) ? $_POST['extra'] : ''
						);
						
		$this->EE->db->query($this->EE->db->update_string('exp_taxonomy_tree_'.$tree, $data, "node_id = '$id'"));
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=taxonomy'.AMP.'method=edit_nodes'.AMP.'tree='.$tree);
		
	}
	
	// just a place to play with stuff.
	function testbed(){
	
	$vars = array();
	
	$tree = 1;
	
	$this->EE->load->library('MPTtree');
	$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
									'left' => 'lft',
									'right' => 'rgt',
									'id' => 'node_id',
									'title' => 'label'));
									
	$vars['test_flat_tree'] = $this->EE->mpttree->get_flat_tree_v2();
	$vars['flat_tree'] = $this->EE->mpttree->get_flat_tree();								
	
	return $this->EE->load->view('xx_testbed', $vars, TRUE);	
	}
	

}
// END CLASS

/* End of file mcp.taxonomy.php */
/* Location: ./system/expressionengine/third_party/modules/taxonomy/mcp.taxonomy.php */