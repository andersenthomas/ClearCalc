<?php

//return a single value from the database
function single_sql_value($con,$query,$column){
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);
    $value = $row[$column];
    mysqli_free_result($result);
    return($value);
}

//return the current data. $format is type string (eg. '%Y')
function get_current_date($format){
    $current_date = date($format);
    return($current_date);
}

function subtract_dates($datein1, $datein2){
	$date1 = date_create($datein1);
	$date2 = date_create($datein2);
	$diff = date_diff($date1,$date2);
	$diff_formatted = $diff->format("%R%a");
	return($diff_formatted);
}

function datesub($datein1, $datein2){
	$seconds = strtotime($datein2) - strtotime($datein1);
	$minutes = $seconds/60.0;
	return($minutes);
}

//add or subtract two times. $sub_or_add takes '+' or '-' as arguments
function add_time($start_time,$time_to_add, $format, $sub_or_add='+'){
    $start_time_datetime = date_create($start_time);
    if($sub_or_add == '+'){        
        $added_time = date_add($start_time_datetime, date_interval_create_from_date_string($time_to_add));
    } elseif ($sub_or_add == '-'){
        $added_time = date_sub($start_time_datetime, date_interval_create_from_date_string($time_to_add));
    } else {
        echo 'NaN';
    }
    $added_time_formatted = date_format($added_time, $format);
    return($added_time_formatted);
}


//calculate persons age. $format is type string (eg. '%Y')
function calc_age($birthday, $exam_date, $format){
    if(!$birthday == ''){
        //$now = date_create(date('Y-m-d'));
        $age = date_diff(date_create($birthday), date_create($exam_date));
        $age_formatted = $age->format($format);
    } else {
        $age_formatted = 'NaN';
    }
    return((int)$age_formatted);
}


//normalize standard counts to cpm/ml
function standard_counts($standard_counts,$background_counts,$standard_volume,$count_period){
    $standard_counts_corr = (($standard_counts-$background_counts)*60)/($count_period*$standard_volume);
    return($standard_counts_corr);
}

//normalize measured counts to cpm/ml
function counts_per_ml($counts,$background,$sample_volume,$counting_time){
    $counts_normalized = (($counts-$background)*60)/($sample_volume*$counting_time);
    return($counts_normalized);
}

//calculate the dose in cpm
function calc_dose($standard_counts,$standard_dilution,$standard_weight,$weight_full_syringe,$weight_empty_syringe){
    $dose = $standard_counts*($standard_dilution/$standard_weight)*($weight_full_syringe-$weight_empty_syringe);
    return($dose);
}

//return the average between two numbers
function calc_average($number_1,$number_2){
    $average = ($number_1+$number_2)/2;
    return($average);
}

//determine the decay factor of radioactivity
function decay_factor($date_diff, $tracer = "51Cr"){
	if($tracer == "51Cr"){
		$decay_factor = exp(-0.693*$date_diff/27.7);
	} elseif($tracer == "99mTc") {
		$decay_factor = exp(-0.693*$date_diff/0.25);
	} else {
		$decay_factor = 'NaN';
	}
    return($decay_factor);
}

//calculate the standard deviation of two numbers
function calc_std_dev($value1,$value2){
    $average = ($value1+$value2)/2;
    $result = sqrt((pow(($value1-$average),2)+pow(($value2-$average),2))/2); //population
    //$result = sqrt((pow(($value1-$average),2)+pow(($value2-$average),2))/1); //stikprøve
    return($result);
}

//calculate the variance of two numbers
function calc_variance($value1,$value2){
    $average = ($value1+$value2)/2;
    $result = (pow(($value1-$average),2)+pow(($value2-$average),2)/(2-1));
    return($result);
}

//convert short type to human readable
function type_converter($measurement_type){
	if($measurement_type == 'V1'){
        $type = 'Voksen, 1-punkt';
    } elseif($measurement_type == 'V3'){
        $type = 'Voksen, 3-punkt';
    } elseif($measurement_type == 'V3_24'){
        $type = 'Voksen, 3+24 timer';
    } elseif($measurement_type == 'B1'){
		$type = 'Barn, 1-punkt';
	} elseif($measurement_type == 'B3'){
		$type = 'Barn, 3-punkt';
	} elseif($measurement_type == 'B2_24'){
		$type = 'Barn, 2+24 timer';
	} else {
		$type = 'Udefineret type';
	}
    return($type);
}

