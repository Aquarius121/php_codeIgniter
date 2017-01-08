<?php

$config['mmi'] = array(

	// output messages
	'verbose' => TRUE,
	
	// run time in seconds
	'run_time' => (60 * 45),
	
	// sort options
	'sort_options' => array(
		0 => 'ASCENDING',
		1 => 'DESCENDING'
	),
	
	// media types
	'media_types' => array(
		4  => 'Blog',
		5  => 'Freelance Journalist',
		6  => 'Internet Publications',
		3  => 'Journal',
		2  => 'Magazine',
		7  => 'News Service/Wires',
		8  => 'Newsletter',
		1  => 'Newspaper',
		10 => 'Research Firm',
		11 => 'All TV',
		9  => 'All Radio'
	),
	
	// beats in the form:
	// array(id => ?, name => ?)
	'beats' => array(
		array(
			'id' => 2,
			'name' => 'Arts & Entertainment'
		),
		array(
			'id' => 3,
			'name' => 'Business'
		),
		array(
			'id' => 4,
			'name' => 'Education'
		),
		array(
			'id' => 5,
			'name' => 'Financial and Insurance Services'
		),
		array(
			'id' => 6,
			'name' => 'Government'
		),
		array(
			'id' => 7,
			'name' => 'Home'
		),
		array(
			'id' => 8,
			'name' => 'Humanities'
		),
		array(
			'id' => 9,
			'name' => 'Industries'
		),
		array(
			'id' => 10,
			'name' => 'Information Technology'
		),
		array(
			'id' => 11,
			'name' => 'Law'
		),
		array(
			'id' => 12,
			'name' => 'Lifestyles and Society'
		),
		array(
			'id' => 13,
			'name' => 'Medicine and Healthcare'
		),
		array(
			'id' => 716,
			'name' => 'News'
		),
		array(
			'id' => 15,
			'name' => 'Sciences'
		),
		array(
			'id' => 16,
			'name' => 'Sports'
		),
		array(
			'id' => 17,
			'name' => 'Travel and Transportation'
		),
	),
	
	// states in the form:
	// array(id => ?, name => ?)
	'states' => array(
		array(
			'id'   => 35,
			'name' => 'NEW YORK'
		),
		array(
			'id'   => 2,
			'name' => 'ALABAMA'
		),
		array(
			'id'   => 1,
			'name' => 'ALASKA'
		),
		array(
			'id'   => 4,
			'name' => 'ARIZONA'
		),
		array(
			'id'   => 3,
			'name' => 'ARKANSAS'
		),
		array(
			'id'   => 5,
			'name' => 'CALIFORNIA'
		),
		array(
			'id'   => 6,
			'name' => 'COLORADO'
		),
		array(
			'id'   => 7,
			'name' => 'CONNECTICUT'
		),
		array(
			'id'   => 8,
			'name' => 'DISTRICT OF COLUMBIA'
		),
		array(
			'id'   => 9,
			'name' => 'DELAWARE'
		),
		array(
			'id'   => 10,
			'name' => 'FLORIDA'
		),
		array(
			'id'   => 11,
			'name' => 'GEORGIA'
		),
		array(
			'id'   => 12,
			'name' => 'HAWAII'
		),
		array(
			'id'   => 14,
			'name' => 'IDAHO'
		),
		array(
			'id'   => 15,
			'name' => 'ILLINOIS'
		),
		array(
			'id'   => 16,
			'name' => 'INDIANA'
		),
		array(
			'id'   => 13,
			'name' => 'IOWA'
		),
		array(
			'id'   => 17,
			'name' => 'KANSAS'
		),
		array(
			'id'   => 18,
			'name' => 'KENTUCKY'
		),
		array(
			'id'   => 19,
			'name' => 'LOUISIANA'
		),
		array(
			'id'   => 22,
			'name' => 'MAINE'
		),
		array(
			'id'   => 21,
			'name' => 'MARYLAND'
		),
		array(
			'id'   => 20,
			'name' => 'MASSACHUSETTS'
		),
		array(
			'id'   => 23,
			'name' => 'MICHIGAN'
		),
		array(
			'id'   => 24,
			'name' => 'MINNESOTA'
		),
		array(
			'id'   => 26,
			'name' => 'MISSISSIPPI'
		),
		array(
			'id'   => 25,
			'name' => 'MISSOURI'
		),
		array(
			'id'   => 27,
			'name' => 'MONTANA'
		),
		array(
			'id'   => 30,
			'name' => 'NEBRASKA'
		),
		array(
			'id'   => 34,
			'name' => 'NEVADA'
		),
		array(
			'id'   => 31,
			'name' => 'NEW HAMPSHIRE'
		),
		array(
			'id'   => 32,
			'name' => 'NEW JERSEY'
		),
		array(
			'id'   => 33,
			'name' => 'NEW MEXICO'
		),
		array(
			'id'   => 28,
			'name' => 'NORTH CAROLINA'
		),
		array(
			'id'   => 29,
			'name' => 'NORTH DAKOTA'
		),
		array(
			'id'   => 36,
			'name' => 'OHIO'
		),
		array(
			'id'   => 37,
			'name' => 'OKLAHOMA'
		),
		array(
			'id'   => 38,
			'name' => 'OREGON'
		),
		array(
			'id'   => 39,
			'name' => 'PENNSYLVANIA'
		),
		array(
			'id'   => 40,
			'name' => 'RHODE ISLAND'
		),
		array(
			'id'   => 41,
			'name' => 'SOUTH CAROLINA'
		),
		array(
			'id'   => 42,
			'name' => 'SOUTH DAKOTA'
		),
		array(
			'id'   => 43,
			'name' => 'TENNESSEE'
		),
		array(
			'id'   => 44,
			'name' => 'TEXAS'
		),
		array(
			'id'   => 45,
			'name' => 'UTAH'
		),
		array(
			'id'   => 47,
			'name' => 'VERMONT'
		),
		array(
			'id'   => 46,
			'name' => 'VIRGINIA'
		),
		array(
			'id'   => 48,
			'name' => 'WASHINGTON'
		),
		array(
			'id'   => 50,
			'name' => 'WEST VIRGINIA'
		),
		array(
			'id'   => 49,
			'name' => 'WISCONSIN'
		),
	),

);

?>
