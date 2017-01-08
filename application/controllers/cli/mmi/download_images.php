<?php 

load_controller('cli/base');

class Download_Images_Controller extends CLI_Base {
			
	const PICTURES_URL = 'https://www.mymediainfo.com/journals-photos/%d.gif';
	const BASE_URL = 'https://www.mymediainfo.com';

	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($last_id = 0, $max_id = 999999999)
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		
		$counter = 0;
		$count = Model_MMI_Contact::count_all(array(
			array('remote_id', '>', $last_id),
			array('remote_id', '<', $max_id),
		));

		$params = array();
		$params[] = 'mmi';
		$params[] = 'download_images';
		$params[] = 'worker';

		$task = new CI_Background_Task();
		$task->set($params);
		$task->run(4);

		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->useTube('download_images');

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
				$picture = $mmi_contact->raw_data_object()->picture;
				if (!$picture) continue;

				$beanstalk->put($picture);
				$this->trace_info(sprintf('[%d/%d]', $counter, $count), 
					$mmi_contact->remote_id, $picture);
			}

			// make sure that the queue doesn't get too big
			// by sleeping until the queue shrinks
			while (true)
			{
				$stats = $beanstalk->statsTube('download_images');
				if ($stats['current-jobs-ready'] < 1000) break;
				usleep(500000);
			}
		}

		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
	}

	public function worker()
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		$start = time();

		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->watch('download_images');

		while (true)
		{
			$job = $beanstalk->reserve();
			$job->delete();

			// end of processing signalled
			if ($job->body === 'terminate')
				break;

			$file = sprintf('raw/mmi_contacts/images/%s', md5($job->body));
			$picture_url = static::BASE_URL . $job->body;
			@copy($picture_url, $file);

			// restart every 60 seconds
			if (time()-$start >= 60)
			{
				$params = array();
				$params[] = 'mmi';
				$params[] = 'download_images';
				$params[] = 'worker';
				$task = new CI_Background_Task();
				$task->set($params);
				$task->run(1);
				break;
			}
		}
	}
		
}
