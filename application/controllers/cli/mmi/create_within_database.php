<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Create_Within_Database_Controller extends CLI_Base {
	
	const PICTURES_URL = 'https://www.mymediainfo.com/journals-photos/%d.gif';
	const BASE_URL = 'https://www.mymediainfo.com';
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index($last_id = 0, $max_id = 999999999)
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		$counter = 0;	
		
		$sql_process = "SELECT * FROM nr_mmi_contact mc
			WHERE remote_id > ?
			AND remote_id < {$max_id}
			ORDER BY remote_id ASC
			LIMIT 1000";
			
		while (true)
		{			
			$db_result = $this->db->query($sql_process, array($last_id));
			$mmi_contacts = Model_MMI_Contact::from_db_all($db_result);
			if (!$mmi_contacts) break;
			
			foreach ($mmi_contacts as $mmi_contact)
			{
				$counter++;
				$last_id = $mmi_contact->remote_id;
				$raw_data = $mmi_contact->raw_data();
				$raw_data_single = clone $raw_data;
				
				if (!$raw_data->email || !filter_var($raw_data->email, FILTER_VALIDATE_EMAIL))
				{
					$this->trace_warn($counter, $mmi_contact->remote_id, 'deleted');
					$mmi_contact->delete();
					continue;
				}
				
				$this->trace_info($counter, $mmi_contact->remote_id, 'started');
				$contact_ids = array();
				
				foreach ($raw_data as $k => $v)
				{
					if (!is_string($v)) continue;
					// it's possible to have multiple values so 
					// for now we are just taking the first one
					$raw_data_single->{$k} = trim(explode(';', $v)[0]);
				}
				
				foreach ($raw_data->companies as $cr_index => $company)
				{			
					$prev_contact_ids = (array) @$raw_data->contact_ids;
					$prev_contact_ids[] = -1;
					$prev_contact_ids_str = sql_in_list($prev_contact_ids);

					$sql = "SELECT * FROM nr_contact 
						WHERE company_name LIKE ?
						AND id IN ({$prev_contact_ids_str})";

					if (($contact = Model_Contact::from_sql($sql, array($company))))
					{
						$this->trace_info($counter, $mmi_contact->remote_id, 'using existing contact');
					}

					if (!$contact) $contact = new Model_Contact();
					$contact->company_id = null;
					$contact->is_media_db_contact = 1;
					$contact->email = $raw_data_single->email;
					$contact->first_name = $raw_data_single->first_name;
					$contact->last_name = $raw_data_single->last_name;
					$contact->company_name = $company;
					$contact->title = @$raw_data_single->title;
					$contact->twitter = Social_Twitter_Profile::parse_id(@$raw_data_single->twitter);

					if ($raw_data_single->media_type)
					{
						$m_media_type = Model_Contact_Media_Type::find(array('media_type', 'like', $raw_data_single->media_type));
						
						if (!$m_media_type) 
						{
							$m_media_type = new Model_Contact_Media_Type();
							$m_media_type->media_type = $raw_data_single->media_type;
							$m_media_type->save();
						}
						
						$contact->contact_media_type_id = $m_media_type->id;
					}
					
					if ($role = $raw_data->roles[$cr_index])
					{
						$m_role = Model_Contact_Role::find(array('role', 'like', $role));
						
						if (!$m_role) 
						{
							$m_role = new Model_Contact_Role();
							$m_role->role = $role;
							$m_role->save();
						}
						
						$contact->contact_role_id = $m_role->id;
					}
					
					$country_map = array(
						'United States of America' => 'United States',
						'Antigua and Barbuda' => 'Antigua & Barbuda',
						'Bosnia and Herzegovina' => 'Bosnia & Herzegovina',
						'Heard Island and Mcdonald Islands' => 'Heard Island & Mcdonald Islands',
						'Saint Kitts and Nevis' => 'Saint Kitts & Nevis',
						'Saint Pierre and Miquelon' => 'Saint Pierre & Miquelon',
						'Saint Vincent and The Grenadines' => 'Saint Vincent & The Grenadines',
						'Sao Tome and Principe' => 'Sao Tome & Principe',
						'South Georgia and The South Sandwich Islan' => 'South Georgia & The South Sandwich Islan',
						'Svalbard and Jan Mayen' => 'Svalbard & Jan Mayen',
						'Trinidad and Tobago' => 'Trinidad & Tobago',
						'Turks and Caicos Islands' => 'Turks & Caicos Islands',
						'Wallis and Futuna' => 'Wallis & Futuna',
					);
					
					if (!@$raw_data_single->country && @$raw_data->address_country)
						$raw_data_single->country = $raw_data->address_country;

					if (@$raw_data_single->country)
					{
						if (isset($country_map[$raw_data_single->country]))
							$raw_data_single->country = $country_map[$raw_data_single->country];
						$m_country = Model_Country::find(array('name', 'like', $raw_data_single->country));
						
						if (!$m_country) 
						{
							$m_country = new Model_Country();
							$m_country->name = $raw_data_single->country;
							$m_country->save();
						}						
												
						$contact->country_id = $m_country->id;
					}
					
					// use locality/region from full profile if both id's are known 
					// (as this has some sort of guarantee about accuracy)
					if ($raw_data->address_locality_id && $raw_data->address_region_id)
					{
						$contact->region_id = $raw_data->address_region_id;
						$contact->locality_id = $raw_data->address_locality_id;
					}
					else ////////////////////////////
					// ----------------------------------------------------
					// try and use existing city/state data or fallback to 
					// partial address_locality/address_region
					// ----------------------------------------------------
					{
						$raw_data_single->state = trim(preg_replace('#^[0-9\- ]+#is', '', @$raw_data_single->state));
						$raw_data_single->city = trim(preg_replace('#^[0-9\- ]+#is', '', @$raw_data_single->city));
						$raw_data_single->state = trim(preg_replace('#[0-9\- ,]+$#is', '', @$raw_data_single->state));
						$raw_data_single->city = trim(preg_replace('#[0-9\- ,]+$#is', '', @$raw_data_single->city));
						
						if ($raw_data_single->state)
						{
							$m_region = Model_Region::find(array('name', 'like', $raw_data_single->state));
							if (!$m_region) $m_region = Model_Region::find(array('abbr', 'like', $raw_data_single->state));
							
							if (!$m_region && !$raw_data->address_region_id) 
							{
								$this->trace_warn('new region', $raw_data_single->state);
								$m_region = new Model_Region();
								$m_region->name = $raw_data_single->state;
								$m_region->country_id = $contact->country_id;
								$m_region->save();
							}
							else if ($m_region)
							{
								$contact->region_id = $m_region->id;
							}
						}
						
						if ($raw_data_single->city)
						{
							$raw_data_single->city = trim(preg_replace('#^[0-9\- ]+#is', '', $raw_data_single->city));
							$m_locality = Model_Locality::find(array('name', 'like', $raw_data_single->city));
							
							if (!$m_locality && !$raw_data->address_locality_id) 
							{
								$this->trace_warn('new city', $raw_data_single->city);
								$m_locality = new Model_Locality();
								$m_locality->name = $raw_data_single->city;
								$m_locality->region_id = $contact->region_id;
								$m_locality->country_id = $contact->country_id;
								$m_locality->save();

								$contact->locality_id = $m_locality->id;
							}
							else if ($m_locality)
							{
								$contact->locality_id = $m_locality->id;
							}
						}

						if (!$contact->region_id && @$raw_data->address_region_id)
						{
							$contact->region_id = $raw_data->address_region_id;
						}

						if (!$contact->locality_id && @$raw_data->address_locality_id)
						{
							$contact->locality_id = $raw_data->address_locality_id;
						}
						else if (!$contact->locality_id && !@$raw_data->address_locality_id && 
							@$raw_data->address_locality && @$raw_data->address_region_id)
						{
							$m_locality = Model_Locality::find(array(
								array('name', 'like', $raw_data->address_locality),
								array('region_id', $raw_data->address_region_id)
							));

							if (!$m_locality)
							{
								$this->trace_warn('new city', $raw_data_single->address_locality);
								$m_locality = new Model_Locality();
								$m_locality->name = $raw_data->address_locality;
								$m_locality->region_id = $raw_data->address_region_id;
								$m_locality->country_id = $contact->country_id;
								$m_locality->save();
							}

							$contact->locality_id = $m_locality->id;
						}
					}
					
					$contact->contact_coverage_id = value_or_null(@$raw_data->contact_coverage_id);
					$contact->zip = value_or_null(@$raw_data_single->zip);

					if (!@$raw_data_single->zip && @$raw_data->address_zip)
					{
						$contact->zip = $raw_data->address_zip;
					}
					
					if (!is_array(@$raw_data->beats))
						$raw_data->beats = preg_split('#\s*;\s*#', @$raw_data->beats);
					$beats = $raw_data->beats = array_unique($raw_data->beats);
					$contact->beat_1_id = Model_Beat::get_beat_id_for_name(@$beats[0]);
					$contact->beat_2_id = Model_Beat::get_beat_id_for_name(@$beats[1]);
					$contact->beat_3_id = Model_Beat::get_beat_id_for_name(@$beats[2]);
					
					if (!$contact->beat_1_id && @$beats[0])
					{
						$m_beat_group_id = Model_Beat::get_beat_id_for_name('Other');
						$m_beat = new Model_Beat();
						$m_beat->name = $beats[0];
						$m_beat->beat_group_id = $m_beat_group_id;
						$m_beat->save();
						$contact->beat_1_id = $m_beat->id;
					}
					
					if (!$contact->beat_2_id && @$beats[1])
					{
						$m_beat_group_id = Model_Beat::get_beat_id_for_name('Other');
						$m_beat = new Model_Beat();
						$m_beat->name = $beats[1];
						$m_beat->beat_group_id = $m_beat_group_id;
						$m_beat->save();
						$contact->beat_2_id = $m_beat->id;
					}
					
					$contact->save();
					$contact_ids[] = $contact->id;
				}
				
				$raw_data->contact_ids = $contact_ids;
				$mmi_contact->raw_data($raw_data);
				$mmi_contact->is_created = 1;
				$mmi_contact->save();

				$contact_profile = Model_Contact_Profile::find_remote(Model_Contact_Profile::REMOTE_TYPE_MMI, $mmi_contact->remote_id);
				if (!$contact_profile) $contact_profile = new Model_Contact_Profile();
				$contact_profile->remote_type = Model_Contact_Profile::REMOTE_TYPE_MMI;
				$contact_profile->remote_id = $mmi_contact->remote_id;
				$contact_profile->save();

				$_profile_rd = new stdClass();
				$_profile_rd->address = @$raw_data->address;
				$_profile_rd->address_country_flag = @$raw_data->address_country_flag;
				$_profile_rd->beats = @$raw_data->beats;
				$_profile_rd->companies = @$raw_data->companies;
				$_profile_rd->date_updated = @$raw_data->date_updated;
				$_profile_rd->fax = @$raw_data->fax;
				$_profile_rd->first_name = @$raw_data->first_name;
				$_profile_rd->languages = @$raw_data->languages;
				$_profile_rd->last_name = @$raw_data->last_name;
				$_profile_rd->linkedin = @$raw_data->linkedin;
				$_profile_rd->media_types = preg_split('#\s*;\s*#', $raw_data->media_type);
				$_profile_rd->phone = (new Phone_Number(@$raw_data->phone))->raw();
				$_profile_rd->profile = @$raw_data->profile;
				$_profile_rd->roles = @$raw_data->roles;
				$_profile_rd->twitter = @$raw_data->twitter;

				$contact_profile->raw_data($_profile_rd);
				$contact_profile->save();

				foreach ($raw_data->contact_ids as $contact_id)
				{
					$sql = "insert ignore into nr_contact_x_contact_profile values (?, ?)";
					$this->db->query($sql, array($contact_id, $contact_profile->id));
				}
				
				// $picture_url = static::PICTURES_URL;
				// $picture_url = sprintf($picture_url, $mmi_contact->remote_id);
				$picture_url = @$raw_data->picture;
				
				if ($picture_url)
				{
					$file = sprintf('raw/mmi_contacts/images/%s', md5($picture_url));

					if (!is_file($file))
					{
						$this->trace_warn('sleeping for download_images');
						sleep(5);
					}

					if (is_file($file))
					{
						$buffer_file = File_Util::buffer_file();
						copy($file, $buffer_file);

						foreach ($contact_ids as $contact_id)
						{
							if ($contact_picture = Model_Contact_Picture::create($buffer_file))
							{
								if ($existing = Model_Contact_Picture::find($contact_id))
									$existing->delete();
								$contact_picture->contact_id = $contact_id;
								$contact_picture->save();
							}
						}

						if (is_file($buffer_file))
							unlink($buffer_file);
					}
				}
				
				$this->trace_success($counter, $mmi_contact->remote_id, 'finished');
			}
		}
	}

}

?>