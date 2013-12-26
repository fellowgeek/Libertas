$(document).ready(function() {

	console.log('Ready.');

	// initialize date picker elements
	$('.datepicker').datepicker();

	// enhance text editor using behave.js
	if($('#contentText') != undefined) {
		var editor = new Behave({
		    textarea: document.getElementById('contentText')
		});
	}

    // file uploads
    var contentDropzone = new Dropzone("#contentDropzone");
    Dropzone.autoDiscover = false;

	contentDropzone.on("success", function(file, response) {
		//console.log('success file:');
		console.log(response);
		if(response.errors == undefined) {
			insertCodeAtCaret('contentText','\n' + response.data.wiki_code + '\n');
		}
	});

	// content
	$('#contentSubmit').click(function() {
		var url = '/sys/pages/add/';
		var data = '';
		data = data + $('#contentForm').serialize();
		data = data + '&' + $('#contentOptionsForm').serialize();

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function(response) {
	            console.log(response);
				if(response.errors != undefined) {
	                $('#adminAlert').text(response.errors.general);
	                $('.alert').addClass('alert-danger');
	                $('.alert').removeClass('alert-success hidden');
	                //$('.alert').addClass('animated fadeIn');
	                setTimeout(function(){
	                    $('.alert').addClass('hidden');
	                }, 4000);
	            } else {
	                $('#adminAlert').text(response.success);
	                $('.alert').addClass('alert-success');
	                $('.alert').removeClass('alert-danger hidden');
	                //$('.alert').addClass('animated fadeIn');
	                setTimeout(function(){
	                    $('.alert').addClass('hidden');
	                }, 4000);
	            }
			}
		});
		return false;
	});

});

// insert code in textarea at caret
function insertCodeAtCaret(elementID, string)
	{
	obj=document.getElementById(elementID);
	obj.focus();
	if (typeof(document.selection) != 'undefined')
		{
	  	var range = document.selection.createRange();
	  	if (range.parentElement() != obj)
	    	return;
	  	range.text = string;
	  	range.select();
		}
		else if (typeof(obj.selectionStart) != 'undefined')	{
	  	var start = obj.selectionStart;
	  	obj.value = obj.value.substr(0, start)
	    	      + string
	        	  + obj.value.substr(obj.selectionEnd, obj.value.length);
	  	start += string.length;
	  	obj.setSelectionRange(start, start);
		}
		else
	   		obj.value += string;
	obj.focus();
	}

// insert markup in textarea at caret
function insertMarkupAtCaret(ElementID, StartString, PlaceHolder, EndString)
	{
	obj=document.getElementById(ElementID);
	obj.focus();
	if (typeof(document.selection) != 'undefined')
		{
	  	var range = document.selection.createRange();
	  	if (range.parentElement() != obj)
	    	return;
	  	range.text =  StartString + PlaceHolder + EndString;
	  	range.select();
		}
		else if (typeof(obj.selectionStart) != 'undefined')
		{
	  	var start = obj.selectionStart;
	  	var SelectedString = obj.value.substring(obj.selectionStart, obj.selectionEnd);
	  	if (SelectedString == '')
			{
			SelectedString = PlaceHolder;
			}

	  	obj.value = obj.value.substr(0, start) + StartString + SelectedString + EndString + obj.value.substr(obj.selectionEnd, obj.value.length);

	  	if(SelectedString == PlaceHolder)
	  		{
	  		start += StartString.length;
	  		var end = SelectedString.length;
	  		end += start;
	  		obj.setSelectionRange(start, end);
	  		}
	  	else
	  		{
			start += StartString.length + SelectedString.length + EndString.length;
			obj.setSelectionRange(start, start);
	  		}
		}
	obj.focus();
	}