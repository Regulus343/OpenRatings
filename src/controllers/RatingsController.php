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

}