//determine the kidney function based on measured GFR from national guidelines
function function_degree($measured_GFR){
    if($measured_GFR >= 90){
        $function = 'Normal eller højere end forventet';
    } elseif($measured_GFR >= 60  && $measured_GFR < 90){
        $function = 'Let nedsat nyrefunktion';
    } elseif($measured_GFR >= 45 && $measured_GFR < 60){
        $function = 'Let/moderat nedsat nyrefunktion';
    } elseif($measured_GFR >= 30 && $measured_GFR < 45){
        $function = 'Moderat/svært nedsat nyrefunktion';
    } elseif($measured_GFR >= 15 && $measured_GFR < 30){
        $function = 'Svært nedsat nyrefunktion';
    } elseif($measured_GFR < 15){
	$function = 'Terminalt nyresvigt';
    } else {
        $function = 'NaN';
    }
    return $function;
}

//calculate stdGFR for single samples for adults and children. 
//corrected for inulin Cr-EDTA difference ((EDTA_clearance - 3.7)*1.1), source: Brøchner-Mortensen, Clin Physiol, 5:1-17, 1985
//adults calculated from Groth and Aasted formula, source: Groth, Aasted, Nucl. Med. Commun, 1:83-86, 1980
//children calculated from EANM guidelines, source: Piepsz et al., Guidelines for glomerular filtration rate determination in children, 2000
function calc_stdGFR($time_in_minutes_from_injection,$counts_per_ml,$dose,$BSA,$adult=True){
    if($adult){
		$exponent = $counts_per_ml*$BSA/$dose;
        //$exponent = ($counts_per_ml-3.7)*1.1*$BSA/$dose;
        $stdGFR = (0.213*$time_in_minutes_from_injection-104)*log($exponent)+1.88*$time_in_minutes_from_injection-928;
		$stdGFR = $stdGFR*1.1-3.7;
    } else {
        $p_120 = ($counts_per_ml-3.6)*1.1*exp(0.008*($time_in_minutes_from_injection-120));
        $v_120 = $dose/($p_120*1000);
        $stdGFR = (2.602*$v_120-0.273)*1.73/$BSA;
    }
    return($stdGFR);
}

//Brøchner-Mortensen and Jødal, Scandinavian Journal of Clinical & Laboratory Investigation, 69(3):314-322, 2009
function calc_stdGFR_multipoint($clearance_single,$BSA){
    $result = $clearance_single/(1+0.00185*pow($BSA,(-0.3))*$clearance_single); //unit: mL/min/1.73m^2
    return($result);
}

//Brøchner-Mortensen and Jødal, Scandinavian Journal of Clinical & Laboratory Investigation, 69(3):314-322, 2009
function calc_GFR_multipoint($clearance_single, $BSA){
    $result = $clearance_single/(1+0.0032*pow($BSA,(-1.3))*$clearance_single); //unit: mL/min
    return($result);
}

//calculate linear regression from array_x, array_y
function linear_regression($x, $y) {
    //calculate # points
    $n = count($x);
    //ensure both arrays of points are the same size
    if (count($x) != count($y)) {
        trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
    }
    //calculate sums
    $x_sum = array_sum($x);
    $y_sum = array_sum($y);
    $xx_sum = 0;
    $xy_sum = 0;
    for($i = 0; $i < $n; $i++) {
        $xy_sum+=($x[$i]*$y[$i]);
        $xx_sum+=($x[$i]*$x[$i]);
    }
    //calculate slope
    $b = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
    //calculate intercept
    $c = ($y_sum - ($b * $x_sum)) / $n;
    //return result
    return(array("slope"=>$b, "intercept"=>$c));
}

//calculate R^2 from fit and data
function r_squared($x, $y, $slope, $intercept) {
	//calculate number points
    $n = count($y);
    //calculate mean
    $y_sum = array_sum($y);
    $x_mean = $x_sum/count($x);
    $y_mean = $y_sum/count($y);
    $ss_tot = 0;
    $ss_res = 0;
    for($i = 0; $i < $n; $i++) {
        $ss_tot+=pow(($y[$i]-$y_mean),2);
        $ss_res+=pow(($y[$i]-($slope*$x[$i]+$intercept)),2);
    }
    //calculate r_squared
    $result = 1-($ss_res/$ss_tot);
    //return result
    return($result);
}

