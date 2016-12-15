//Method to calculate weight difference between syringes
function checkField(){ 
	if(document.getElementById("weight_full_syringe") && document.getElementById("weight_empty_syringe")){
		var weight_full_syringe = document.getElementById("weight_full_syringe").value;
		var weight_empty_syringe = document.getElementById("weight_empty_syringe").value;
	
		weight_full_syringe = weight_full_syringe.replace(',', '.')
		weight_empty_syringe = weight_empty_syringe.replace(',', '.')
		
		var weight_full_syringe_number = +weight_full_syringe
		var weight_empty_syringe_number = +weight_empty_syringe
		//alert("The input value has changed. The new value is: " + weight_empty_syringe);
		
		var net_weight = weight_full_syringe_number-weight_empty_syringe_number;
		net_weight = net_weight.toFixed(4);
		document.getElementById("net_weight_syringe").innerHTML = net_weight + " g";
	}
}
