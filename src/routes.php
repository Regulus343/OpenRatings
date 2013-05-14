<?php namespace Regulus\OpenRatings;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

/* Map Controller */
Route::controller('ratings', 'RatingsController');

/* Set Ajax Filter */
Route::when('ratings/*', 'ajax');