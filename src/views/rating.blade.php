{{-- Setup (JS & CSS) --}}
@include(Config::get('open-ratings::viewsLocation').'partials.setup')

<div class="ratings-container">
	{{-- Average Member Rating --}}
	@include(Config::get('open-ratings::viewsLocation').'partials.average_rating')

	{{-- User Rating --}}
	@include(Config::get('open-ratings::viewsLocation').'partials.user_rating')
</div>