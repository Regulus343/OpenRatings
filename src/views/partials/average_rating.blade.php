<div class="rating rating-inactive">
	<h3>{{{ Lang::get('open-ratings::labels.averageRating') }}}</h3>

	<div class="stars">
		@for ($r = 1; $r <= Config::get('open-ratings::ratingMax'); $r++)

			<?php $class = "";
			if (!isset($rating)) $rating = "UNRATED";
			if ($rating != "UNRATED") {
				if ($rating >= $r) {
					$class .= 'full';
				} else if ($rating >= ($r - 0.5) && $rating < $r) {
					$class .= 'half';
				}
			} ?>

			<div class="star star{{ $r }} {{ $class }}"></div>

		@endfor
	</div><!-- /stars -->
	<div class="clear"></div>

	<div class="tip">
		{{{ $rating != "UNRATED" ? Lang::get('open-ratings::labels.currentRating', array('current' => $rating, 'max' => Config::get('open-ratings::ratingMax'))) : Lang::get('open-ratings::messages.unrated') }}}
	</div><!-- /tip -->

	<div class="ratings-number">
		Rated by <a href="" title="View individual member ratings"><strong>{{{ isset($ratings) ? $ratings : 0 }}}</strong> members</a>
	</div>
</div><!-- /rating -->