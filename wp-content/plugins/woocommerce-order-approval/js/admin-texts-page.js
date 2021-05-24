jQuery(document).ready(function()
{
	"use strict";
	jQuery(document).on('click', '.tablinks', wcoa_manage_tab);
	jQuery('#'+jQuery('#active_tab_ref').val()+"_button").trigger('click');
	
});

function wcoa_manage_tab(evt) 
{
	evt.preventDefault();
	const tab_name = jQuery(evt.currentTarget).data('tab');
	jQuery('#active_tab_ref').val(tab_name)
	var i, tabcontent, tablinks;

	// Get all elements with class="tabcontent" and hide them
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
	tabcontent[i].style.display = "none";
	}

	// Get all elements with class="tablinks" and remove the class "active"
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
	tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	// Show the current tab, and add an "active" class to the button that opened the tab
	document.getElementById(tab_name).style.display = "block";
	evt.currentTarget.className += " active";
	
	return false;
}