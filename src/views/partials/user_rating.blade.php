@if (Regulus\OpenRatings\OpenRatings::auth())
	<div class="rating rating-active">
		<h3>{{{ Lang::get('open-ratings::labels.yourRating') }}}</h3>

		<div class="remove-rating">x</div>

		<div class="stars">
			<div id="star0" class="star zero"></div>

			@for ($r = 1; $r <= Config::get('open-ratings::ratingMax'); $r++)

				<div id="star{{ $r }}" class="star"></div>

			@endfor
		</div><!-- /stars -->
		<div class="clear"></div>

		<div class="tip"></div><!-- /tip -->
	</div><!-- /rating -->
@endif