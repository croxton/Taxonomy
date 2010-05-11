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


class Taxonomy {

	function Taxonomy()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	

	// {exp:taxonomy:breadcrumbs tree_id="1" entry_id="{entry_id}"}
	function breadcrumbs()
	{
	
		$tree = $this->EE->TMPL->fetch_param('tree_id');
				
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			return false;
		}
		
		$display_root = $this->EE->TMPL->fetch_param('display_root');
		
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');

		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
										'left' => 'lft',
										'right' => 'rgt',
										'id' => 'node_id',
										'title' => 'label'));
		
		$delimiter = $this->EE->TMPL->fetch_param('delimiter');
		
		$here = $this->EE->mpttree->get_node_by_entryid($entry_id);

		$return_data = '';
		
		if($delimiter ==''){$delimiter = '&rarr;';}
					
		if($here != '')
		{
			$path = $this->EE->mpttree->get_parents_crumbs($here['lft'],$here['rgt']);
			
			$depth = 0;	
				
			foreach($path as $crumb)
			{
			
				$template_group = 	'/'.$crumb['group_name']; 
				$template_name = 	'/'.$crumb['template_name']; 
				$url_title = 		'/'.$crumb['url_title'];
				
				// don't display /index
				if($template_name == '/index')
				{
					$template_name = '';
				}
				
				$node_url = 	$this->EE->functions->fetch_site_index().$template_group.$template_name.$url_title;
				// if we're not using an index, get rid of double slashes
				$node_url = $this->EE->functions->remove_double_slashes($node_url);
				
				// override template and entry slug with custom url if set
				if($crumb['custom_url'] != '')
				{
					$node_url = $crumb['custom_url'];
				}
				
				if($display_root =="no" && $depth == 0)
				{
					$return_data .= '';
				}
				else
				{
					$return_data .= '<a href="'.$node_url.'">'.$crumb['label'].'</a> '.$delimiter.' ';
				}
				
				$depth++;
				
			}

			$return_data .= $here['label'];
		}	
	
		return $return_data;
	}
	
	
	function nav($str = "")
	{
		
		$tree = $this->EE->TMPL->fetch_param('tree_id');
		
		if ($this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			$str = $this->EE->TMPL->tagdata;; 
			$depth = $this->EE->TMPL->fetch_param('depth');
			
			if($depth == ""){$depth = 100;}
			
			$display_root = $this->EE->TMPL->fetch_param('display_root');
			
			if($this->EE->TMPL->fetch_param('root_node_lft'))
			{
				$root = $this->EE->TMPL->fetch_param('root_node_lft');
			}
			else
			{
				$root = 1;
			}

			$this->EE->load->library('MPTtree');
			$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
												'left' => 'lft',
												'right' => 'rgt',
												'id' => 'node_id',
												'title' => 'label'));
			
			$entry_id = $this->EE->TMPL->fetch_param('entry_id');
			$here = $this->EE->mpttree->get_node_by_entryid($entry_id);
			$path = array();
			
			if($here != '')
			{
				$path = $this->EE->mpttree->get_parents_crumbs($here['lft'],$here['rgt']);
				$entry_id = $here['entry_id'];
			}

			$tree2array = $this->EE->mpttree->tree2array_v2($root);
					
			return $this->EE->mpttree->mptree_cp_loop($tree2array, $str, $display_root, $depth, $path, $entry_id);
		}
		else
		{
			return false;
		}
	}


	function node_url()
	{
		$tree = $this->EE->TMPL->fetch_param('tree_id');
		
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			return false;
		}		

		// set a session variable with an array of all the node entry_ids and path settings
		if ( ! isset($this->EE->session->cache['taxonomy']['templates_to_entries'][$tree]))
		{

			$this->EE->load->library('MPTtree');
			$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
											'left' => 'lft',
											'right' => 'rgt',
											'id' => 'node_id',
											'title' => 'label'));
			
			$tree_array = $this->EE->mpttree->build_session_path_array();
	
			$entry = array();
			$url_title = '';
			$node_url = '';
			$template_group = '';
			$template_name = '';

			foreach($tree_array as $node)
			{
				$template_group = 	'/'.$node['group_name']; 
				$template_name = 	'/'.$node['template_name']; 
				$url_title = 		'/'.$node['url_title'];
				
				
				// don't display /index
				if($template_name == '/index')
				{
					$template_name = '';
				}
				
				$node_url = $this->EE->functions->fetch_site_index().$template_group.$template_name.$url_title;
				// if we're not using an index, get rid of double slashes
				$node_url = $this->EE->functions->remove_double_slashes($node_url);
				
				if($node['custom_url'] != '')
				{
					$node_url = $node['custom_url'];
				}
				
				$entry[$tree][$node['entry_id']] =  $node_url;
			}
											
			$this->EE->session->cache['taxonomy']['templates_to_entries'][$tree][] = $entry;
			
			// print_r($this->EE->session->cache['taxonomy']['templates_to_entries'][$tree]);
			
		}
				
		$tree_key = $this->EE->session->cache['taxonomy']['templates_to_entries'][$tree];
		
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		
		
		if(array_key_exists($entry_id, $tree_key[0][$tree]))
		{
			return $tree_key[0][$tree][$entry_id];
		}
		
		
	}
	

	function get_children_ids()
	{

		$tree = $this->EE->TMPL->fetch_param('tree_id');

		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			return false;
		}
				
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');

		$this->EE->load->library('MPTtree');
		$this->EE->mpttree->set_opts(array( 'table' => 'exp_taxonomy_tree_'.$tree,
											'left' => 'lft',
											'right' => 'rgt',
											'id' => 'node_id',
											'title' => 'label'));
		
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');

		$depth = $this->EE->TMPL->fetch_param('depth');	

		$here = $this->EE->mpttree->get_node_by_entryid($entry_id);

		$immediate_children = array();
		$child_entry_ids = '';

		if($here != '')
		{
			$immediate_children = $this->EE->mpttree->get_children_ids($here['node_id']);

			foreach($immediate_children as $child)
			{
				$child_entry_ids .= $child['entry_id'].'|';
			}
		}

		$entry_id = "|".$entry_id;

		$child_entry_ids = str_replace($entry_id, '', $child_entry_ids);

		return rtrim($child_entry_ids, '|');

	}

	function get_sibling_ids()
	{
	
		//must be a more efficient method of getting siblings?
		
		$tree = $this->EE->TMPL->fetch_param('tree_id');
		
		// check the table exists
		if ( ! $this->EE->db->table_exists('exp_taxonomy_tree_'.$tree))
		{
			return false;
		}
		
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		$include_current = $this->EE->TMPL->fetch_param('include_current');
		
		// where are we
		$here = $this->EE->mpttree->get_node_by_entryid($entry_id);
		
		if($here =="")
		{
			return false;
		}
				
		// find daddy
		$parent = $this->EE->mpttree->get_parent($here['lft'],$here['rgt']);
		
		// get the kids ready for school
		$siblings = $this->EE->mpttree->get_children_ids($parent['node_id']);
		
		$return = '';
		
		foreach($siblings as $sibling)
		{
			$return .= $sibling['entry_id'].'|';
		}
		
		// do we want the entry_id of the current node?
		if($include_current != 'yes')
		{
			$return = str_replace($here['entry_id'].'|', '', $return);
		}
		
		// pop off the last pipe
		$return = rtrim($return, "|");
		
		return $return;
	
	}


} // end class Taxonomy



/* End of file mod.taxonomy.php */
/* Location: ./system/expressionengine/third_party/taxonomy/mod.taxonomy.php */