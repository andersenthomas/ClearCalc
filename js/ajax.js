function showData(str){
	if(str==""){
		document.getElementById("rackdata").innerHTML="";
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
			document.getElementById("rackdata").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","getdata.php?date=" + str, true);
	xmlhttp.send();
}
