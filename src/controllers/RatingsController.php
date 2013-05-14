<?php namespace Regulus\OpenRatings;

use \BaseController;

use Regulus\Identify\User;
use Regulus\Identify\Role;

class RatingsController extends BaseController {

	public function postSave($type = '')
	{
		$points = substr(Input::get('points'), 0, 1);
		if (!is_numeric($points)) $points = 0;
		if ($points > 5)          $points = 5;
		if ($points < 0)          $points = 0;

		$data = array('user_id'=>		$this->session->userdata('user_id'),
					  'content_id'=>	$this->input->post('content_id'),
					  'content_type'=>	$this->input->post('content_type'),
					  'points'=>		$points,
					  'ip_address'=>	$this->input->ip_address(),
					  'date_updated'=>	date('Y-m-d H:i:s'));
		$existing_rating = $this->db->query("SELECT * FROM ratings WHERE user_id='".$data['user_id']."'
											 AND content_id='".$data['content_id']."' AND content_type='".$data['content_type']."'")->row();

		$existingRating = Rating::orderBy('id')
			->where('user_id', '=', Auth::userID())
			->where('content_id', '=', $contentID)
			->where('content_type', '=', $contentType);

		$rating_own_content = false;
		$item = array();
		switch ($data['content_type']) {
			case "Printable Materials":
				$item = $this->activism->materials_item($data['content_id']);
			break;
		}
		if (!empty($item) && $item->user_id != $data['user_id']) {
			if (!empty($existing_rating)) {
				$this->db->update('ratings', $data, array('id'=>$existing_rating->id));

				//log activity
				$this->general->log_activity(ucwords($data['content_type']).' - Rating Changed', '', $data['content_id']);
			} else {
				$this->db->insert('ratings', $data);

				//log activity
				$this->general->log_activity(ucwords($data['content_type']).' - Rating Saved', '', $data['content_id']);
			}
			if ($this->db->affected_rows() > 0) {
				echo "Success";
			} else {
				echo "Error";
			}
		} else {
			echo "Error";
		}
	}

}