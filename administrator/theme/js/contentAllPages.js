$(document).ready(function() {

	// delete page
	$('.contentDeletePage').click(function() {
		var page = $(this).data('page');

		$.sysModal({
			title: 'Delete',
			body: '<p>Do you want to <u>permenantly</u> delete this page?</p>',
			width: '600px',
			buttonConfirm: 'Delete',
			buttonConfirmClasses: 'btn-danger small-button',
			buttonClose: 'Close',
			buttonCloseClasses: 'btn-default small-button',
			onConfirm: function() {
				$.ajax({
					url: '/sys/pages/delete/?page_id=' + page,
					success: function(response) {
						$('#Modal').modal('hide');
						window.location.reload();
					}
				});
			}
		});
	});

	// preview page
	$('.contentPreviewPage').click(function() {
		page = $(this).data('page');

		$.ajax({
			url: '/sys/pages/getParsed/?page_id=' + page,
			success: function(response) {
				title = response.data[0].page_title;
				content = response.data[0].parsed;

				$.sysModal({
					title: title,
					body: content,
					width: '1000px',
					centered: true,
					buttonConfirmEnabled: false,
					buttonCloseClasses: 'btn-default small-button',
				});
			}
		});
	});

});