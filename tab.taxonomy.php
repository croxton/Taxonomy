<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Created on April 10 2010
 * Copyright Iain Urquhart www.iain.co.nz
 */
/*
    This file is part of Taxonomy for ExpressionEngine.

    Taxonomy is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Taxonomy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Taxonomy.  If not, see <http://www.gnu.org/licenses/>.
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