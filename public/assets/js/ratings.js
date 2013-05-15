function saveRating(points) {
	if (points == 0) {
		$('.rating-active #star0').addClass('rate-hover');
	} else {
		$('.rating-active #star0').removeClass('rate-hover');
	}
	for (r=1; r <= 5; r++) {
		if (points >= r) {
			$('.rating-active #star'+r).removeClass('rate-hover-higher').addClass('rate-hover');
		} else {
			$('.rating-active #star'+r).removeClass('rate-hover').addClass('rate-hover-higher');
		}
	}
	$('.rating-active .tip').html(ratingMessages.savingRating);
	savingRating = true;

	$.ajax({
		url: baseURL + 'ratings/save',
		type: 'post',
		data: { 'content_id': contentID, 'content_type': contentType, 'points': points },
		dataType: 'json',
		success: function(results){
			if (results.resultType == "Success") {
				$('.rating-active .tip').html(points+' out of 5');
				$('.rating-active .remove-rating').fadeIn('fast');
				rating = points;
				reloadMemberRating();
			} else {
				if (rating == "") {
					$('.rating-active .tip').html('Select a rating');
					$('.rating-active .remove-rating').fadeOut('fast');
				} else {
					$('.rating-active .tip').html(rating+' out of 5');
				}
			}
			savingRating = false;
		},
		error: function(){
			$('.rating-active .tip').html(ratingMessages.selectRating);
			$('.rating-active .remove-rating').fadeOut('fast');
			savingRating = false;
			console.log('Save Rating Failed');
		}
	});
}

function removeRating() {
	$('.rating-active .star').removeClass('rate-hover').removeClass('rate-hover-higher').removeClass('full').removeClass('half');
	$('.rating-active .tip').html(ratingMessages.removingRating);

	$.ajax({
		url: baseURL + 'ratings/remove',
		type: 'post',
		data: { 'content_id': contentID, 'content_type': contentType },
		success: function(data){
			if (data == "Success") {
				$('.rating-active .tip').html('Select a rating');
				$('.rating-active .remove-rating').fadeOut('fast');
				rating = "";
				reloadMemberRating();
			} else {
				if (rating == "") {
					$('.rating-active .tip').html('Select a rating');
					$('.rating-active .remove-rating').fadeOut('fast');
				} else {
					$('.rating-active .tip').html(rating+' out of 5');
				}
			}
		},
		error: function(){
			$('.rating-active .tip').html('Select a rating');
			$('.rating-active .remove-rating').fadeOut('fast');
			savingRating = false;
			console.log('Remove Rating Failed');
		}
	});
}

function getUserRating(id) {
	$.ajax({
		url: baseURL + 'ratings/user',
		type: 'post',
		data: { 'content_id': id, 'content_type': contentType },
		dataType: 'json',
		success: function(results){
			if (results.points != "UNRATED") {
				rating = results.points;
				$('.rating .tip').html(results.points+' out of 5');
				for (r=1; r <= 5; r++) {
					if (rating >= r) {
						$('.rating .star'+r).removeClass('half').addClass('full');
					} else if (rating >= (r - 0.5) && results.points < r) {
						$('.rating .star'+r).removeClass('full').addClass('half');
					} else {
						$('.rating .star'+r).removeClass('half').removeClass('full');
					}
				}
			} else {
				rating = "";
				$('.rating .tip').html('Unrated');
				for (r=1; r <= 5; r++) {
					$('.rating .star'+r).removeClass('half').removeClass('full');
				}
			}
			if (results.ratings != "") $('.rating .ratings-number strong').html(results.ratings);
		}
	});
}

$(document).ready(function(){

	//get current user rating for selected content
	if (contentID) getUserRating(contentID);

	$('.rating-active div.star').hover(function(){
		if (!savingRating) {
			var ratingPoints = parseInt($(this).attr('id').replace('star', ''));
			for (r=1; r <= 5; r++) {
				if (ratingPoints >= r) {
					$('.rating-active #star'+r).removeClass('rate-hover-higher').addClass('rate-hover');
				} else {
					$('.rating-active #star'+r).removeClass('rate-hover').addClass('rate-hover-higher');
				}
			}
			$('.rating-active .tip').html('Rate <strong>'+ratingPoints+' out of 5</strong>');
		}
	}).click(function(){
		var ratingPoints = parseInt($(this).attr('id').replace('star', ''));
		saveRating(ratingPoints);
	}).mouseleave(function(){
		if (!savingRating) {
			for (r=1; r <= 5; r++) {
				if (rating >= r) {
					$('.rating-active #star'+r).removeClass('rate-hover-higher').addClass('rate-hover');
				} else {
					$('.rating-active #star'+r).removeClass('rate-hover').addClass('rate-hover-higher');
				}
			}
			if (rating === "") {
				$('.rating-active .tip').html('Select a rating');
			} else {
				$('.rating-active .tip').html(rating+' out of 5');
			}
		}
	});

	$('.rating-active .remove-rating').click(function(){
		removeRating();
	});

});