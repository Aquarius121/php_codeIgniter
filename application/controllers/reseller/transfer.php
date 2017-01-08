<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_parent_controller('api/iella/base');

class Transfer_Controller extends Iella_Base {
	public function index()
	{
		// prints the string "world"
		$response="";
		$this->iella_out->companyExists="Yes";
		$company_profile = new Model_Company_Profile();
		$this->iella_out->companyProfile=$this->iella_in->companyProfile;
		$company=Model_Newsroom::find_name($this->iella_in->companyName);
		if(!$company)
		{
			$company=$this->createNewsRoom($this->iella_in->companyName,$this->iella_in->user_id);
			$comp_profile=$this->createCompanyProfile($company->company_id,$this->iella_in->companyProfile);
		}
		else
		{
			//$comp_profile=$company_profile->find('company_id',$company->company_id);
			//if($company_profile::find()
		}	
		
		$this->iella_out->company = $company;
		$this->iella_out->company_profile = $comp_profile;
		$this->send();
	}
	public function createNewsRoom($nrName, $user_id)
	{
		$newsroom = Model_Newsroom::create($user_id);
 		$newsroom->name = $nrName;
 		$newsroom->save();
		$this->iella_out->responseText.="<br> NewsRoom Created";
		return $newsroom;
	}
	public function createCompanyProfile($company_id,$companyProfile)
	{
		$company_profile = new Model_Company_Profile();
		$company_profile->company_id = $company_id;
		$company_profile->address_street = $companyProfile->address_street;
		$company_profile->summary = $companyProfile->summary;
		$company_profile->save();
		return $company_profile;
	}
	
}

?>
