<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Import_From_Search_Download_Controller extends CLI_Base {
	
	const FILES_DIR = 'raw/mmi_contacts';
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function import_file($file)
	{
		$col_3 = 'raw_title';
		$col_4 = 'raw_outlet';
		$col_5 = 'email';

		$base_dir = static::FILES_DIR;
		libxml_use_internal_errors(true);
		set_memory_limit('2048M');
		set_time_limit(0);
		
		$ex_params = array();
		$base64_data = @end(explode('_', $file));
		$ex_params_str = base64_decode($base64_data);
		parse_str($ex_params_str, $ex_params);
		
		$this->trace($file, 'load_document');
		// we need to prepend a header with the content type
		$header = file_get_contents("{$base_dir}/header");
		$source = file_get_contents($file);
		$source = $header . $source;
		$document = new DOMDocument();
		$document->recover = true;
		$document->strictErrorChecking = false;
		$document->loadHTML($source);
		
		$this->trace($file, 'find_rows');
		$rows = $document->getElementsByTagName('tr');
		$total_rows = $rows->length;
		
		for ($i = 0; $i < $total_rows; $i++)
		{
			$row = $rows->item($i);
			$css_class = $row->getAttribute('class');
			if (!preg_match('#\btable_row\b#', $css_class))
				continue;
			
			$cols = $row->getElementsByTagName('td');
			$remote_id = $cols->item(0)->getElementsByTagName('input')->item(0)->getAttribute('value');

			$first_name = trim($cols->item(1)->textContent);
			$last_name = trim($cols->item(2)->textContent);
			$col_3_value = trim($cols->item(3)->textContent);
			$col_4_value = trim($cols->item(4)->textContent);
			$col_5_value = trim($cols->item(5)->textContent);
			$first_name = to_utf8_remove_4b($first_name);
			$last_name = to_utf8_remove_4b($last_name);
			$col_3_value = to_utf8_remove_4b($col_3_value);
			$col_4_value = to_utf8_remove_4b($col_4_value);
			$col_5_value = to_utf8_remove_4b($col_5_value);
			
			$hover_data = $cols->item(1)->getElementsByTagName('div')
				->item(0)->getAttribute('data-hover-value');
			$hover_data_parts = explode('####', stripslashes($hover_data));
			$rc_parts = preg_split('#<br[^>]*>#i', $hover_data_parts[5]);
			$companies = array();
			$roles = array();
			
			foreach ($rc_parts as $role_company_str)
			{
				$role_company_parts = explode(':', $role_company_str);
				if (count($role_company_parts) < 2) continue;
				$roles[] = trim($role_company_parts[0]);
				$companies[] = trim($role_company_parts[1]);
			}
						
			if (!$col_3_value)
			{
				$images = $cols->item(3)->getElementsByTagName('img');
				if ($images->length) $col_3_value = $images->item(0)->getAttribute('title');
			}
			
			if (!$col_4_value)
			{
				$images = $cols->item(4)->getElementsByTagName('img');
				if ($images->length) $col_4_value = $images->item(0)->getAttribute('title');
			}
			
			if (!$col_5_value)
			{
				$images = $cols->item(5)->getElementsByTagName('img');
				if ($images->length) $col_5_value = $images->item(0)->getAttribute('title');
			}
			
			$mmi_contact = Model_MMI_Contact::find($remote_id);
			if ($mmi_contact) 
			     $this->trace_info('update existing', $remote_id);
			else $mmi_contact = new Model_MMI_Contact();
			$mmi_contact->remote_id = $remote_id;
			$raw_data = $mmi_contact->raw_data();
			if (!$raw_data) $raw_data = new stdClass();
			$raw_data->first_name = $first_name;
			$raw_data->last_name = $last_name;
			if ($col_3) $raw_data->{$col_3} = $col_3_value;
			if ($col_4) $raw_data->{$col_4} = $col_4_value;
			if ($col_5) $raw_data->{$col_5} = $col_5_value;
			
			if (isset($ex_params['coverageTypeIDs']))
				$raw_data->contact_coverage_id = 
					$ex_params['coverageTypeIDs'];
			
			$raw_data->companies = $companies;
			$raw_data->roles = $roles;
			$mmi_contact->raw_data($raw_data);
			$mmi_contact->save();
			
			$this->trace_success($file, $i, $mmi_contact->remote_id,
				$raw_data->first_name, $raw_data->last_name);
		}
	}
	
}

?>