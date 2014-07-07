$(document).ready(function() {

	// center modal dialog
	$('#Modal').on('show.bs.modal', function () {
		$('.modal-content').css('visibility','hidden');
	});

	$('#Modal').on('shown.bs.modal', function () {
		if($(window).height() > $('.modal-content').height()) {
			var top = ($(window).height() / 2) - ($('.modal-content').height()/2);
			top = top + $(window)[0].scrollY;
			$('.modal-content').offset({top: top});
		} else {
			$('.modal-content').offset({top: 30});
		}
		$('.modal-content').css({'display':'none','visibility':'visible'});
		$('.modal-content').fadeIn(500);
	});

	/*
	$('#ModalTitle').html('Lorm Ipsum');
	$('#ModalBody').html('<img src="/files/image07.jpg" class="img-responsive"/><p>Irony Cosby sweater shabby chic officia, occupy flannel nulla  eiusmod squid pug esse  adipisicing blog elit Tonx.</p>');
	$('#ModalFooter').html('<button type="button" class="btn btn-default small-button" data-dismiss="modal">Close</button><a class="btn btn-primary small-button">Lorem</a>');

	setTimeout(function(){
		$('#Modal').modal('show');
	}, 2000)
	*/

});