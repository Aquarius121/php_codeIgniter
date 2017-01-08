<?php 

abstract class PRNewswire_Distribution {

	const DIST_NATIONAL = 'DIST_NATIONAL';
	const DIST_STATELINE = 'DIST_STATELINE';
	const DIST_WEB = 'DIST_WEB';

	const MICROLISTS_FILE = 'raw/distribution/prnewswire/microlists.php';

	protected static $microlists;
	protected static $states = array(

		'ALA' => 'Alabama',
		'ALK' => 'Alaska',
		'ARI' => 'Arizona',
		'ARK' => 'Arkansas',
		'CAL' => 'California',
		'CLV' => 'Cleveland',
		'COL' => 'Colorado',
		'DEL' => 'Delaware',
		'FLA' => 'Florida',
		'GEO' => 'Georgia',
		'HAW' => 'Hawaii',
		'IDA' => 'Idaho',
		'ILL' => 'Illinois',
		'INI' => 'Indiana',
		'IWA' => 'Iowa',
		'KAN' => 'Kansas',
		'KEN' => 'Kentucky',
		'LOU' => 'Louisiana',
		'MAI' => 'Maine',
		'MAR' => 'Maryland',
		'MAS' => 'Massachusetts',
		'MIC' => 'Michigan',
		'MIS' => 'Mississippi',
		'MNS' => 'Minnesota',
		'MON' => 'Montana',
		'MOU' => 'Missouri',
		'NEB' => 'Nebraska',
		'NEH' => 'New Hampshire',
		'NEV' => 'Nevada',
		'NJY' => 'New Jersey',
		'NMX' => 'New Mexico',
		'NOC' => 'North Carolina',
		'NOD' => 'North Dakota',
		'NYS' => 'New York State',
		'OHI' => 'Ohio',
		'OKA' => 'Oklahoma',
		'ORE' => 'Oregon',
		'PEN' => 'Pennsylvania',
		'RHO' => 'Rhode Island',
		'SOC' => 'South Carolina',
		'SOD' => 'South Dakota',
		'TEN' => 'Tennessee',
		'TEX' => 'Texas',
		'UTA' => 'Utah',
		'VER' => 'Vermont',
		'VIR' => 'Virginia',
		'WAS' => 'Washington State',
		'WIS' => 'Wisconsin',
		'WYO' => 'Wyoming',

	);

	public static function states()
	{
		return static::$states;
	}

	public static function state($code)
	{
		return static::$states[$code];
	}

	public static function microlists()
	{
		if (!static::$microlists)
		{
			$file = static::MICROLISTS_FILE;
			$microlists = (require $file);
			static::$microlists = array();
			$indexed = array();

			foreach ($microlists as $microlist)
			{
				static::$microlists[$microlist->item_code] = $microlist;
				$slug = static::microlist_slug($microlist->item_code);
				$indexed[$slug] = $microlist;
			}

			$list = sql_in_list(array_keys($indexed));
			$sql = "SELECT * FROM co_item WHERE slug IN ({$list})";
			$items = Model_Item::from_sql_all($sql);
			foreach ($items as $item)
				$indexed[$item->slug]->item = $item;

			uasort(static::$microlists, function($a, $b) {
				$r = spaceship($a->group_name, $b->group_name);
				if ($r === 0) return spaceship($a->name, $b->name);
				return $r;
			});
		}

		return static::$microlists;
	}

	public static function microlist_slug($code)
	{
		return sprintf('microlist-%s', $code);
	}

	public static function included_words($dist)
	{
		$dists = array(
			static::DIST_NATIONAL => 400,
			static::DIST_STATELINE => 400,
			static::DIST_WEB => PHP_INT_MAX,
		);

		return $dists[$dist];
	}

}
