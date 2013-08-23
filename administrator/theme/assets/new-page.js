// task to be done when page ready
$(document).ready(function() {

    $(".datepicker").datepicker();
    $(".uniform_on").uniform();
});

// enhance text editor using behave.js
if(document.getElementById('pageText') != undefined) {
	var editor = new Behave({
	    textarea: document.getElementById('pageText')
	});
}

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
		else if (typeof(obj.selectionStart) != 'undefined')
		{
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