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
			var currentRatingText = ratingLabels.currentRating.replace(':current', points).replace(':max', ratingMax);
			if (results.resultType == "Success") {
				$('.rating-active .tip').html(currentRatingText);
				$('.rating-active .remove-rating').fadeIn('fast');
				rating = points;
				getRating(contentID);
			} else {
				if (rating == "") {
					$('.rating-active .tip').html('Select a rating');
					$('.rating-active .remove-rating').fadeOut('fast');
				} else {
					$('.rating-active .tip').html(currentRatingText);
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
	$('.rating-active .tip').html(ratingMessages.removingRating);

	$.ajax({
		url: baseURL + 'ratings/remove',
		type: 'post',
		data: { 'content_id': contentID, 'content_type': contentType },
		success: function(result){
			savingRating = false;
			if (result == "Success") {
				rating = "";
				$('.rating-active .star').removeClass('rate-hover').removeClass('rate-hover-higher').removeClass('full').removeClass('half');
			}
			getRating(contentID);
		},
		error: function(){
			savingRating = false;
			console.log('Remove Rating Failed');
		}
	});
}

function getRating(id) {
	$.ajax({
		url: baseURL + 'ratings/get',
		type: 'post',
		data: { 'content_id': id, 'content_type': contentType },
		dataType: 'json',
		success: function(results){
			if (results.pointsUser != "UNRATED") {
				rating = results.pointsUser;
			} else {
				rating = "";
			}

			if (results.pointsAverage != "UNRATED") {
				ratingAverage = results.pointsAverage;
			} else {
				ratingAverage = "";
			}

			displayRatings();
			if (results.ratings != "") $('.rating .ratings-number strong').html(results.ratings);
		}
	});
}

function displayRatings() {
	//set user rating
	console.log(rating);
	if (rating == 0 && rating != "") {
		$('.rating-active #star0').removeClass('rate-hover-higher').addClass('rate-hover');
	} else {
		$('.rating-active #star0').removeClass('rate-hover').addClass('rate-hover-higher');
	}
	for (r=1; r <= ratingMax; r++) {
		if (rating >= r) {
			$('.rating-active #star'+r).removeClass('rate-hover-higher').addClass('rate-hover');
		} else {
			$('.rating-active #star'+r).removeClass('rate-hover').addClass('rate-hover-higher');
		}
	}

	if (rating === "") {
		$('.rating-active .tip').html(ratingMessages.selectRating);
		$('.rating-active .remove-rating').fadeOut('fast');
	} else {
		var currentRatingText = ratingLabels.currentRating.replace(':current', rating).replace(':max', ratingMax);
		$('.rating-active .tip').html(currentRatingText);
	}

	//set average member rating
	for (r=1; r <= ratingMax; r++) {
		if (ratingAverage >= r) {
			$('.rating-inactive .star'+r).removeClass('half').addClass('full');
		} else if (ratingAverage >= (r - 0.5) && ratingAverage < r) {
			$('.rating-inactive .star'+r).removeClass('full').addClass('half');
		} else {
			$('.rating-inactive .star'+r).removeClass('full').removeClass('half');
		}
	}

	if (ratingAverage === "") {
		$('.rating-inactive .tip').html(ratingMessages.unrated);
	} else {
		var currentRatingText = ratingLabels.currentRating.replace(':current', ratingAverage).replace(':max', ratingMax);
		$('.rating-inactive .tip').html(currentRatingText);
	}

}

$(document).ready(function(){

	//get current rating for selected content
	if (contentID) getRating(contentID);

	$('.rating-active div.star').hover(function(){
		if (!savingRating) {
			var ratingPoints = parseInt($(this).attr('id').replace('star', ''));
			for (r=1; r <= ratingMax; r++) {
				if (ratingPoints >= r) {
					$('.rating-active #star'+r).removeClass('rate-hover-higher').addClass('rate-hover');
				} else {
					$('.rating-active #star'+r).removeClass('rate-hover').addClass('rate-hover-higher');
				}
			}
			$('.rating-active .tip').html('Rate <strong>'+ratingPoints+' out of '+ratingMax+'</strong>');
		}
	}).click(function(){
		var ratingPoints = parseInt($(this).attr('id').replace('star', ''));
		saveRating(ratingPoints);
	}).mouseleave(function(){
		if (!savingRating) {
			displayRatings();
		}
	});

	$('.rating-active .remove-rating').click(function(){
		removeRating();
	});

});