$(document).ready(function() {

	console.log('Content Files');

	// initalize masonary
	var $container = $('#contentFiles');

	$container.imagesLoaded(function() {
		$container.masonry({
		isAnimated: true,
		isFitWidth: true,
		animationOptions: {
			duration: 250,
			easing: 'linear',
			queue: false
		},
		itemSelector : '.file',
		columnWidth : 220,

		});
	});


});