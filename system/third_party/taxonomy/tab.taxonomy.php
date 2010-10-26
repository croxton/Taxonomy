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


class Taxonomy_tab {

	
	function Taxonomy_tab()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	
	// will get to this eventually...
	
	
	/*
	function publish_tabs($channel_id, $entry_id = '')
	{
		
		
		$settings = array();
		$selected = array();
		$existing_files = array();


		// Load the module lang file for the field label
		$this->EE->lang->loadfile('taxonomy');
		$id_instructions = lang('id_field_instructions');

		$settings[] = array(
				'field_id'				=> '',
				'field_label'			=> $this->EE->lang->line('Node Tree'),
				'field_required' 		=> 'n',
				'field_data'			=> '',
				'field_list_items'		=> '',
				'field_fmt'				=> '',
				'field_instructions' 	=> '',
				'field_show_fmt'		=> 'n',
				'field_fmt_options'		=> array(),
				'field_pre_populate'	=> 'n',
				'field_text_direction'	=> 'ltr',
				'field_type' 			=> 'text'
			);

		return $settings;
	}

	function validate_publish($params)
	{
		return FALSE;
	}
	
	function publish_data_db($params)
	{

	}

	function publish_data_delete_db($params)
	{

	}
	
	*/

}
/* END Class */

/* End of file tab.download.php */
/* Location: ./system/expressionengine/third_party/modules/download/tab.download.php */