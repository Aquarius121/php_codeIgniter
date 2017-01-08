<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Profile {
	
	public static function url($platform, $profile)
	{
		if ($platform === 'facebook')
			return Social_Facebook_Profile::url($profile);
		if ($platform === 'twitter')
			return Social_Twitter_Profile::url($profile);
		if ($platform === 'gplus')
			return Social_GPlus_Profile::url($profile);
		if ($platform === 'youtube')
			return Social_Youtube_Profile::url($profile);
		if ($platform === 'pinterest')
			return Social_Pinterest_Profile::url($profile);
		return null;
	}
	
}

?>