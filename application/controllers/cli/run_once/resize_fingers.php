<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Resize_Fingers_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		set_memory_limit('4096M');
		set_time_limit(86400);

		$sqlt = "select max(id) as max from nr_stored_image";
		$max = $this->db->query($sqlt)->row()->max;
		$start = time();

		$sqlq = "select sif.id, sif.filename, sio.filename as original_filename from nr_stored_image sif inner join 
			nr_image_variant ivf on ivf.stored_image_id = sif.id
			and ivf.name = 'finger' inner join nr_image_variant ivo 
			on ivf.image_id = ivo.image_id and ivo.name = 'original'
			inner join nr_stored_image sio on ivo.stored_image_id = sio.id
			where sif.id > ?
			order by sif.id asc
			limit 10000";

		$vsizes = $this->conf('v_sizes');
		$finger = $vsizes['finger'];
		$lastid = 0;

		while (true)
		{
			$dbr = $this->db->query($sqlq, array($lastid));
			$xs = Model_Base::from_db_all($dbr);
			if (!$xs) break;

			foreach ($xs as $x)
			{
				$lastid = $x->id;
				$diff = time()-$start;
				$plus = (int) (((1 / ($lastid / $max)) * $diff) - $diff);
				$this->trace($x->id, Date::difference_in_words(Date::seconds($plus)));
				$sio = Stored_Image::from_stored_filename($x->original_filename);
				if (!$sio->exists()) continue;
				$sif = Stored_Image::from_stored_filename($x->filename);
				if ($sif->exists()) $sif->delete();
				$sif = $sio->from_this_resized($finger);
				$sql = "update nr_stored_image set filename = ?, width = ?, height = ? where id = ?";
				$im = Image::from_file($sif->actual_filename());
				$this->db->query($sql, array($sif->filename, $im->width(), $im->height(), $x->id));
			}
		}
	}

}
