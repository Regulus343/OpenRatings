<div class="rating rating-inactive">
	<h3><?=Lang::get('open-ratings::labels.averageRating')?></h3>

	<div class="stars">
		<?php for ($r = 1; $r <= Config::get('open-ratings::ratingMax'); $r++) { ?>

			<div class="star star<?=$r?>"></div>

		<?php } ?>
	</div><!-- /stars -->
	<div class="clear"></div>

	<div class="tip"></div><!-- /tip -->

	<div class="ratings-number">
		Rated by <a href="" title="View individual member ratings"><strong>0</strong> members</a>
	</div>
</div><!-- /rating -->