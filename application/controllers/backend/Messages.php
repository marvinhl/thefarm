<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends TF_Controller {
	public function index()
	{
		if (!$this->session->has_userdata('user_id'))
		{
			redirect('login');
		}
		$this->load->view('settings');
	}

	public function json() {
		$user_id = $this->input->get_post('current_user');
		$recieved = $this->input->get_post('recieved');
		$this->db->select('message, message_id, UNIX_TIMESTAMP(date_sent) AS date_sent');
		$this->db->where('receiver', $user_id);
		$this->db->where('received', $recieved);
		$query = $this->db->get('messages');
		$messages = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$message = array(
					'message_id' => $row['message_id'],
					'message' => $row['message'],
					'sent' => get_time_ago($row['date_sent']),
				);
				if ($recieved == 0) {
					$this->db->update('messages', array('received' => 1), array('message_id' => $row['message_id']));
				}
				$messages[] = $message;
			}
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($messages));
	}
}
