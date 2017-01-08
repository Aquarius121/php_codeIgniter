<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Redirect_Newsrooms_Controller extends CLI_Base {

	public function index()
	{
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// THIS CODE HAS BEEN DISABLED AS IT WAS CAUSING
		// ISSUES WITH NEWSWIRE COMPANIES BEING INACCESSIBLE
		// *************************************************
		// *************************************************
		// !!!!!!!!!! IT IS NOT SAFE !!!!!!!!!!!!!!!!!
		// !!!!!!!!!! DO NOT RUN !!!!!!!!!!!!!!!!!!!!!
		// *************************************************
		// *************************************************
		// *************************************************
		// Suggestions: 
		// > Check that a newsroom exists with 
		//   new_slug before adding a redirect
		// > Check that the new newsroom has all the 
		//   content/contacts/customizations that the 
		//   old one had. 
		// > Make sure newswire companies are not 
		//   included (the array used currently 
		//   does nothing)
		// > If any doubt please review with me before
		//   running this again. 
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************
		// *************************************************

		// $sql = "SELECT c.newsroom
		// 		FROM nr_company c
		// 		INNER JOIN nr_newsroom n
		// 		ON n.company_id = c.id
		// 		LEFT JOIN nr_newsroom_redirect r
		// 		ON c.newsroom = r.old_slug
		// 		WHERE newsroom REGEXP '^.*[0-9]{3}$'
		// 		AND c.date_created > '2015-03-15'
		// 		AND r.old_slug IS NULL
		// 		AND n.is_active = 1";

		// $query = $this->db->query($sql, array(Model_Company::SOURCE_BUSINESSWIRE, 
		// 	Model_Company::SOURCE_MARKETWIRED, Model_Company::SOURCE_CRUNCHBASE, 
		// 	Model_Company::SOURCE_OWLER, Model_Company::SOURCE_PRWEB));

		// $results = Model_Company::from_db_all($query);

		// foreach ($results as $result)
		// {
		// 	$redirect = new Model_Newsroom_Redirect();
		// 	$old_slug = $result->newsroom;
		// 	$new_slug = substr($old_slug, 0, strlen($old_slug) - 3);
		// 	$redirect->old_slug = $old_slug;
		// 	$redirect->new_slug = $new_slug;
		// 	$redirect->save();
		// }
	}

}

?>