//calculate ydiff from fit and data
function coeff_deter($x, $y, $slope, $intercept) {
	//calculate number points
    $n = count($y);
    //calculate mean
    $y_sum = array_sum($y);
    $x_mean = $x_sum/count($x);
    $y_mean = $y_sum/count($y);
    $ss_tot = 0;
    $ss_res = 0;
    for($i = 0; $i < $n; $i++) {
        //$ss_tot+=pow(($y[$i]-$y_mean),1);
        $ss_res+=($y[$i]-$y_mean)*($x[$i]-$x_mean);
        $ss_tot+=sqrt(pow($y[$i]-$y_mean,2)*pow($x[$i]-$x_mean,2));
    }
    //calculate r_squared
    $result = $ss_res/$ss_tot;
    //return result
    return($result);
}

//check that the measurement valid based on background levels
function measurement_validity_poisson($sample_1_counts,$sample_2_counts,$background_1_counts,$background_2_counts, $sample_1_volume, $sample_2_volume){
    $expected_uncertainty = $sample_2_volume/$sample_1_volume*sqrt($sample_1_counts) + sqrt($sample_2_counts)+sqrt($background_1_counts)+sqrt($background_2_counts);
    $difference_counts = abs(($sample_1_counts-$background_1_counts)*$sample_2_volume/$sample_1_volume-($sample_2_counts-$background_2_counts));
    $ratio = $difference_counts/$expected_uncertainty;
    if(2*$expected_uncertainty > $difference_counts){
		$color = 'green';
	} elseif(2.5*$expected_uncertainty > $difference_counts){
		$color = '#ffcc00';
	} else{
		$color = 'red';
	}
	return(array("ratio"=>$ratio, "color"=>$color));
}

//Estimate Endogenous Creatinin Clearance (EECC)
function calc_EECC($age, $weight, $s_crea, $sex){
	if($sex == 'F'){
		$EECC = ((140-$age)*$weight*1.04)/$s_crea;
	} elseif($sex == 'M'){
		$EECC = ((140-$age)*$weight*1.23)/$s_crea;
	} else {
		$EECC = 'NaN';
	}
    return($EECC);
}

//Estimate GFR from Modification of Diet in Renal Disease (MDRD)
function calc_GFR_MDRD_Schwartz($s_crea,$patient_age,$patient_sex,$patient_height){
	if($s_crea == ''){
		$eGFR = '';
	} else {
		if($patient_age <= 18){
			$eGFR = '';
		} else {
			$eGFR = round(175*pow(($s_crea/88.4),-1.154)*pow($patient_age,-0.203),0);
			if($patient_sex == 'F'){
				$eGFR = round($eGFR * 0.742,0);
			}
			if($eGFR > 90){
				$eGFR = '>90';
			}
		}
	}
	return($eGFR);
}

//Estimate GFR from CKD-EPI for age >= 17 and CKiD_bedside for age < 17
function calc_GFR_CKD_EPI_CKiD($s_crea,$patient_age,$patient_sex,$patient_height){
	if($s_crea == ''){
		$eGFR = '';
	} else {
		if($patient_age < 17){
			$eGFR = round(36.5 * ($patient_height/$s_crea),0);
		} else {
			if($patient_sex == 'F'){
				if($s_crea <= 62){
					$eGFR = round(144*pow(($s_crea/(0.7*88.4)),-0.329)*pow(0.993,$patient_age),0);
				} else {
					$eGFR = round(144*pow(($s_crea/(0.7*88.4)),-1.209)*pow(0.993,$patient_age),0);
				}
			} else {
				if($s_crea <= 80){
					$eGFR = round(141*pow(($s_crea/(0.9*88.4)),-0.411)*pow(0.993,$patient_age),0);
				} else {
					$eGFR = round(141*pow(($s_crea/(0.9*88.4)),-1.209)*pow(0.993,$patient_age),0);
				}
			}
		}
		if($eGFR > 90){
			$eGFR = '>90';
		}
	}
	return($eGFR);
}

