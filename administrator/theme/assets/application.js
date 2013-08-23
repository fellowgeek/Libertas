// task to be done when page ready
$(document).ready(function() {
    // init jquery masonry layout
    init_masonry();

    $(".datepicker").datepicker();
    $(".uniform_on").uniform();
});

// enhance text editor using behave.js
if(document.getElementById('pageText') != undefined) {
	var editor = new Behave({
	    textarea: document.getElementById('pageText')
	});
}

// display media items using masonry
var $container = $('.media-content');

var gutter = 15;
var min_width = 150;
var containerWidth = $container.width();
var reArrange = true;

function getAdjustedWidth(containerWidth) {
	var num_of_boxes = (containerWidth/min_width | 0);
	var box_width = (((containerWidth - (num_of_boxes-1)*gutter)/num_of_boxes) | 0);
	if(containerWidth < min_width) {
		box_width = containerWidth;
	}
	$('.item').width(box_width);
	return box_width;
}

function setReArrange() {
	reArrange = true;
	$container.masonry('option', {
		columnWidth: getAdjustedWidth(containerWidth)
	});
}

$(window).resize(function() {
	containerWidth = $container.width();
	if(reArrange == true) {
		$container.masonry('option', {
			columnWidth: getAdjustedWidth(containerWidth)
		});
		reArrange = false;
		setTimeout('setReArrange();',100);
	}
});

function init_masonry() {
    $container.imagesLoaded( function() {
        $container.masonry({
            itemSelector : '.item',
            gutter: 15,
			isResizable: true,
            isAnimated: true,
			columnWidth: getAdjustedWidth(containerWidth)
        });

    });

}
