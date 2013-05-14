<?php
//$rating  = $materialsItem->getRating();
//$ratings = $materialsItem->getRatingsNumber();

$rating  = 3;
$ratings = 2;
$active  = true;
?>

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

	var ratingLabels   = {{ json_encode(Lang::get('open-ratings::labels')) }};
	var ratingMessages = {{ json_encode(Lang::get('open-ratings::messages')) }};

	@if (!is_null(Site::get('contentID')) && !is_null(Site::get('contentType')))
		var contentID   = "{{ Site::get('contentID') }}";
		var contentType = "{{ Site::get('contentType') }}";
	@else
		if (contentID == undefined)   var contentID   = 0;
		if (contentType == undefined) var contentType = "";
	@endif
</script>

<script type="text/javascript" src="{{ Site::js('ratings', 'regulus/open-ratings') }}"></script>

<div class="rating rating-{{ $active ? 'active' : 'inactive' }}">
	<h3>{{ $active ? 'Your' : 'Member' }} Rating</h3>

	@if ($active)
		<div class="remove-rating{{ !$rating ? ' hidden' : '' }}">x</div>
	@endif

	<div class="stars">
		<div id="star0" class="star zero{{ ($rating != "" && $rating == 0) ? ' rate-hover' : '' }}"></div>

		@for ($r = 1; $r <= Config::get('open-ratings::ratingMax'); $r++)

			<?php if ($active) {
				$class = '';
			} else {
				$class = ' star'.$r;
			}
			if ($rating >= $r) {
				$class .= ' full';
			} else if ($rating >= ($r - 0.5) && $rating < $r) {
				$class .= ' half';
			} ?>

			<div{{ $active ? ' id="star'.$r.'"' : '' }} class="star{{ $class }}"></div>

		@endfor

	</div><!-- /stars -->
	<div class="clear"></div>

	<div class="tip">
		@if ($rating)
			{{ $rating }} out of 5
		@else
			@if ($active)
				Select a rating
			@else
				Unrated
			@endif
		@endif
	</div><!-- /tip -->

	@if ($ratings)
		<div class="ratings-number">
			Rated by <a href="" title="View individual member ratings"><strong>{{ $ratings }}</strong> members</a>
		</div>
	@endif
</div><!-- /rating -->