//calculate the expected std GFR
//source: children (<2 years): Brøchner-Mortensen, Scand. J. Urol. Nephrol 16:229-236, 1982
//source: adults (>= 20 years): Brøchner-Mortensen, Scand. J. Urol. Nephrol 11:257-262, 1977
function calc_expected_stdGFR($age,$age_in_days,$sex){
    if($age <= 2){
        $exponent = 0.209*log10($age_in_days)+1.44;
        $stdGFR = pow(10,$exponent);
    } elseif($age > 2 && $age < 20){
        $stdGFR = 109;
    } elseif($age >= 20 && $age <= 39 ){
        if($sex == 'M'){ //male
            $stdGFR = 111;
        } else { //female
            $stdGFR = 111*0.929;
        }
    } elseif($age > 39){
        if($sex == 'M'){ //male
            $stdGFR = -1.16*$age+157.8;
        } else { //female
            $stdGFR = -1.07*$age+146;
        }
    } else {
        $stdGFR = 'NaN';
    }
    return(round($stdGFR,2));
}

//calculate effective circulating volume (ECV) from the body surface area
function calc_ECV($BSA){
    $result = (8116.6*$BSA-28.2)/1000;
    return($result);
}

//calculate effective circulating volume (ECV) (Bird's estimate)
function calc_ECV_Bird($height, $weight){
	if(is_null($height) == True || is_null($weight) == True){
		$result = '';
	} else {
		$result = 0.02154*pow($weight,0.6469)*pow($height, 0.7236);
	}
    return($result);
}

//calculate effective circulating volume (ECV) (Abraham's estimate)
function calc_ECV_Abraham($height, $weight){
    $result = $height*sqrt($weight);
    return($result);
}

//calculate effective circulating volume (ECV) from GFR measurement
function calc_ECV_JBM($dose, $BSA, $slope, $intercept){
    $V1 = $dose/$intercept;
    $Cl1 = $dose/($intercept/abs($slope));
    $BSA_power = pow($BSA,-1.3);
    $result = $V1/(1.0+2.0*0.0032*$BSA_power*$Cl1);
    //(1.0+2.0*0.0032*$BSA_power*$Cl1);
    //echo '...' . $BSA . '...';
    //echo '...' . (1.0+2.0*0.0032*$BSA_power*$Cl1) . '...';
    return($result);
}

//calculate effective circulating volume (ECV) from GFR measurement
function calc_ECV_Peters($GFR, $slope){
    $result = $GFR/($slope+0.0154*$slope);
    return($result);
}

//calculate effective circulating volume (ECV) from GFR measurement
function calc_test($dose, $BSA, $slope, $intercept){
    $V1 = $dose/$intercept;
    $Cl1 = $dose/($intercept/$slope);
    $result = round($V1/(1+2*0.0032*pow($BSA,-1.3)*$Cl1)/1000.0,2);
    return($result);
}

//calculate body surface area.
function calc_BSA($height,$weight, $method='haycock'){
    if($method == 'haycock'){
        //Jødal & Brøchner-Mortens calculates from Haycock-formula
        $bsa = 0.024265*pow($weight,0.5378)*pow($height,0.3964);
    } elseif($method == 'dubois'){
        //Du Bois formula
        $bsa = 0.007184*pow($weight,0.425)*pow($height,0.725);
    } else {
        $bsa = 'NaN';
    }
    return((float)$bsa);
}

//change name syntax from RIS output to human readable
function name_syntax($patient_name){
    $result = str_replace("^", ", ", $patient_name);
    return($result);
}

//adhere to MySQL syntax by using . instead of ,
function replace_comma($value){
	$result = str_replace(",", ".", $value);
	return($result);
}

//Switch from english to danish notation of sex
function danify_sex($sex){
    if($sex == 'F'){
        $sex = 'Kvinde';
    } elseif($sex == 'M') { //unnessary but helps understanding
        $sex = 'Mand';
    } else { //we should hopefully not wind up here
        $sex = $sex;
    }
    return($sex);
}

