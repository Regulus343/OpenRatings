<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Views Location
	|--------------------------------------------------------------------------
	|
	| The location of your ratings views. It is defaulted to "open-ratings::"
	| to use OpenForum's built-in views, but you may point it towards a views
	| directory of your own for full view customization.
	|
	*/
	'viewsLocation' => 'open-ratings::',

	/*
	|--------------------------------------------------------------------------
	| Authorization Class
	|--------------------------------------------------------------------------
	|
	| The name of your authorization class including the namespace and a
	| leading backslash. This variable along with the "authMethod" variables
	| allow OpenForum's built-in views to remain authoriztion class agnostic.
	| The default is "\Illuminate\Support\Facades\Auth" which is Laravel 4's
	| native authorization class.
	|
	*/
	'authClass' => '\Illuminate\Support\Facades\Auth',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - Authentication Check
	|--------------------------------------------------------------------------
	|
	| The method in your authorization class that checks if user is logged in.
	| The default is "check()" which, along with the default auth class above,
	| selects Laravel 4's native authentication method.
	|
	*/
	'authMethodActiveCheck' => 'check()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - User
	|--------------------------------------------------------------------------
	|
	| The method for getting the active user.
	|
	*/
	'authMethodActiveUser' => 'user()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - User ID
	|--------------------------------------------------------------------------
	|
	| The attribute for getting the active user ID which is used in conjunction
	| with the user method above. By default, they get "user()->id" together.
	|
	*/
	'authMethodActiveUserID' => 'id',

	/*
	|--------------------------------------------------------------------------
	| Authorization - Roles
	|--------------------------------------------------------------------------
	|
	| Whether user model has roles available.
	|
	*/
	'authMethodAdminCheck' => false,

	/*
	|--------------------------------------------------------------------------
	| Authorization - Admin Role
	|--------------------------------------------------------------------------
	|
	| The name of the admin role if admin check is enabled.
	|
	*/
	'authMethodAdminRole' => 'admin',

	/*
	|--------------------------------------------------------------------------
	| Allowed Content Types and Corresponding Tables
	|--------------------------------------------------------------------------
	|
	| It is recommended that you declare a list of allowed content types with
	| their corresponding tables to prevent users from getting invalid
	| ratings in your database. In the below example, "BlogArticle" is the
	| content type and "blog_articles" is the database table:
	|
	|     array('BlogArticle' => 'blog_articles')
	|
	*/
	'allowedContentTypes' => false,

	/*
	|--------------------------------------------------------------------------
	| Set Comments Totals For Objects
	|--------------------------------------------------------------------------
	|
	| If true, this will save the current rating to a "rating" field in the
	| content type / table pairs declared in Allowed Content Types above.
	|
	*/
	'setContentRating' => false,

	/*
	|--------------------------------------------------------------------------
	| Maximum Rating
	|--------------------------------------------------------------------------
	|
	| The range of ratings within which a user may rate a content item. The
	| default is 5, allowing for ratings of 0, 1, 2, 3, 4, and 5.
	|
	*/
	'ratingMax' => 5,

	/*
	|--------------------------------------------------------------------------
	| Average Rating Decimals
	|--------------------------------------------------------------------------
	|
	| The range of ratings within which a user may rate a content item. The
	| default is 5, allowing for ratings of 0, 1, 2, 3, 4, and 5.
	|
	*/
	'ratingDecimals' => 1,

	/*
	|--------------------------------------------------------------------------
	| Load jQuery
	|--------------------------------------------------------------------------
	|
	| Whether or not to have Open Comments automatically load jQuery.
	| Turn this off if your website already loads jQuery.
	|
	*/
	'loadJquery' => true,

	/*
	|--------------------------------------------------------------------------
	| Load Bootstrap
	|--------------------------------------------------------------------------
	|
	| Whether or not to have Open Comments automatically load Twitter Bootsrap.
	| If set to false, Open Comments will assume you are already loading
	| Bootstrap CSS and JS files. If true, Open Comments will attempt to load
	| "bootstrap.css" and "bootstrap.min.js".
	|
	*/
	'loadBootstrap' => true,

	/*
	|--------------------------------------------------------------------------
	| Load Boxy
	|--------------------------------------------------------------------------
	|
	| By default, Open Comments makes use of the lightweight javascript
	| library Boxy for modal windows like comment deleting confirmation.
	| You may turn off Boxy if you intend to use another modal window script.
	|
	*/
	'loadBoxy' => true,

);