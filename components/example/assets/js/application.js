
$(document).ready(function() {

	console.log('Hello World.');


	$('body').addClass('animated shake');


	setTimeout(function(){
		$('.flapjack').removeClass('animated bounceInLeft');
		$('.flapjack').addClass('animated flash bounce shake tada swing wobble pulse');
	}, 2000);


	setTimeout(function(){
		$('.flapjack').removeClass('animated flash bounce shake tada swing wobble pulse');
		$('.flapjack').addClass('animated wobble');
	}, 4000);


	setTimeout(function(){
		$('.flapjack').removeClass('animated wobble');
		$('.flapjack').addClass('animated bounce');
	}, 6000);

	setTimeout(function(){
		$('.flapjack').removeClass('animated bounce');
		$('.flapjack').addClass('animated shake');
	}, 8000);


	setTimeout(function(){
		$('.flapjack').removeClass('animated shake');
		$('.flapjack').addClass('animated hinge');
	}, 10000);


});