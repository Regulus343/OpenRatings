<?php namespace Regulus\OpenRatings;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Rating extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ratings';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * Can be used to fetch the content that the ratings are attached to.
	 *
	 * @return object
	 */
	public function content()
	{
		return $this->morphTo();
	}

	/**
	 * Gets the creator of the comment.
	 *
	 * @return object
	 */
	public function creator()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}










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










	/**
	 * Creates or updates a comment.
	 *
	 * @param  integer  $id
	 * @return mixed
	 */
	public static function createUpdate($id = 0)
	{
		$points = substr(Input::get('points'), 0, 1);
		if (!is_numeric($points)) $points = 0;
		if ($points > 5)          $points = 5;
		if ($points < 0)          $points = 0;

		$results = array(
			'resultType' => 'Error',
			'action'     => 'Create',
			'ratingID'   => false,
			'points'     => Input::get('points'),
			'message'    => Lang::get('open-ratings::messages.errorGeneral'),
		);

		//ensure user is logged in
		if (!OpenRatings::auth()) return $results;

		$userID      = OpenComments::userID();
		$contentID   = trim(Input::get('content_id'));
		$contentType = trim(Input::get('content_type'));
		$points      = $results['points'];

		//if allowedContentTypes config is set, require the content type to be specified and the item to exist in the database
		$allowedContentTypes = Config::get('open-ratings::allowedContentTypes');
		if ($allowedContentTypes && is_array($allowedContentTypes)) {

			//content type is not allowed; return error results
			if (!isset($allowedContentTypes[$contentType])) return $results;

			//item does not exist in specified table; return error results
			$item = DB::table($allowedContentTypes[$contentType])->find($contentID);
			if (empty($item)) return $results;

			//item is deleted; return error results
			$itemArray = (array) $item;
			if (isset($itemArray['deleted']) && $itemArray['deleted']) return $results;
		}

		//check if an existing rating exists
		$rating = static::orderBy('id')
			->where('user_id', '=', $userID)
			->where('content_id', '=', $contentID)
			->where('content_type', '=', $contentType)
			->find();

		if ($rating) {
			$id = $rating->id;
		} else {
			$id = 0;
			$rating = new static;
			$rating->user_id = $userID;
		}

		if ($results['action'] == "Create") {
			$comment->content_id   = $contentID;
			$comment->content_type = $contentType;
			$comment->parent_id    = $parentID;
			$comment->ip_address   = Request::getClientIp();
		}
		$comment->comment = $commentText;
		$comment->save();

		$results['commentID'] = $comment->id;

		//add order ID for easy comment ordering for queries
		if ($results['action'] == "Create") {
			if ($parentID) {
				$comment->order_id = $parentID;
			} else {
				$comment->order_id = $comment->id;
			}
			$comment->save();

			Session::set('lastComment', $comment->id);
		}

		$results['resultType'] = "Success";
		if ($results['action'] == "Create") {
			$results['message'] = Lang::get('open-comments::messages.successCreated');
			if (!$autoApproval) $results['message'] .= ' '.Lang::get('open-comments::messages.notYetApproved');
		} else {
			$results['message'] = Lang::get('open-comments::messages.successUpdated');
		}

		//set the comment totals for the model declared by the content type if feature is enabled
		if ($allowedContentTypes && is_array($allowedContentTypes) && Config::get('open-comments::setCommentTotals')) {
			$totalComments = static::where('content_id', '=', $contentID)->where('content_type', '=', $contentType)->count();

			DB::table($allowedContentTypes[$contentType])->where('id', '=', $contentID)->update(array('comments' => $totalComments));
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

}