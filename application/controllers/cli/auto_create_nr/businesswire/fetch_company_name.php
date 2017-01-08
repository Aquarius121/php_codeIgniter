<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Fetch_Company_Name_Controller extends CLI_Base { // fetching businesswire single pr

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT url, businesswire_company_id
				FROM nr_pb_businesswire_pr p
				INNER JOIN ac_nr_businesswire_company c
				ON p.businesswire_company_id = c.id
				WHERE p.url LIKE 'http://businesswire.com/news/home/%'
				AND c.is_company_name_read = 0
				GROUP by p.businesswire_company_id
				ORDER BY c.id DESC
				LIMIT 1";

		while ($cnt++ <= 20)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$businesswire_pr = Model_PB_BusinessWire_PR::from_db($result);
			if (!$businesswire_pr) break;

			$this->get($businesswire_pr);
			sleep(1);
		}
	}

	protected function get($businesswire_pr)
	{
		lib_autoload('simple_html_dom');

		if (empty($businesswire_pr->url))
			return false;

		$url = $businesswire_pr->url."/";
		$matches = array();

		if (preg_match('/home\/([0-9]+)\/([A-Za-z\-]+)\//', $url, $matches))
		{
			$id = $matches[1];
			$lang = $matches[2];
			
			$url = "http://www.businesswire.com/portal/site/home/template.BINARYPORTLET/permalink/";
			$url = "{$url}resource.process/;portal.JSESSIONID=f2Q7VyMScmNDvtpgFx9hH5GftxLGM0xzWpmT0yZbpKp";
			$url = "{$url}lT97zp2F4!1573858074!986354835?javax.portlet.tpst=e8d55157ef2522ec12306b100d908";
			$url = "{$url}a0c&javax.portlet.prp_e8d55157ef2522ec12306b100d908a0c_releaseid={$id}&javax.portlet.";
			$url = "{$url}prp_e8d55157ef2522ec12306b100d908a0c_mmgroupid=&javax.portlet.prp_e8d55157ef2522";
			$url = "{$url}ec12306b100d908a0c_language={$lang}&javax.portlet.rst_e8d55157ef2522ec12306b100d908a0c_";
			$url = "{$url}releaseId={$id}&javax.portlet.rst_e8d55157ef2522ec12306b100d908a0c_displayLanguage";
			$url = "{$url}={$lang}&javax.portlet.rst_e8d55157ef2522ec12306b100d908a0c_language={$lang}&javax.portlet.ri";
			$url = "{$url}d_e8d55157ef2522ec12306b100d908a0c=companyInformation&javax.portlet.rcl_e8d55157ef2";
			$url = "{$url}522ec12306b100d908a0c=cacheLevelPage&javax.portlet.begCacheTok=com.vignette.cacheto";
			$url = "{$url}ken&javax.portlet.endCacheTok=com.vignette.cachetoken";

			//echo $url;
			//exit;
			
			$html = @file_get_html($url);
			if (empty($html))
				return;

			if (!$m_bw_company = Model_BusinessWire_Company::find($businesswire_pr->businesswire_company_id))
					return;

			if ($c_name = @$html->find('h3[itemprop=sourceOrganization] span[itemprop=name]', 0)->innertext)
				$m_bw_company->name = $c_name;
			
			$m_bw_company->is_company_name_read = 1;
			$m_bw_company->save();
			
		}
		
	}
	
}

?>
