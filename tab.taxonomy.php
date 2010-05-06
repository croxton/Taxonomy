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