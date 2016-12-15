// Method to toogle lists when clicking on links
function toggle(list){ 
	var listElementStyle=document.getElementById(list).style;
	if (listElementStyle.display=="none"){ 
		listElementStyle.display="block"; 
	} else {
		listElementStyle.display="none"; 
	} 
}

function toogle_checkbox(source){
	checkboxes = document.getElementsByName('patient_samples[]');
	for(var i=0, n=checkboxes.length; i<n; i++){
		checkboxes[i].checked = source.checked;
	}
}
