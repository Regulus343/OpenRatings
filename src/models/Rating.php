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
			'message'    => Lang::get('open-ratings::messages.error'),
		);

		//ensure user is logged in
		if (!OpenRatings::auth()) return $results;

		$userID      = OpenRatings::userID();
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
			->first();

		if ($rating) {
			$id = $rating->id;
			$results['action'] = "Update";
		} else {
			$id = 0;
			$rating = new static;
			$rating->user_id = $userID;
			$rating->content_id   = $contentID;
			$rating->content_type = $contentType;
			$rating->ip_address = Request::getClientIp();
		}

		$max = Config::get('open-ratings::ratingMax');
		if ($points > $max) $points = $max;

		$rating->points = $points;
		$rating->save();

		$results['ratingID']   = $rating->id;
		$results['resultType'] = "Success";
		$results['message']    = Lang::get('open-ratings::messages.success');

		//set the average rating for the model declared by the content type if feature is enabled
		if ($allowedContentTypes && is_array($allowedContentTypes) && Config::get('open-ratings::setContentRating')) {
			$ratings = static::where('content_id', '=', $contentID)->where('content_type', '=', $contentType)->get();
			$ratingsData = OpenRatings::ratingsData($ratings);

			DB::table($allowedContentTypes[$contentType])->where('id', '=', $contentID)->update(array('rating' => $ratingsData['pointsAverage']));
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

}