//return a standard html header
function html_header(){
	$header = "";
	$header = $header . "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	$header = $header . "<html>\n";
	$header = $header . "    <head>\n";
	$header = $header . "		 <meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7; IE=EmulateIE9;\" >"; 
	$header = $header . "        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	$header = $header . "        <title>ClearCalc - Nuklear Medicinsk Afdeling, OUH</title>\n";
	$header = $header . "        <link rel=\"stylesheet\" href=\"css/style.css\" type=\"text/css\" media=\"screen\" />\n";
	$header = $header . "        <link rel=\"stylesheet\" href=\"css/dygraphs.css\" type=\"text/css\" />\n";
	$header = $header . "        <link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\"  />\n";
	$header = $header . "        <script src=\"js/confirm_delete.js\" type=\"text/javascript\"></script>\n";
	$header = $header . "        <script src=\"js/checkIE.js\" type=\"text/javascript\"></script>\n";
	$header = $header . "        <script src=\"js/net_weight.js\" type=\"text/javascript\"></script>\n";
	$header = $header . "		 <script src=\"js/toogle.js\" type=\"text/javascript\"></script>\n";
	$header = $header . "		 <!--[if IE]><script src=\"js/dygraphs/excanvas.js\"></script><![endif]-->\n";
	$header = $header . "		 <script src=\"js/dygraphs/dygraph-combined.js\"></script>\n";
	$header = $header . "		 <script src=\"js/dygraphs/shapes.js\"></script>\n";
	$header = $header . "		 <script src=\"js/ajax.js\"></script>\n";
	$header = $header . "    </head>\n";
	$header = $header . "    <body onload=\"checkField(); detectIE();\">\n";
	$header = $header . "        <div class=\"container\">\n";
	$header = $header . "            <div class=\"caption\"><sup>51</sup>Cr-EDTA Clearance - ClearCalc\n";
	//$header = $header . "                <a href=\"/\"><div class=\"logo\">NUK</div>\n";
	$header = $header . "                <a href=\"index.php\"><img class=\"logo\" src=\"images/rsyd_transparent.png\" alt=\"NUK, OUH\"></a>\n";
	$header = $header . "                <div class=\"header_utilities\">\n";
	$header = $header . "                   <a class=\"header_links\" href=\"javascript:window.print()\">Print side</a><br>\n";
	$header = $header . "                   <a class=\"header_links\" href=\"reference.php\">Reference</a><br>\n";
	$header = $header . "					<a class=\"header_links\" href=\"logout.php\">Log ud</a>\n";
	$header = $header . "                </div>\n";
	$header = $header . "            </div>\n";

	return($header);
}

//return a standard html footer
function html_footer(){
	$footer = "";
	$footer = $footer . "<div class=\"copyright\">Nuklearmedicinsk afdeling, Odense Universitetshospital</div>\n";
	$footer = $footer . "        </div>\n";
	$footer = $footer . "    </body>\n";
	$footer = $footer . "</html>\n";
  
	return($footer);
}

//using usort to sort multidimensional arrays
function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strcmp($a[$key], $b[$key]);
    };
}

//format x, y values for dygraph plotting
function create_xy_dygraph($xdata, $ydata, $yfit){
	$numargs = func_num_args();
	if ($numargs == 3){
		for($i=0;$i<sizeof($xdata);$i++){
			if ($i==sizeof($xdata)-1){
				$datastring .= '"'.$xdata[$i].','.$ydata[$i].','.$yfit[$i].'\n",';
			} else {
				$datastring .= '"'.$xdata[$i].','.$ydata[$i].','.$yfit[$i].'\n" +';
			} 
		}
	} elseif($numargs == 2){
		for($i=0;$i<sizeof($xdata);$i++){
			if ($i==sizeof($xdata)-1){
				$datastring .= '"'.$xdata[$i].','.$ydata[$i].'\n",';
			} else {
				$datastring .= '"'.$xdata[$i].','.$ydata[$i].'\n" +';
			} 
		}
	}
	return($datastring);
}

