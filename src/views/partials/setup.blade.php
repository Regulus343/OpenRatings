{{-- Load jQuery --}}
@if (Config::get('open-ratings::loadJquery'))

	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>

@endif

{{-- Load Bootstrap CSS & JS --}}
@if (Config::get('open-ratings::loadBootstrap'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('bootstrap', 'regulus/open-ratings') }}" />
	<script type="text/javascript" src="{{ Site::js('bootstrap.min', 'regulus/open-ratings') }}"></script>

@endif

{{-- Load Boxy --}}
@if (Config::get('open-ratings::loadBoxy'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('boxy', 'regulus/open-ratings') }}" />
	<script type="text/javascript" src="{{ Site::js('jquery.boxy', 'regulus/open-ratings') }}"></script>

@endif

{{-- Ratings CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('ratings', 'regulus/open-ratings') }}" />

{{-- Ratings JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = "{{ URL::to('') }}";

	var rating         = 0;
	var ratingAverage  = 0;
	var ratingLabels   = {{ json_encode(Lang::get('open-ratings::labels')) }};
	var ratingMessages = {{ json_encode(Lang::get('open-ratings::messages')) }};

	@if (!is_null(Site::get('contentID')) && !is_null(Site::get('contentType')))
		var contentID   = "{{ Site::get('contentID') }}";
		var contentType = "{{ Site::get('contentType') }}";
	@else
		if (contentID == undefined)   var contentID   = 0;
		if (contentType == undefined) var contentType = "";
	@endif

	var ratingMax = {{ Config::get('open-ratings::ratingMax') }};

</script>

<script type="text/javascript" src="{{ Site::js('ratings', 'regulus/open-ratings') }}"></script>