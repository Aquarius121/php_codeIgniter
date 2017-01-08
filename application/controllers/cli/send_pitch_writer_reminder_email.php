<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Pitch_Writer_Reminder_Email_Controller extends CLI_Base {

	public function index()
	{
		$now_str = Date::$now->format(Date::FORMAT_MYSQL);
		$delivery_rush = Model_Pitch_Order::DELIVERY_RUSH;
		$delivery_standard = Model_Pitch_Order::DELIVERY_STANDARD;

		$condition1 = "(po.delivery = '{$delivery_rush}' AND 
							pw.process_date <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 12 HOUR))";

		$condition2 = "(po.delivery = '{$delivery_standard}' AND 
							pw.process_date <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 DAY))";

		$pw_status_list = sql_in_list(array(Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER, 
											Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER));
		$sql = "SELECT po.*, 
				pw.process_date AS date_assigned,
				pr.pitch_order_id AS reminder_id
				FROM pw_pitch_order po
				LEFT JOIN pw_pitch_writing_process pw
				ON pw.pitch_order_id = po.id
				AND pw.process = ?
				LEFT JOIN pw_pitch_writer_reminder pr
				ON pr.pitch_order_id = po.id
				WHERE po.status IN ({$pw_status_list})
				AND pr.pitch_order_id is NULL
				AND ({$condition1} OR {$condition2})";

		$query = $this->db->query($sql, array(Model_Pitch_Writing_Process::PROCESS_ASSIGNED_TO_WRITER));
		$results = Model_Pitch_Order::from_db_all($query);
		foreach ($results as $result)
		{			
			$pw_mailer = new Pitch_Wizard_Mailer();
			$pw_mailer->send_reminder_email_to_writer($result->id);
			
			$m_pw_reminder = new Model_Pitch_Writer_Reminder();
			$m_pw_reminder->pitch_order_id = $result->id;
			$m_pw_reminder->date_reminder_sent = Date::$now->format(DATE::FORMAT_MYSQL);
			$m_pw_reminder->save();
		}
	}

}

?>