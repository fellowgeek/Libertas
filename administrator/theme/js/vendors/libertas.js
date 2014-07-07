(function ($) {
	$.extend({
		sysModal: function(options) {

	        var options = $.extend({
	            title: '',
	            body: '',
	            top: '0px',
				width: '600px',
				centered: false,
				buttonConfirmEnabled: true,
	            buttonConfirm: 'Confirm',
	            buttonConfirmClasses: 'btn-primary',
	            buttonCloseEnabled: true,
	            buttonClose: 'Close',
	            buttonCloseClasses: 'btn-default',
	            onConfirm : function(){},
	            onClose: function(){}
	        }, options);

			var html = '';
			html += '<div class="modal fade" id="appModal" tabindex="-1" role="dialog">';
			html += '	<div class="modal-dialog">';
			html += '		<div class="modal-content">';
			html += '			<div class="modal-header" id="appModalHeader">';
			html += '				<button type="button" class="close appModalButtonClose" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '				<h4 class="modal-title">' + options.title + '</h4>';
			html += '			</div>';
			html += '			<div class="modal-body" id="appModalBody">' + options.body + '</div>';
			if(options.buttonCloseEnabled == true || options.buttonConfirmEnabled == true) {
				html += '<div class="modal-footer" id="appModalFooter">';
				if(options.buttonCloseEnabled == true) {
					html += '<button type="button" class="btn ' + options.buttonCloseClasses + ' appModalButtonClose" data-dismiss="modal">' + options.buttonClose + '</button>';
				}
				if(options.buttonConfirmEnabled == true) {
					html += '<button type="button" class="btn ' + options.buttonConfirmClasses + '" id="appModalButtonConfirm">' + options.buttonConfirm + '</button>';
				}
				html += '</div>';
			}
			html += '		</div>';
			html += '	</div>';
			html += '</div>';

			$('body').append(html);

			$('#appModal .modal-content').css('margin-top', options.top);
			$('#appModal .modal-dialog').css('width', options.width);

			$('#appModalButtonConfirm').on('click', function(){
				options.onConfirm();
				$('#appModal').modal('hide');
			});

			$('.appModalButtonClose').on('click', function(){
				options.onClose();
				$('#appModal').modal('hide');
			});

			if(options.centered == true) {
				$('#appModal').on('show.bs.modal', function () {
					$('#appModal .modal-content').css('visibility','hidden');
				});

				$('#appModal').on('shown.bs.modal', function () {
					if($(window).height() > $('#appModal .modal-content').height()) {
						var top = ($(window).height() / 2) - ($('#appModal .modal-content').height()/2);
						top = top + $(window)[0].scrollY;
						$('#appModal .modal-content').offset({top: top});
					} else {
						$('#appModal .modal-content').offset({top: 30});
					}
					$('#appModal .modal-content').css({'display':'none','visibility':'visible'});
					$('#appModal .modal-content').fadeIn(500);
				});
			}

			$('#appModal').on('hidden.bs.modal', function () { $('#appModal').remove(); });
			$('#appModal').modal('show');
	    }
	})
}(jQuery));
