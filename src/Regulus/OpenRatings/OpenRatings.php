<?php namespace Regulus\OpenRatings;

/*----------------------------------------------------------------------------------------------------------
	OpenRatings
		A light, effective user ratings composer package that is easy to configure and implement.

		created by Cody Jassman
		last updated on May 15, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class OpenRatings {

	public static $auth;

	/**
	 * Authenticates users for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function auth()
	{
		$auth = static::configAuth();
		if ($auth->methodActiveCheck != false) {
			$function = static::separateFunction($auth->methodActiveCheck);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Authenticates admin for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function admin()
	{
		$auth = static::configAuth();
		if ($auth->methodAdminCheck) {
			if (static::auth()) {
				$user = static::user();
				if ($user->roles[0]->role == $auth->methodAdminRole) return true;
			}
		}
		return false;
	}

	/**
	 * Gets the active user.
	 *
	 * @return boolean
	 */
	public static function user()
	{
		$auth = static::configAuth();
		if ($auth->methodActiveUser != false) {
			$function = static::separateFunction($auth->methodActiveUser);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Gets the active user ID.
	 *
	 * @return boolean
	 */
	public static function userID()
	{
		$auth = static::configAuth();
		$user = static::user();

		if (isset($user->{$auth->methodActiveUserID}))
			return $user->{$auth->methodActiveUserID};

		return false;
	}

	/**
	 * Prepare authorization configuration.
	 *
	 * @return array
	 */
	private static function configAuth()
	{
		if (is_null(static::$auth)) {
			static::$auth = (object) array(
				'class'              => Config::get('open-ratings::authClass'),
				'methodActiveCheck'  => Config::get('open-ratings::authMethodActiveCheck'),
				'methodActiveUser'   => Config::get('open-ratings::authMethodActiveUser'),
				'methodActiveUserID' => Config::get('open-ratings::authMethodActiveUserID'),
				'methodAdminCheck'   => Config::get('open-ratings::authMethodAdminCheck'),
				'methodAdminRole'    => Config::get('open-ratings::authMethodAdminRole'),
			);
		}
		return static::$auth;
	}

	/**
	 * Get the current average rating, the total number of points, and the total number of ratings for a content items.
	 *
	 * @return array
	 */
	public static function ratingsData($ratings)
	{
		$results = array(
			'pointsTotal'   => 0,
			'ratingsTotal'  => 0,
			'pointsAverage' => 0,
		);
		foreach ($ratings as $rating) {
			$results['pointsTotal']  += $rating->points;
			$results['ratingsTotal'] ++;
		}
		if ($results['ratingsTotal'] > 0) {
			$results['pointsAverage'] = number_format($results['pointsTotal'] / $results['ratingsTotal'], Config::get('open-ratings::ratingDecimals'), '.', '');
		}
		return $results;
	}

	/**
	 * Get the HTML text for a rating for use in lists of content items.
	 *
	 * @param  mixed    $rating
	 * @param  integer  $ratings
	 * @return string
	 */
	public static function listedRatingHTML($rating = 'UNRATED', $ratings = 0)
	{
		if (!$ratings) $rating = "UNRATED";
		return View::make(Config::get('open-ratings::viewsLocation').'partials.average_rating')
			->with('rating', $rating)
			->with('ratings', $ratings)
			->render();
	}

	/**
	 * Separates a function string "function('array')" into the
	 * function name and the parameters for use with call_user_func.
	 *
	 * @param  string   $function
	 * @return object
	 */
	public static function separateFunction($function)
	{
		$data = preg_match('/([\w\_\d]+)\(([\w\W]*)\)/', $function, $matches);
		if (!isset($matches[0])) $matches[0] = $function;
		if (!isset($matches[1])) $matches[1] = str_replace('()', '', $function);
		if (!isset($matches[2])) $matches[2] = null;
		return (object) array(
			'method'     => $matches[1],
			'parameters' => str_replace("'", '', $matches[2]),
		);
	}

	/**
	 * Calls a function using call_user_func and call_user_func array.
	 *
	 * @param  object   $function
	 * @return boolean
	 */
	public static function callFunction($function)
	{
		if (!isset($function->method) OR !isset($function->parameters)) return false;

		$auth = static::configAuth();
		if (substr($function->parameters, 0, 6) == "array(") {

			$function->parameters = explode(',', $function->parameters);
			for ($p = 0; $p < count($function->parameters); $p++) {
				$function->parameters[$p] = str_replace("'", '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace('array(', '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace(')', '', $function->parameters[$p]);
			}

			return call_user_func_array($auth->class.'::'.$function->method, $function->parameters);
		} else {
			return call_user_func($auth->class.'::'.$function->method, $function->parameters);
		}
	}

}