<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Import_MaxMind_DB_Controller extends CLI_Base {
	
	const LINK_BINARY = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
	const LINK_CSV = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip';
	const PATH_BASE = 'application/data/maxmind_db';
	const PATH_BINARY = 'application/data/maxmind_db/db.mmdb.gz';
	const PATH_CSV = 'application/data/maxmind_db/db.csv.zip';
	const PATH_CSV_EXTRACT = 'application/data/maxmind_db/csv';
	const PATH_CSV_IPv4 = 'application/data/maxmind_db/csv/GeoLite2-City-Blocks-IPv4.csv';
	const PATH_CSV_L_EN = 'application/data/maxmind_db/csv/GeoLite2-City-Locations-en.csv';

	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index()
	{
		set_time_limit(7200);

		if (!is_dir(static::PATH_BASE))
			mkdir(static::PATH_BASE);

		$this->trace('binary download: started');
		if (!copy(static::LINK_BINARY, static::PATH_BINARY))
			throw new Exception();
		$this->trace('binary download: finished');

		$this->trace('csv download: started');
		if (!copy(static::LINK_CSV, static::PATH_CSV))
			throw new Exception();
		$this->trace('csv download: finished');

		$this->trace('binary decompress: started');
		echo shell_exec(sprintf('gunzip -f %s', 
			escapeshellarg(static::PATH_BINARY)));
		$this->trace('binary decompress: finished');

		$this->trace('csv decompress: started');
		if (!is_dir(static::PATH_CSV_EXTRACT)) 
			mkdir(static::PATH_CSV_EXTRACT);
		echo shell_exec(sprintf('unzip -oj %s -d %s', 
			escapeshellarg(static::PATH_CSV),
			escapeshellarg(static::PATH_CSV_EXTRACT)));
		unlink(static::PATH_CSV);
		$this->trace('csv decompress: finished');

		// -------------------------------------

		$this->trace('csv import: started');
		$stat = $this->load_db('stat');

		$stat->query('DROP TABLE IF EXISTS _maxmind_db_build_ip');
		$stat->query('CREATE TABLE _maxmind_db_build_ip (
			IP_from INT UNSIGNED NOT NULL,
			IP_to INT UNSIGNED NOT NULL, 
			geoname_id INT UNSIGNED NOT NULL,
			INDEX gid (geoname_id)
		)');

		$chunk = array();
		$chunk_num = 0;
		$csv = new CSV_Reader(static::PATH_CSV_IPv4);
		$csv->read();

		while ($line = $csv->read())
		{
			$subnet = explode('/', $line[0]);
			$from = $this->subnet_first($subnet[0], $subnet[1]);
			$to = $this->subnet_last($subnet[0], $subnet[1]);
			$chunk[] = array($from, $to, $line[1]);

			if (count($chunk) % 1000 == 0)
			{
				$this->trace('_maxmind_db_build_ip', $chunk_num++);
				$this->insert_chunk($stat, '_maxmind_db_build_ip', $chunk);
				$chunk = array();
			}
		}

		$this->insert_chunk($stat, '_maxmind_db_build_ip', $chunk);
		$csv->close();

		$stat->query('DROP TABLE IF EXISTS _maxmind_db_build_loc');
		$stat->query('CREATE TABLE _maxmind_db_build_loc (
			geoname_id INT UNSIGNED NOT NULL,
			geo_country CHAR(2) NOT NULL,
			geo_sub CHAR(3) NOT NULL,
			INDEX gid (geoname_id)
		)');

		$chunk = array();
		$chunk_num = 0;
		$csv = new CSV_Reader(static::PATH_CSV_L_EN);
		$csv->read();

		while ($line = $csv->read())
		{
			$chunk[] = array($line[0], $line[4], $line[6]);

			if (count($chunk) % 1000 == 0)
			{
				$this->trace('_maxmind_db_build_loc', $chunk_num++);
				$this->insert_chunk($stat, '_maxmind_db_build_loc', $chunk);
				$chunk = array();
			}
		}

		// insert final chunk
		$this->insert_chunk($stat, '_maxmind_db_build_loc', $chunk);
		$csv->close();

		$this->trace('addr_location');
		$stat->query('TRUNCATE addr_location');
		$stat->query('INSERT INTO addr_location
			SELECT i.IP_From, i.IP_To, l.geo_country, l.geo_sub
			FROM _maxmind_db_build_ip i
			INNER JOIN _maxmind_db_build_loc l 
			ON i.geoname_id = l.geoname_id
			ORDER BY i.IP_From DESC, i.IP_To ASC
		');

		$this->trace('location_data');
		$stat->query('TRUNCATE location_data');

		$chunk = array();
		$chunk_num = 0;
		$csv = new CSV_Reader(static::PATH_CSV_L_EN);
		$csv->read();

		while ($line = $csv->read())
		{
			// skip any empty row
			if (!trim($line[4])) continue;

			// country_iso, sub_iso,
			// country_name, sub_name
			$chunk[] = array($line[4], $line[6], 
				value_or_null($line[5]), 
				value_or_null($line[7]));

			if (count($chunk) % 1000 == 0)
			{
				$this->trace('location_data', $chunk_num++);
				$this->insert_chunk($stat, 'location_data', $chunk);
				$chunk = array();
			}
		}

		// insert final chunk
		$this->insert_chunk($stat, 'location_data', $chunk);
		$csv->close();

		$stat->query('DROP TABLE IF EXISTS _maxmind_db_build_ip');
		$stat->query('DROP TABLE IF EXISTS _maxmind_db_build_loc');
		$this->trace('csv import: finished');
	}

	protected function subnet_first($ip, $cidr)
	{
		return (ip2long($ip)) & ((-1 << (32 - (int) $cidr)));
	}

	protected function subnet_last($ip, $cidr)
	{
		return $this->subnet_first($ip, $cidr) + (pow(2, (32-$cidr)) - 1);
	}

	protected function insert_chunk($db, $table, &$chunk)
	{
		$inserts = array();
		// transform to SQL values line
		foreach ($chunk as $insert)
			$inserts[] = sql_insert_line($insert);
		$inserts = comma_separate($inserts);

		// insert into this table
		$sql = "INSERT IGNORE INTO 
			{$table} VALUES {$inserts}";
		$db->query($sql);
	}

}

?>