//generate plotting code for regressions
function dygraph_regression($xdata, $ydata, $fit){
	$yfit = array();
	foreach($xdata as $key=>$value){
		$fit_value = $fit['slope']*$value+$fit['intercept'];
		array_push($yfit,$fit_value);
	}
	
	$ymin = min($ydata)-0.5;
	$ymax = max($ydata)+0.5;
	$xmin = min($xdata)-100;
	$xmax = max($xdata)+100;

	$numItems = count($x_val);
	echo "<div class=\"chart\" id=\"dygraphregression\" style=\"width:800px;height:600px;\"></div>\n";
	$initial_header = '<script type="text/javascript">
							g = new Dygraph(document.getElementById("dygraphregression"),';
	$data_header = '"Time,data,fit\n" + ' .
                      create_xy_dygraph($xdata,$ydata,$yfit);
    $final_header = '{
						legend: \'follow\',
						valueRange: ['.$ymin.','.$ymax.'],
						dateWindow: ['.$xmin.','.$xmax.'],
						xlabel: \'tid (min)\',
						ylabel: \'plasmakoncentration (log(Q))\',
						axes: {
							x: {
								axisLabelFontSize: 20,
								valueFormatter: function (x) {
									return x + \' min\'; 
								},
							},
							y: {
								axisLabelFontSize: 20,
							},
						},
						series:{
							data:{
								color:\'blue\',
								pointSize: 4,
								drawPoints: true,
								strokeWidth: 0.0,
							},
							fit:{
								color:\'orange\',
								pointSize: 0,
								drawPoints: false,
								strokeWidth: 3,
							},
						},	
						underlayCallback: function(ctx, area, dygraph) {
                         ctx.strokeStyle = \'black\';
                         ctx.strokeRect(area.x, area.y, area.w, area.h);
						},	
						digitsAfterDecimal: 4,
						gridLineColor: \'#ddd\',
						title: \'PC(logQ) = '.$fit["slope"].' * tid(min) + '.$fit["intercept"].' [R^2 = '.$fit["r_squared"].']\',
					  });
    </script>';
    echo $initial_header ."\n". $data_header ."\n". $final_header ."\n";
}

//code and formatting for dygraph statistics plot
function dygraph_statistics($xdata, $ydata, $zdata){
	
	$ymin = 0;
	$ymax = max($ydata)+2;

	echo "<div class=\"chart\" id=\"dygraphstatistics\" style=\"width:800px;height:600px;\"></div>\n";
	$initial_header = '<script type="text/javascript">
							g = new Dygraph(document.getElementById("dygraphstatistics"),';
	$data_header = '"date,antal,fit\n" + ' .
                      create_xy_dygraph($xdata,$ydata,$yfit);
    $final_header = '{
						legend: \'always\',
						valueRange: ['.$ymin.','.$ymax.'],
						/*dateWindow: ['.$xmin.','.$xmax.'],*/
						xlabel: \'dato\',
						ylabel: \'antal patienter\',
						axes: {
							x: {
								axisLabelFontSize: 20,
							},
							y: {
								axisLabelFontSize: 20,
							},
						},
						series:{
							antal:{
								pointSize: 2,
								color: \'black\',
								drawPoints: false,
								strokeWidth: 1.0,
							},
							fit:{
								pointSize: 2,
								color: \'red\',
								drawPoints: false,
								strokeWidth: 1.0,
							},
						},	
						underlayCallback: function(ctx, area, dygraph) {
                         ctx.strokeStyle = \'black\';
                         ctx.strokeRect(area.x, area.y, area.w, area.h);
						},	
						gridLineColor: \'#ddd\',
						title: \'GFR-patienter\',
						rollPeriod: 1,
						showRoller: true,
					  });
    </script>';
    echo $initial_header ."\n". $data_header ."\n". $final_header ."\n";
}

//dygraph formatting for previrous measurements
function dygraph_history($historydata){
	$age = array();
	$stdGFR = array();
	for($i=0;$i<sizeof($historydata);$i++){
		array_push($age, number_format(calc_age($historydata[$i]['patient_birthday'], $historydata[$i]['patient_exam_date'], '%a')/365.25,2,'.',''));
		array_push($stdGFR, $historydata[$i]['stdGFR']);
	}

	$ymin = min($stdGFR)-20;
	if($ymin < 0){
		$ymin = 0;
	}
	$ymax = max($stdGFR)+20;
	$xmin = min($age)-1.5;
	if($xmin < 0){
		$xmin = 0;
	}
	$xmax = max($age)+1.5;
	
	echo "<div class=\"chart\" id=\"dygraphhistory\" style=\"width:800px;height:600px;\"></div>\n";
	
	$initial_header = '<script type="text/javascript">
							g = new Dygraph(document.getElementById("dygraphhistory"),';
	$data_header = '"Alder,stdGFR\n" + ' .
                      create_xy_dygraph($age, $stdGFR);
    $final_header = '{
						legend: \'follow\',
						valueRange: ['.$ymin.','.$ymax.'],
						dateWindow: ['.$xmin.','.$xmax.'],
						xlabel: \'alder (år)\',
						ylabel: \'stdGFR (ml/min/1.73m^2)\',
						axes: {
							x: {
								axisLabelFontSize: 20,
								valueFormatter: function (x) {
									return x + \' år\'; 
								}
							},
							y: {
								axisLabelFontSize: 20,
								valueFormatter: function (y) {
									return y + \' ml/min/1.73m^2\'; 
								}
							},
						},
						series:{
							stdGFR:{
								color:\'blue\',
								pointSize: 4,
								drawPoints: true,
								strokeWidth: 1.0
							},
						},	
						underlayCallback: function(ctx, area, dygraph) {
                         ctx.strokeStyle = \'black\';
                         ctx.strokeRect(area.x, area.y, area.w, area.h);
						},
						digitsAfterDecimal: 4,
						gridLineColor: \'#ddd\'
					  });
    </script>';
    echo $initial_header ."\n". $data_header ."\n". $final_header ."\n";
}

