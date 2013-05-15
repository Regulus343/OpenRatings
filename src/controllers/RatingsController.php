<?php namespace Regulus\OpenRatings;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Regulus\Identify\User;
use Regulus\Identify\Role;

class RatingsController extends BaseController {

	public function postSave()
	{
		return json_encode(Rating::createUpdate());
	}

	public function postRemove()
	{
		$contentID   = Input::get('content_id');
		$contentType = Input::get('content_type');

		$userID = OpenRatings::userID();
		if ($userID) {
			$rating = Rating::where('user_id', '=', $userID)
				->where('content_id', '=', $contentID)
				->where('content_type', '=', $contentType)
				->first();
			if (!empty($rating)) {
				$rating->delete();
				return "Success";
			}
		}
		return "Error";
	}

	public function postGet()
	{
		$contentID   = Input::get('content_id');
		$contentType = Input::get('content_type');
		$ratings     = $rating = Rating::where('content_id', '=', $contentID)->where('content_type', '=', $contentType)->get();
		$ratingsData = OpenRatings::ratingsData($ratings);
		$results = array(
			'pointsUser'    => "UNRATED",
			'pointsAverage' => "UNRATED",
			'ratings'       => $ratingsData['ratingsTotal'],
		);
		if ($ratingsData['ratingsTotal'] > 0) {
			$results['pointsAverage'] = $ratingsData['pointsAverage'];
		}

		$userID = OpenRatings::userID();
		if ($userID) {
			$rating = Rating::where('user_id', '=', $userID)
				->where('content_id', '=', $contentID)
				->where('content_type', '=', $contentType)
				->first();
			if (!empty($rating)) {
				$results['pointsUser'] = $rating->points;
			}
		}
		return json_encode($results);
	}

}