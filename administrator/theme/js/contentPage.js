$(document).ready(function() {

	// initialize date picker
	$('.datepicker').datepicker();

	// initialize behave
	if($('#page_text') != undefined) {
		var editor = new Behave({
		    textarea: document.getElementById('page_text')
		});
	}

    // initialize dropzone and handle file uploads
    var contentDropzone = new Dropzone("#dropzone");
    Dropzone.autoDiscover = false;
	contentDropzone.on("success", function(file, response) {
		if(response.errors == undefined) {
			insertCodeAtCaret('page_text','\n' + response.data.wiki_code + '\n');
		}
	});

	// handle content editor events ( save / preview / cancel )
	$('#page_submit').click(function() {
		var url = '/sys/pages/add/';
		var data = '';
		data = data + $('#page_editor').serialize();
		data = data + '&' + $('#page_options').serialize();

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function(response) {
				if(response.errors != undefined) {
	            	$('.form-group').removeClass('has-error');
					for(i=0; i<response.errors.fields.length; i++) {
						$('#' + response.errors.fields[i]).parent().parent().addClass('has-error');
					}
					$.pnotify({
						icon: false,
						title: 'Error',
						text: response.errors.general.join('<br/>'),
						type: 'error',
						width: '400px'
						});
	            } else {
	            	$('#page_id').val(response.data[0].page_id);
					$('.form-group').removeClass('has-error');
					$.pnotify({
						icon: false,
						title: 'Success',
						text: 'Page successfully updated.',
						type: 'Success',
						width: '400px'
						});
	            }
			}
		});
		return false;
	});

	// create path (slug) from title
	$('#page_title').blur(function() {
		$.ajax({
			type: 'POST',
			url: '/sys/pages/suggestSlug/',
			data: $(this).serialize(),
			success: function(response) {
				if(response.data.slug != undefined) {
					$('#page_path').val('/' + response.data.slug + '/');
				}
			}
		});
	});

	// handle theme / layout selection
	$('#page_theme').change(function() {
		theme = this.value;
		var url = '/cmd/administrator/admin/getLayouts/?theme=' + theme;
		$.ajax({
			type: 'POST',
			url: url,
			success: function(response) {
				$('#page_layout').empty();
				var layout = $('#page_layout').data('layout');
				for(i=0;i<response.data.length;i++) {
					if(response.data[i].file == layout) {
						$('#page_layout').append('<option value="' + response.data[i].file + '" selected>' + response.data[i].name + '</option>');
					} else {
						$('#page_layout').append('<option value="' + response.data[i].file + '">' + response.data[i].name + '</option>');
					}
				}
				$('#page_layout').select2({minimumResultsForSearch: -1});
			}
		});
	});

	// trigger the events to select the default theme / layout
	$('#page_theme').trigger('change');

});

// insert code at caret
function insertCodeAtCaret(elementID, string) {
	obj=document.getElementById(elementID);
	obj.focus();
	if (typeof(document.selection) != 'undefined') {
	  	var range = document.selection.createRange();
	  	if (range.parentElement() != obj) { return; }
	  	range.text = string;
	  	range.select();
	} else if (typeof(obj.selectionStart) != 'undefined') {
	  	var start = obj.selectionStart;
	  	obj.value = obj.value.substr(0, start) + string + obj.value.substr(obj.selectionEnd, obj.value.length);
	  	start += string.length;
	  	obj.setSelectionRange(start, start);
	} else {
		obj.value += string;
	}
	obj.focus();
}

// insert markup at caret
function insertMarkupAtCaret(ElementID, StartString, PlaceHolder, EndString) {
	obj=document.getElementById(ElementID);
	obj.focus();
	if (typeof(document.selection) != 'undefined') {
	  	var range = document.selection.createRange();
	  	if (range.parentElement() != obj) { return; }
	  	range.text =  StartString + PlaceHolder + EndString;
	  	range.select();
	} else if (typeof(obj.selectionStart) != 'undefined') {
	  	var start = obj.selectionStart;
	  	var SelectedString = obj.value.substring(obj.selectionStart, obj.selectionEnd);
	  	if (SelectedString == '') { SelectedString = PlaceHolder; }
	  	obj.value = obj.value.substr(0, start) + StartString + SelectedString + EndString + obj.value.substr(obj.selectionEnd, obj.value.length);
	  	if(SelectedString == PlaceHolder) {
	  		start += StartString.length;
	  		var end = SelectedString.length;
	  		end += start;
	  		obj.setSelectionRange(start, end);
	  	} else {
			start += StartString.length + SelectedString.length + EndString.length;
			obj.setSelectionRange(start, start);
	  	}
	}
	obj.focus();
}