//normal data for comparison to current measured value
function dygraph_data($GFR,$stdGFR,$patient_sex,$patient_age,$measurement_type){
	if($measurement_type == 'B1' || $measurement_type == 'B2_24' || $measurement_type == 'B3'){
    $datax = array(0.002778,0.055556,0.16667,0.3333,0.5,0.666667,0.833333,1,1.166667,1.3333,1.5,1.66667,1.83333,2,3,15);
    $datay_normal = array(27.54,51.51,64.81,74.91,81.54,86.59,90.72,94.25,97.33,100.09,102.58,104.87,106.98,108,108,108);
    $datay_p2d = array(34.43,64.39,81.01,93.64,101.92,108.24,113.40,117.81,121.67,125.11,128.23,131.08,133.72,135,135,135);
    $datay_m2d = array(20.66,38.64,48.61,56.18,61.15,64.94,68.04,70.69,73.00,75.07,76.94,78.65,80.23,81,81,81);
    $datay_m4d = array(14.32,26.79,33.70,38.95,42.39,45.03,47.17,49.01,50.61,52.05,53.34,54.53,55.63,56.16,56.16,56.16);
    $datay_m6d = array(7.71,14.42,18.14,20.98,22.83,25.05,25.80,26.39,27.25,28.02,28.72,29.36,29.95,30.24,30.24,30.24);
    $xmin = 0;
    $xmax = 15;
} else {
    $datax = array(0,20,39,45,55,65,75,85,95);
    $xmin = 10;
    $xmax = 95;
    if($patient_sex == 'F'){
        $datay_normal = array(103.2,103.2,103.2,97.85,87.15,76.45,65.75,55.05,44.35);
        $datay_p2d = array(129,129,129,122.31,108.94,95.56,82.19,68.81,55.44);      //1.25 x $datay_normal
        $datay_m2d = array(77.4,77.4,77.4,73.39,65.36,57.34,49.31,41.29,33.26);      //0.75 x $datay_normal
        $datay_m4d = array(53.66,53.66,53.66,51.44,45.32,39.75,34.19,28.63,23.06);    //0.52 x $datay_normal
        $datay_m6d = array(28.90,28.90,28.90,27.40,24.40,21.40,18.41,15.41,12.42);    //0.28 x $datay_normal
    } else {
        $datay_normal = array(111,111,111,105.6,94,82.4,70.8,59.2,47.6);
        $datay_p2d = array(139.25,139.25,139.25,132,117.5,103,88.5,74,59.5);           //1.25 x $datay_normal
        $datay_m2d = array(83.55,83.55,83.55,79.2,70.5,61.8,53.1,44.4,35.7);          //0.75 x $datay_normal
        $datay_m4d = array(57.93,57.93,57.93,54.91,48.88,42.85,36.82,30.78,24.75);    //0.52 x $datay_normal
        $datay_m6d = array(31.19,31.19,31.19,29.57,26.32,23.07,19.82,16.58,13.33);    //0.28 x $datay_normal
    }
    
}
$data_string = '';
for($i=0;$i<sizeof($datax);$i++){
	//logic for placing measurement correct in the normal data array
	$data_string .= '"'.$datax[$i].','.$datay_normal[$i].','.$datay_p2d[$i].','.$datay_m2d[$i].','.$datay_m4d[$i].','.$datay_m6d[$i].',NaN,NaN\n" +';
	if($datax[$i] < $patient_age && $datax[$i+1] >= $patient_age){
		$data_string .= '"'.$patient_age.',,,,,,'.$stdGFR.','.$GFR.'\n" +';
	} 
}
	$data_string = preg_replace('/\+(?!.*\+)/','$1,',$data_string);
	echo "<div class=\"chart\" id=\"dygraphdata\" style=\"width:800px;height:600px;\"></div>\n";
	$dygraph_string = '<script type="text/javascript">
      g = new Dygraph(document.getElementById("dygraphdata"),
                      "Time,normal,p2SD,m2SD,m4SD,m6SD,stdGFR,GFR\n" +
                      '.$data_string.'
                      {
						legend: \'never\',
						/*labelsSeparateLines: true,*/
						dateWindow: ['.$xmin.','.$xmax.'],
						xlabel: \'alder (år)\',
						ylabel: \'GFR ml/min/1.73m^2\',
						axes: {
							x: {
								axisLabelFontSize: 20,
								valueFormatter: function (x) {
									return x + \' år\'; 
								},
							},
							y: {
								axisLabelFontSize: 20,
								/*valueRange: [2,6.1],
								 ticker: function(min, max, pixels, opts, dygraph, vals) {
									return [{v:0, label:"0"}, {v:5, label:"5"}, {v:10, label:"10"},
									{v:15, label:"15"}, {v:20, label:"20"}, {v:25, label:"25"},
									{v:30, label:"30"}, {v:35, label:"35"}, {v:40, label:"40"}, 
									{v:45, label:"45"}, {v:50, label:"50"}, {v:55, label:"55"},
									{v:60, label:"60"}, {v:65, label:"65"}, {v:70, label:"70"},
									{v:75, label:"75"}, {v:80, label:"80"}, {v:85, label:"85"}, 
									{v:90, label:"90"}, {v:95, label:"95"}, {v:100, label:"100"},
									{v:105, label:"105"}, {v:110, label:"110"}, {v:115, label:"115"},
									{v:120, label:"120"}, {v:125, label:"125"}, {v:130, label:"130"},
									{v:135, label:"135"}];
								}*/
							},
						},
						series:{
							normal:{
								color:\'black\',
								highlightCircleSize: 0,
								drawPoints: false,
								strokeWidth: 0.8
							},
							p2SD:{
								color:\'olive\',
								highlightCircleSize: 0,
								drawPoints: false,
								strokeWidth: 1.0,
								strokePattern: [3, 3]
							},
							m2SD:{
								color:\'brown\',
								highlightCircleSize: 0,
								strokeWidth: 1.0,
								strokePattern: [3, 3]
							},
							m4SD:{
								color:\'purple\',
								highlightCircleSize: 0,
								strokeWidth: 1.0,
								strokePattern: [3, 3]
							},
							m6SD:{
								color:\'navy\',
								highlightCircleSize: 0,
								drawPoints: false,
								strokeWidth: 1.0,
								strokePattern: [3, 3]
							},
							stdGFR:{
								drawPoints: true,
								drawPointCallback: Dygraph.Circles.SQUARE,
								drawHighlightPointCallback: Dygraph.Circles.SQUARE,
								color:\'blue\',
								pointSize: 6,
								drawPoints: true,
								highlightCircleSize: 6
							},
							GFR:{
								drawPointCallback: Dygraph.Circles.CIRCLE,
								drawHighlightPointCallback: Dygraph.Circles.CIRCLE,
								color:\'red\',
								pointSize: 5,
								drawPoints: true,
								highlightCircleSize: 5
							},
						},	
						underlayCallback: function(ctx, area, dygraph) {
                         ctx.strokeStyle = \'black\';
                         ctx.strokeRect(area.x, area.y, area.w, area.h);
						},
						digitsAfterDecimal: 4,
						connectSeparatedPoints: true,
						gridLineColor: \'#ddd\'
					  });
					  
			g.ready(function() {
					g.setAnnotations([{
						series: \'GFR\',
						x: '.$patient_age.',
						shortText: \'GFR\',
						width:50,
						height: 20,
						text: \'GFR\',
						tickHeight:10
					},
					{
						series: \'stdGFR\',
						x: '.$patient_age.',
						shortText: \'stdGFR\',
						width:50,
						height: 20,
						text: \'stdGFR\',
						tickHeight:10
					}
					 ]);
			});
    </script>';
	echo $dygraph_string;
}

?>
