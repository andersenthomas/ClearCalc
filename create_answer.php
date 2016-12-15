<?php
/*
 * create_answer.php
 */

require('user_authentication.php');
include('common_functions.php');

$config_parameters = parse_ini_file("../../config_clearance.ini", true);

//configuration of database
$db_host = $config_parameters['database_setup']['host'];
$db_user = $config_parameters['database_setup']['user'];
$db_password = $config_parameters['database_setup']['password'];
$db = $config_parameters['database_setup']['database'];

//model parameters
$BSA_model = $config_parameters['model_parameters']['BSA_model'];

//counting parameters
//$counting_time = $config_parameters['counting_parameters']['counting_time'];

$connection_error = False;
$con = mysqli_connect($db_host,$db_user,$db_password,$db);
// Check connection
if (mysqli_connect_errno()){
    $connection_error = True;
    $error_message = '<div class="body_text">Failed to connect to MySQL: ' . mysqli_connect_error() . ' </div>';
}


echo(html_header());

if($connection_error){
    echo $error_message;
    echo html_footer();
} else {
        
		//get and initialize parameters to fill out the form
		$page =                     isset($_GET['page'])                    ? $_GET['page']                     : 1;
		$id =                       isset($_GET['id'])                      ? $_GET['id']                       : 0;
		$measurement_type =         isset($_GET['measurement_type'])        ? $_GET['measurement_type']         : 0;
		$patient_cpr =              isset($_GET['patient_cpr'])             ? $_GET['patient_cpr']              : 0;
		$patient_name =             isset($_GET['patient_name'])            ? $_GET['patient_name']             : 0;
		$patient_injection_time =   isset($_GET['patient_injection_time'])  ? $_GET['patient_injection_time']   : 0;
		$patient_height =           isset($_GET['patient_height'])          ? $_GET['patient_height']           : 0;
		$patient_weight =           isset($_GET['patient_weight'])          ? $_GET['patient_weight']           : 0;
		$patient_sex =              isset($_GET['patient_sex'])             ? $_GET['patient_sex']              : 0;
		$patient_s_crea =           isset($_GET['patient_s_crea'])          ? $_GET['patient_s_crea']           : 0;
		$patient_exam_date =        isset($_GET['patient_exam_date'])       ? $_GET['patient_exam_date']        : 0;
		$department =               isset($_GET['department'])              ? $_GET['department']               : 0;
		$accession_number =         isset($_GET['accession_number'])        ? $_GET['accession_number']         : 0;
		$page_url = 'http://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
		
		//find the specific measurement data from the patient
		$result = mysqli_query($con, "select * from patient_data where id='$id'");
		$row = mysqli_fetch_array($result);
			
		//find measured data from the patient examination. accession_number is unique so no reason for "limit 1"
		$result_examination = mysqli_query($con, "select * from patient_values where accession_number='$accession_number'");
		$row_examination = mysqli_fetch_array($result_examination);			
			
		//find previous measurements from patient
		$result_duplicates = mysqli_query($con, "select * from patient_data where patient_cpr='$patient_cpr' and patient_exam_date != '$patient_exam_date' order by patient_exam_date desc");
		
		//find the standard given by the worksheet
		$standard_to_use = $row_examination['standard_lot_number'];
		//if the standard_lot_number is empty we will chose the newest standard
		if(!isset($standard_to_use)){
			$query = "select * from standards order by standard_date desc limit 1";
		} else {
			$query = "select * from standards where lot_number='$standard_to_use'";
		}
		
		$result_standard = mysqli_query($con, $query);
		$row_standard = mysqli_fetch_array($result_standard);

		//$standard_to_use variable contains the printed value
		if(empty($standard_to_use)){
			$standard_to_use = $row_standard['lot_number'];
		} else {
			$standard_to_use = $row_examination['standard_lot_number'];
		}

		//reused parameters
		$patient_birthday = $row['patient_birthday'];
		$patient_age = number_format(calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a')/365.25,2,'.','');

		//array for previous measurements
		$history_array = array();

		//check if there are any counts available from the worksheet
		if(empty($row_examination['sample_1_counts']) || $row_examination['sample_1_counts'] == '0' || empty($row_examination['sample_2_counts']) || $row_examination['sample_2_counts'] == '0'){
			echo '<script language="javascript">';
			echo 	'alert("Der blev ikke fundet tælletal på prøve 1 og/eller 2. Gå venligst til arbejdsarket og ret.\nDer vil ikke blive vist nogen graf.")';
			echo '</script>';
		} elseif(empty($row_examination['background_1_counts']) || $row_examination['background_1_counts'] == '0' || empty($row_examination['background_2_counts']) || $row_examination['background_2_counts'] == '0'){
			echo '<script language="javascript">';
			echo 	'alert("Baggrund 1 og/eller 2 er 0. Ret venligst")';
			echo '</script>';
		} elseif(empty($row_examination['standard_1_counts']) || $row_examination['standard_1_counts'] == '0' || empty($row_examination['standard_2_counts']) || $row_examination['standard_2_counts'] == '0'){
			echo '<script language="javascript">';
			echo 	'alert("Standard 1 og/eller 2 er 0. Ret venligst")';
			echo '</script>';
		} 

		?>

		<div class="datacontainer">
			<!--header info-->
			<table class="submit_table">
				<tr>
					<td colspan="2" align="right">
						<input type="button" class="button" value="Patientliste" onclick="window.location.href='list_patients.php?page=<?php echo $page;?>'">
						<input type="button" class="button" value="Arbejdsark" onclick="window.location.href='create_worksheet.php?page=<?php echo $page;?>&id=<?php echo($row['id']);?>&measurement_type=<?php echo($row['measurement_type']);?>&patient_cpr=<?php echo($row['patient_cpr']);?>&patient_injection_time=<?php echo($row['patient_injection_time']);?>&patient_s_crea=<?php echo($row['patient_s_crea']);?>&patient_height=<?php echo($row['patient_height']);?>&patient_weight=<?php echo($row['patient_weight']);?>&department=<?php echo($row['department']);?>&patient_name=<?php echo($row['patient_name']);?>&patient_sex=<?php echo($row['patient_sex']);?>&patient_exam_date=<?php echo($row['patient_exam_date']);?>&accession_number=<?php echo($row['accession_number']);?>'">
					</td>
				</tr>
			</table>
			<table class="sub_worksheet">
				<tr>
					<td width="50%"><h3><sup>51</sup>Cr-EDTA Clearance</h3></td>
					<td align="right"><b>Odense Universitetshospital</b><br>Nuklearmedicinsk afdeling</td>
				</tr>
				<tr>
					<td width="50%">Type: <?php echo type_converter($measurement_type); ?></td>
					<td align="right">Henvisende afdeling: <?php echo $department; ?></td>
				</tr>
				<tr>
					<td colspan="2" align="right">Svarark dannet d. <?php echo get_current_date('d/m/Y'); ?> </td>
				</tr>
			</table>
			<!--end header info-->
			
			<!--patient info-->
			<table class="sub_worksheet">
				<tr>
					<td colspan="4"><b>Patientinformation</b></td>
				</tr>
				<tr>
					<td width="30%">Undersøgelsesdato</td>
					<td colspan="3"><?php echo $row['patient_exam_date']; ?>
				</tr>
				<tr>
					<td width="30%">Navn</td>
					<td colspan="3"><?php echo name_syntax($patient_name); ?></td>
				</tr>
				<tr>
					<td width="30%">Køn</td>
					<td colspan="3"><?php echo danify_sex($patient_sex); ?></td>
				</tr>
				<tr>
					<td width="30%">CPR</td>
					<td><?php echo $patient_cpr; ?></td>
					<td width="30%"align="right">Kreatinin</td>
					<td><?php echo $patient_s_crea; ?> &#181;mol/l</td>
				</tr>
				<tr>
					<td width="30%">Højde</td>
					<td width="20%"><?php echo $patient_height; ?> cm</td>
					
					<td align="right">eGFR (aldersbaseret)</td> 
					<td><?php echo round(calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex),0);?> ml/min/1.73m<sup>2</sup></td>
				</tr>
				<tr>
					<td width="30%">Vægt</td>
					<td width="20%"><?php echo $patient_weight; ?> kg</td>
				<?php
				if($patient_age <= 18){
				?>
					<td colspan="2"></td>
				<?php
				} else {
				?>
					<td align="right">eGFR (MDRD)</td>
					<td><?php echo calc_GFR_MDRD_Schwartz($row['patient_s_crea'], $patient_age, $patient_sex, $row['patient_height']);?> ml/min/1.73m<sup>2</sup></td>
				<?php
				}
				?>
				</tr>
				<tr>
					<td width="30%">Alder</td>
					<td width="20%"><?php echo $patient_age;?> år</td>
				<?php
				if($patient_age < 1){
				?>
					<td colspan="2">
				<?php
				} else {
				?>
					<td align="right">eGFR (<?php echo $patient_age < 17 ? 'CKiD<sub>bedside</sub>' : 'CKD-EPI';?>)<!--<sup><a href="#footMDRD">1</a></sup>--></td>
					<td><?php echo calc_GFR_CKD_EPI_CKiD($row['patient_s_crea'], $patient_age, $patient_sex, $patient_height);?> ml/min/1.73m<sup>2</sup></td>
				<?php
				}
				?>
				</tr>
				<tr>
					<td width="30%">Overfladeareal</td>
					<td width="20%"><?php echo round(calc_BSA($patient_height, $patient_weight, $BSA_model),2);?> m<sup>2</sup></td>
					<td align="right">ECV<sub>est</sub> (Bird's estimate)<!--<sup><a href="#footECV">2</a></sup>--></td>
					<td><?php echo round(calc_ECV_Bird($row['patient_height'],$row['patient_weight']),1);?> liter</td>
				</tr>
			</table>
			<!--end patient info-->
			
			<!--standard info-->
			<table class="sub_worksheet">
				<tr>
					<td colspan="2"><b>Standardinformation</b></td>
				</tr>
				<tr>
					<td width="30%">Standard</td>
					<td width="70%">#<?php echo $standard_to_use; ?></td>
				</tr>
			</table>
								
		<?php
			if($measurement_type == 'V1'){
                $standard_counts_1_normalized = standard_counts($row_examination['standard_1_counts'],$row_examination['background_1_counts'],$row_examination['standard_1_volume'],$row_examination['standard_1_count_time']);
                $standard_counts_2_normalized = standard_counts($row_examination['standard_2_counts'],$row_examination['background_2_counts'],$row_examination['standard_2_volume'],$row_examination['standard_2_count_time']);
                $dose = calc_dose(calc_average($standard_counts_1_normalized,$standard_counts_2_normalized),$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
                $time_from_injection_sample_1 = datesub($row_examination['actual_injection_time'],$row_examination['sample_1_time']);
                $time_from_injection_sample_2 = datesub($row_examination['actual_injection_time'],$row_examination['sample_2_time']);
				$counts_1_normalized = counts_per_ml($row_examination['sample_1_counts'],$row_examination['background_1_counts'],$row_examination['sample_1_volume'],$row_examination['sample_1_count_time']);
                $counts_2_normalized = counts_per_ml($row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['sample_2_volume'],$row_examination['sample_2_count_time']);
                $stdGFR_sample_1 = calc_stdGFR($time_from_injection_sample_1,$counts_1_normalized,$dose,calc_BSA($patient_height, $patient_weight, $BSA_model));
                $stdGFR_sample_2 = calc_stdGFR($time_from_injection_sample_2,$counts_2_normalized,$dose,calc_BSA($patient_height, $patient_weight, $BSA_model));
                $stddev_GFR = calc_std_dev($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),$stdGFR_sample_2*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73));
                $stddev_stdGFR = calc_std_dev($stdGFR_sample_1,$stdGFR_sample_2);
                $stdGFR = calc_average($stdGFR_sample_1, $stdGFR_sample_2);
                
                //echo $time_from_injection_sample_2;
		?>
			
					<!--data section-->                                   
					<table class="sub_worksheet_half" style="float:left">
						<tr>
							<td colspan="2"><b>Blodprøve 1</b></td>
						</tr>
						<tr>
							<td width="30%">GFR:</td>
							<td><?php echo round($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</td>
						</tr>
						<tr>
							<td>std-GFR:</td>
							<td><?php echo round($stdGFR_sample_1,0); ?> ml/min/1.73 m<sup>2</sup></td>
						</tr>
					</table>
					<table class="sub_worksheet_half" style="float:right">
						<tr>
							<td colspan="2"><b>Blodprøve 2</b></td>
						</tr>
						<tr>
							<td width="30%">GFR:</td>
							<td><?php echo round($stdGFR_sample_2*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</td>
						</tr>
						<tr>
							<td>std.-GFR:</td>
							<td><?php echo round($stdGFR_sample_2,0); ?> ml/min/1.73 m<sup>2</sup></td>
						</tr>
					</table>
					
					<table class="sub_worksheet">
						<tr>
							<td colspan="2"><b>Samlet resultat</b></td>
						</tr>
						<tr>
							<td width="30%">GFR (gennemsnit)</td> 
							<td><b><?php echo round(calc_average($stdGFR_sample_1*calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73,$stdGFR_sample_2*calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</b></td>
						</tr>
						<tr>
							<td width="30%">std.-GFR (gennemsnit)</td>
							<td><b><?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),0); ?> ml/min/1.73 m<sup>2</sup> (<?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2)/calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex)*100,0);?> % af forventet niveau)</b></td>
						</tr>
						<tr>
							<td width="30%">Funktion (aldersuafhængig klassificering)</td>
							<td><b><?php echo function_degree(round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),0)); ?></b></td>
						</tr>
						<tr>
							<td width="30%">clearance pr. time / ECV<sub>est</sub></td> 
							<td><?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2)/(calc_ECV_Bird($row['patient_height'],$row['patient_weight'])*1000)*60*100,0);?>%</td> 
						</tr>
						<tr>
							<?php $validity = measurement_validity_poisson($row_examination['sample_1_counts'],$row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_1_volume'],$row_examination['sample_2_volume']);?>
							<td width="30%">Statistisk afvigelse af tælletal</td> 
							<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
						</tr>
					</table>
					<!--end data section-->
					
					
					<!--plot section-->
					<table class="sub_worksheet">
						<tr>
							<?php
								$stdGFR = calc_average($stdGFR_sample_1,$stdGFR_sample_2);
								$GFR = $stdGFR*(calc_BSA($row['patient_height'], $row['patient_weight'], $BSA_model)/1.73);
							?>
							<!--<td align="center"><img width="640" height="480" src="create_GFR_plot.php?patient_age=<?php echo $patient_age;?>&std_GFR=<?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),2)?>&stddev_stdGFR=<?php echo round(calc_std_dev($stdGFR_sample_1,$stdGFR_sample_2),2);?>&GFR=<?php echo round($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0)?>&stddev_GFR=<?php echo $stddev_GFR;?>&patient_sex=<?php echo $row['patient_sex'];?>&measurement_type=<?php echo $row['measurement_type'];?>&accession_number=<?php echo $accession_number;?>"></td>-->
							<td align="center"><?php dygraph_data($GFR,$stdGFR,$row['patient_sex'],$patient_age,$row['measurement_type']);?></td>
						</tr>
					</table>
					<!--end plot section-->
								
				<?php
				
			} elseif($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
				$standard_counts_1_normalized = standard_counts($row_examination['standard_1_counts'],$row_examination['background_1_counts'],$row_examination['standard_1_volume'],$row_examination['standard_1_count_time']);
				$standard_counts_2_normalized = standard_counts($row_examination['standard_2_counts'],$row_examination['background_2_counts'],$row_examination['standard_2_volume'],$row_examination['standard_2_count_time']);
				$dose_standard_1 = calc_dose($standard_counts_1_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
				$dose_standard_2 = calc_dose($standard_counts_2_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
				$time_from_injection_sample_1 = datesub($row_examination['actual_injection_time'],$row_examination['sample_1_time']);
				$time_from_injection_sample_2 = datesub($row_examination['actual_injection_time'],$row_examination['sample_2_time']);
				$time_from_injection_sample_3 = datesub($row_examination['actual_injection_time'],$row_examination['sample_3_time']);
				$time_from_injection_sample_4 = datesub($row_examination['actual_injection_time'],$row_examination['sample_4_time']);
				$counts_1_normalized = counts_per_ml($row_examination['sample_1_counts'],$row_examination['background_1_counts'],$row_examination['sample_1_volume'],$row_examination['sample_1_count_time']);
				$counts_2_normalized = counts_per_ml($row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['sample_2_volume'],$row_examination['sample_2_count_time']);
				$counts_3_normalized = counts_per_ml($row_examination['sample_3_counts'],$row_examination['background_1_counts'],$row_examination['sample_3_volume'],$row_examination['sample_3_count_time']);
				$counts_4_normalized = counts_per_ml($row_examination['sample_4_counts'],$row_examination['background_1_counts'],$row_examination['sample_4_volume'],$row_examination['sample_4_count_time']);
		
	
				$x_values = array($time_from_injection_sample_1,$time_from_injection_sample_2,$time_from_injection_sample_3,$time_from_injection_sample_4);
				$y_values = array(log($counts_1_normalized),log($counts_2_normalized),log($counts_3_normalized),log($counts_4_normalized));
				
				//returns array[b = slope,c = intercept]
				$regression_values = linear_regression($x_values,$y_values);
				//For statistics plot
				$r_squared = r_squared($x_values, $y_values, $regression_values[slope], $regression_values[intercept]);
				$intercept = $regression_values[intercept];
				$slope = $regression_values[slope];
				$x_values_stat = $x_values;
				$y_values_stat = $y_values;
				$regression_values[intercept] = exp($regression_values[intercept]);
				$area_under_curve = abs($regression_values[intercept]/$regression_values[slope]);
				$dose_auc = calc_average($dose_standard_1,$dose_standard_2)/$area_under_curve;
				$GFR = calc_GFR_multipoint($dose_auc, calc_BSA($patient_height, $patient_weight, $BSA_model));
				$stdGFR = $GFR * 1.73/calc_BSA($patient_height, $patient_weight, $BSA_model);

				
		?>

				<!--data section-->
				<?php
					if(3*calc_std_dev($row_examination['background_1_counts'],$row_examination['background_2_counts']) + calc_average($row_examination['background_1_counts'],$row_examination['background_2_counts']) > calc_average($row_examination['sample_3_counts'],$row_examination['sample_4_counts'])){
				?>
					<table class="sub_worksheet">
						<tr>
							<td>
								<div style="color:#FF0000">
									<h3>Nyrefunktionen er for god. Der bør anvendes 1-punktsmåling i stedet.</h3></td>
								</div>
						</tr>
					</table>
				<?php
					}
					if($row_examination['sample_4_counts'] - $row_examination['background_2_counts'] < 0 || $row_examination['sample_3_counts'] - $row_examination['background_1_counts'] < 0){
				 ?>
					<table class="sub_worksheet">
						<tr>
							<td>
								<div style="color:#FF0000">
									<h3>Baggrunden er højere end tælletalene. Der vil ikke blive vist data.</h3></td>
								</div>
						</tr>
					</table>
				<?php
					} else {
				?>
				
						<table class="sub_worksheet">
							<tr>
								<td width="30%">GFR</td> 
								<td><b><?php echo round($GFR,0); ?> ml/min</b></td>
							</tr>
							<tr>
								<td width="30%">std.-GFR</td> 
								<td><b><?php echo round($stdGFR,0); ?> ml/min/1.73 m<sup>2</sup> (<?php echo round($stdGFR/calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex)*100,0);?> % af forventet niveau)</b></td>
							</tr>
							<tr>
								<td width="30%">Funktion (aldersuafhængig klassificering)</td>
								<td><b><?php echo function_degree(round($stdGFR,0)); ?></b></td>
							</tr>
							<tr>
								<td width="30%">clearance pr. time / ECV<sub>målt</sub></td> 
								<td><?php echo round(($stdGFR*60/1000)/(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000))*100,0);?>%</td>
							</tr>
							<tr>
								<?php $validity = measurement_validity_poisson($row_examination['sample_1_counts'],$row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_1_volume'],$row_examination['sample_2_volume']);?>
								<td width="30%">Statistisk afvigelse af tælletal (dag 1)</td> 
								<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
							</tr>
							<tr>
								<?php $validity = measurement_validity_poisson($row_examination['sample_3_counts'],$row_examination['sample_4_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_3_volume'],$row_examination['sample_4_volume']);?>
								<td width="30%">Statistisk afvigelse af tælletal (dag 2)</td> 
								<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
							</tr>
						</table>
						
						<table class="sub_worksheet">
							<tr>
								<td width="30%">T<sub>1/2</sub>:</td> 
								<td><?php echo round(abs(log(2)/$regression_values[slope]),2);?> minutter</td>
							</tr>
							<!--<tr>
								<td width="30%">Målt ECV:</td>
								<td><?php echo round(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000),2);?> liter (<?php echo round(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000)/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							-->
							<tr>
								<td width="30%">Målt ECV (JBM):</td>
								<td><?php echo calc_ECV_JBM(calc_average($dose_standard_1,$dose_standard_2), calc_BSA($patient_height, $patient_weight, $BSA_model), $regression_values[slope], $regression_values[intercept]);?> liter (<?php echo round(calc_ECV_JBM(calc_average($dose_standard_1,$dose_standard_2), calc_BSA($patient_height, $patient_weight, $BSA_model), $regression_values[slope], $regression_values[intercept])/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							<!--<tr>
								<td width="30%">Beregnet ECV:</td>
								<td><?php echo round(calc_ECV_Bird($row['patient_height'],$row['patient_weight']),1);?> liter (<?php echo round(calc_ECV_Bird($row['patient_height'],$row['patient_weight'])/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							-->
						</table>
						<!--end data section-->
						
						<!--plot section-->
						<table class="sub_worksheet">
							<tr>
								<!--<td align="center"><img class="GFR-plot" src="create_GFR_plot.php?patient_age=<?php echo $patient_age;?>&std_GFR=<?php echo round($stdGFR,2)?>&GFR=<?php echo round($GFR,2)?>&patient_sex=<?php echo $row['patient_sex'];?>&measurement_type=<?php echo $row['measurement_type'];?>&accession_number=<?php echo $accession_number;?>"></td>-->
								<td align="center"><?php dygraph_data($GFR,$stdGFR,$row['patient_sex'],$patient_age,$row['measurement_type']);?></td>
							</tr>
						</table>
						<!--end plot section-->
							
			<?php
					}
				
			} elseif($measurement_type == 'V3' || $measurement_type == 'B3'){
				$standard_counts_1_normalized = standard_counts($row_examination['standard_1_counts'],$row_examination['background_1_counts'],$row_examination['standard_1_volume'],$row_examination['standard_1_count_time']);
				$standard_counts_2_normalized = standard_counts($row_examination['standard_2_counts'],$row_examination['background_2_counts'],$row_examination['standard_2_volume'],$row_examination['standard_2_count_time']);
				$dose_standard_1 = calc_dose($standard_counts_1_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
				$dose_standard_2 = calc_dose($standard_counts_2_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
				$time_from_injection_sample_1 = datesub($row_examination['actual_injection_time'],$row_examination['sample_1_time']);
				$time_from_injection_sample_2 = datesub($row_examination['actual_injection_time'],$row_examination['sample_2_time']);
				$time_from_injection_sample_3 = datesub($row_examination['actual_injection_time'],$row_examination['sample_3_time']);
				$time_from_injection_sample_4 = datesub($row_examination['actual_injection_time'],$row_examination['sample_4_time']);
				$time_from_injection_sample_5 = datesub($row_examination['actual_injection_time'],$row_examination['sample_5_time']);
				$time_from_injection_sample_6 = datesub($row_examination['actual_injection_time'],$row_examination['sample_6_time']);
				$counts_1_normalized = counts_per_ml($row_examination['sample_1_counts'],$row_examination['background_1_counts'],$row_examination['sample_1_volume'],$row_examination['sample_1_count_time']);
				$counts_2_normalized = counts_per_ml($row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['sample_2_volume'],$row_examination['sample_2_count_time']);
				$counts_3_normalized = counts_per_ml($row_examination['sample_3_counts'],$row_examination['background_1_counts'],$row_examination['sample_3_volume'],$row_examination['sample_3_count_time']);
				$counts_4_normalized = counts_per_ml($row_examination['sample_4_counts'],$row_examination['background_1_counts'],$row_examination['sample_4_volume'],$row_examination['sample_4_count_time']);
				$counts_5_normalized = counts_per_ml($row_examination['sample_5_counts'],$row_examination['background_1_counts'],$row_examination['sample_5_volume'],$row_examination['sample_5_count_time']);
				$counts_6_normalized = counts_per_ml($row_examination['sample_6_counts'],$row_examination['background_1_counts'],$row_examination['sample_6_volume'],$row_examination['sample_6_count_time']);
	
				$x_values = array($time_from_injection_sample_1,$time_from_injection_sample_2,$time_from_injection_sample_3,$time_from_injection_sample_4,$time_from_injection_sample_5,$time_from_injection_sample_6);
				$y_values_log = array(log($counts_1_normalized),log($counts_2_normalized),log($counts_3_normalized),log($counts_4_normalized),log($counts_5_normalized),log($counts_6_normalized));
				$y_values = array($counts_1_normalized,$counts_2_normalized,$counts_3_normalized,$counts_4_normalized,$counts_5_normalized,$counts_6_normalized);
				
				//print_r($y_values_log);
				
				//returns array[b = slope,c = intercept]
				$regression_values = linear_regression($x_values,$y_values_log);

				//For statistics plot
				$r_squared = r_squared($x_values, $y_values_log, $regression_values[slope], $regression_values[intercept]);
				$intercept = $regression_values[intercept];
				$slope = $regression_values[slope];
				$x_values_stat = $x_values;
				$y_values_stat = $y_values_log;
				$regression_values[intercept] = exp($regression_values[intercept]);
				$area_under_curve = abs($regression_values[intercept]/$regression_values[slope]);
				$dose_auc = calc_average($dose_standard_1,$dose_standard_2)/$area_under_curve;
				$GFR = calc_GFR_multipoint($dose_auc, calc_BSA($patient_height, $patient_weight, $BSA_model));
				$stdGFR = $GFR * 1.73/calc_BSA($patient_height, $patient_weight, $BSA_model);
				
		?>

				<!--data section-->
				<?php
					
					if($row_examination['sample_3_counts'] - $row_examination['background_1_counts'] < 0){
				 ?>
					<table class="sub_worksheet">
						<tr>
							<td>
								<div style="color:#FF0000">
									<h3>Baggrunden er højere end tælletalene. Der vil ikke blive vist data.</h3></td>
								</div>
						</tr>
					</table>
				<?php
					} else {
				?>
				
						<table class="sub_worksheet">
							<tr>
								<td width="30%">GFR</td> 
								<td><b><?php echo round($GFR,0); ?> ml/min</b></td>
							</tr>
							<tr>
								<td width="30%">std.-GFR</td> 
								<td><b><?php echo round($stdGFR,0); ?> ml/min/1.73 m<sup>2</sup> (<?php echo round($stdGFR/calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex)*100,0);?> % af forventet niveau)</b></td>
							</tr>
							<tr>
								<td width="30%">Funktion (aldersuafhængig klassificering)</td>
								<td><b><?php echo function_degree(round($stdGFR,0)); ?></b></td>
							</tr>
							<tr>
								<td width="30%">clearance pr. time / ECV<sub>målt</sub></td> 
								<td><?php echo round(($stdGFR*60/1000)/(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000))*100,0);?>%</td>
							</tr>
							<tr>
								<?php $validity = measurement_validity_poisson($row_examination['sample_1_counts'],$row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_1_volume'],$row_examination['sample_2_volume']);?>
								<td width="30%">Statistisk afvigelse af tælletal (prøvetidspunkt 1)</td> 
								<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
							</tr>
							<tr>
								<?php $validity = measurement_validity_poisson($row_examination['sample_3_counts'],$row_examination['sample_4_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_3_volume'],$row_examination['sample_4_volume']);?>
								<td width="30%">Statistisk afvigelse af tælletal (prøvetidspunkt 2)</td> 
								<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
							</tr>
							<tr>
								<?php $validity = measurement_validity_poisson($row_examination['sample_5_counts'],$row_examination['sample_6_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_5_volume'],$row_examination['sample_6_volume']);?>
								<td width="30%">Statistisk afvigelse af tælletal (prøvetidspunkt 3)</td> 
								<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
							</tr>
						</table>
						
						<table class="sub_worksheet">
							<tr>
								<td width="30%">T<sub>1/2</sub>:</td> 
								<td><?php echo round(abs(log(2)/$regression_values[slope]),2);?> minutter</td>
							</tr>
							<!--<tr>
								<td width="30%">Målt ECV:</td>
								<td><?php echo round(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000),2);?> liter (<?php echo round(calc_average($dose_standard_1,$dose_standard_2)/($regression_values[intercept]*1000)/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							
							<tr>
								<td width="30%">Målt ECV (Peters):</td>
								<td><?php echo round(calc_ECV_Peters($GFR,abs($regression_values[slope]))/1000,2);?> liter (<?php echo round((calc_ECV_Peters($GFR,abs($regression_values[slope]))/1000)/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							-->
							<tr>
								<td width="30%">Målt ECV (JBM):</td>
								<td><?php echo round(calc_ECV_JBM(calc_average($dose_standard_1,$dose_standard_2), calc_BSA($patient_height, $patient_weight, $BSA_model), $regression_values[slope], $regression_values[intercept])/1000,2);?> liter (<?php echo round(calc_ECV_JBM(calc_average($dose_standard_1,$dose_standard_2), calc_BSA($patient_height, $patient_weight, $BSA_model), $regression_values[slope], $regression_values[intercept])/1000/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							<!--<tr>
								<td width="30%">Beregnet ECV:</td>
								<td><?php echo round(calc_ECV(calc_BSA($patient_height, $patient_weight, $BSA_model)),2);?> liter (<?php echo round(calc_ECV(calc_BSA($patient_height, $patient_weight, $BSA_model))/$row['patient_weight']*100,2);?>% af patientvægt)</td>
							</tr>
							-->
						</table>
						<!--end data section-->
						
						<!--plot section-->
						<table class="sub_worksheet">
							<tr>
								<!--<td align="center"><img class="GFR-plot" src="create_GFR_plot.php?patient_age=<?php echo $patient_age;?>&std_GFR=<?php echo round($stdGFR,2)?>&GFR=<?php echo round($GFR,2)?>&patient_sex=<?php echo $row['patient_sex'];?>&measurement_type=<?php echo $row['measurement_type'];?>&accession_number=<?php echo $accession_number;?>"></td>-->
								<td align="center"><?php dygraph_data($GFR,$stdGFR,$row['patient_sex'],$patient_age,$row['measurement_type']);?></td>
							</tr>
						</table>
						<!--end plot section-->
								
			<?php
					}
			} elseif($measurement_type == 'B1') {
                $standard_counts_1_normalized = standard_counts($row_examination['standard_1_counts'],$row_examination['background_1_counts'],$row_examination['standard_1_volume'],$row_examination['standard_1_count_time']);
                $standard_counts_2_normalized = standard_counts($row_examination['standard_2_counts'],$row_examination['background_2_counts'],$row_examination['standard_2_volume'],$row_examination['standard_2_count_time']);
                $dose = calc_dose(calc_average($standard_counts_1_normalized, $standard_counts_2_normalized),$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_examination['weight_full_syringe'],$row_examination['weight_empty_syringe']);
                $time_from_injection_sample_1 = datesub($row_examination['actual_injection_time'],$row_examination['sample_1_time']);
                $time_from_injection_sample_2 = datesub($row_examination['actual_injection_time'],$row_examination['sample_2_time']);
                $counts_1_normalized = counts_per_ml($row_examination['sample_1_counts'],$row_examination['background_1_counts'],$row_examination['sample_1_volume'],$row_examination['sample_1_count_time']);
                $counts_2_normalized = counts_per_ml($row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['sample_2_volume'],$row_examination['sample_2_count_time']);
                $stdGFR_sample_1 = calc_stdGFR($time_from_injection_sample_1,$counts_1_normalized,$dose,calc_BSA($patient_height, $patient_weight, $BSA_model),False); //false to indicate child
                $stdGFR_sample_2 = calc_stdGFR($time_from_injection_sample_2,$counts_2_normalized,$dose,calc_BSA($patient_height, $patient_weight, $BSA_model),False); //false to indicate child
                $stddev_GFR = calc_std_dev($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),$stdGFR_sample_2*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73));
                $stdGFR = calc_average($stdGFR_sample_1, $stdGFR_sample_2);
                
		?>
			
				<table class="sub_worksheet_half" style="float:left">
					<tr>
						<td colspan="2"><b>Blodprøve 1</b></td>
					</tr>
					<tr>
						<td width="30%">GFR:</td>
						<td><?php echo round($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</td>
					</tr>
					<tr>
						<td>std-GFR:</td>
						<td><?php echo round($stdGFR_sample_1,0); ?> ml/min/1.73 m<sup>2</sup></td>
					</tr>
				</table>
				<table class="sub_worksheet_half" style="float:right">
					<tr>
						<td colspan="2"><b>Blodprøve 2</b></td>
					</tr>
					<tr>
						<td width="30%">GFR:</td>
						<td><?php echo round($stdGFR_sample_2*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</td>
					</tr>
					<tr>
						<td>std-GFR:</td>
						<td><?php echo round($stdGFR_sample_2,0); ?> ml/min/1.73 m<sup>2</sup></td>
					</tr>
				</table>
				
				<table class="sub_worksheet">
					<tr>
						<td colspan="2"><b>Samlet resultat</b></td>
					</tr>
					<tr>
						<td width="30%">GFR (gennemsnit)</td> 
						<td><b><?php echo round(calc_average($stdGFR_sample_1*calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73,$stdGFR_sample_2*calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0); ?> ml/min</b></td>
					</tr>
					<tr>
						<td width="30%">std.-GFR (gennemsnit)</td>
						<td><b><?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),0); ?> ml/min/1.73 m<sup>2</sup> (<?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2)/calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex)*100,0);?> % af forventet niveau)</b></td>
					</tr>
					<tr>
						<td width="30%">Funktion (aldersuafhængig klassificering)</td>
						<td><b><?php echo function_degree(round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),0)); ?></b></td>
					</tr>
					<tr>
						<td width="30%">clearance pr. time / ECV<sub>est</sub></td> 
						<td><?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2)/(calc_ECV_Bird($row['patient_height'],$row['patient_weight'])*1000)*60*100,0);?>%</td> 
					</tr>
					<tr>
						<?php $validity = measurement_validity_poisson($row_examination['sample_1_counts'],$row_examination['sample_2_counts'],$row_examination['background_1_counts'],$row_examination['background_2_counts'],$row_examination['sample_1_volume'],$row_examination['sample_2_volume']);?>
						<td width="30%">Statistisk afvigelse af tælletal</td> 
						<td><span style="color:<?php echo $validity['color'];?>"><?php echo round($validity['ratio'],2);?> x SD</span></td>
					</tr>
				</table>
				
				<!--end data section-->
				
				<!--plot section-->
				<table class="sub_worksheet">
					<tr>
						<!--<td align="center"><img width="640" height="480" src="create_GFR_plot.php?patient_age=<?php echo $patient_age;?>&std_GFR=<?php echo round(calc_average($stdGFR_sample_1,$stdGFR_sample_2),2)?>&stddev_stdGFR=<?php echo round(calc_std_dev($stdGFR_sample_1,$stdGFR_sample_2),2);?>&GFR=<?php echo round($stdGFR_sample_1*(calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73),0)?>&stddev_GFR=<?php echo $stddev_GFR;?>&patient_sex=<?php echo $row['patient_sex'];?>&measurement_type=<?php echo $row['measurement_type'];?>&accession_number=<?php echo $accession_number;?>"></td>-->
						<td align="center"><?php dygraph_data($stdGFR*(calc_BSA($row['patient_height'], $row['patient_weight'], $BSA_model)/1.73),$stdGFR,$row['patient_sex'],$patient_age,$row['measurement_type']);?></td>
					</tr>
				</table>
				<!--end plot section-->
								
				<?php
				
			} else {
				echo '<div class="body_text">Der findes ikke resultark til den pågældende type eller typen er ikke angivet.</div>';
			}
			
			?>
			
			<!--previous measurements-->
			<h3><a class="history" href="javascript:toggle('history')">Vis/skjul historik</a></h3>
			<div id="history" style="display:block;page-break-before:always;">
			
			<div class="body_text">
				<!-- create search link for patient history-->
				<?php
                    $query = "select * from patient_data where patient_cpr = '$patient_cpr'";
					$result = mysqli_query($con, $query);
					$list_of_ids = array();

					while($row = mysqli_fetch_array($result)){
						array_push($list_of_ids,$row['id']);
					}
                    $pt_other_meas = 'list_patients.php?';

                    for($i=0;$i<sizeof($list_of_ids);$i++){
                        $pt_other_meas = $pt_other_meas . 'id[]=' . $list_of_ids[$i];
                        //do not add '&' in the last iteration
                        if($i<sizeof($list_of_ids)-1){
                            $pt_other_meas = $pt_other_meas . '&';
                        }
                    }
				?>
				
				<h3>Historik på <a href="<?php echo $pt_other_meas;?>"><?php echo name_syntax($patient_name);?></a></h3>
			</div>
			<table class="sub_worksheet">
				<tr>
					<th>Undersøgelsesdato</b></th>
					<th>Alder / år</b></th>
					<th>std.-GFR / ml/min/1.73 m<sup>2</sup></th>
					<th>GFR / ml/min</th>
					<th>eGFR / ml/min/1.73 m<sup>2</sup></th>
					<th>Kreatinin &mu;mol/l</th>
					<th>Undersøgelsestype</th>
				</tr>
			<?php
			//create the tables containing previous measurements
				if(mysqli_num_rows($result_duplicates) >= 1){
					$accession_number_array = array();
					$GFR_values_array = array();
					while($row_duplicates = mysqli_fetch_array($result_duplicates)){
						array_push($accession_number_array, $row_duplicates['accession_number']);
					}
					for($i=0; $i<sizeof($accession_number_array);$i++){
						$query = "SELECT * FROM patient_values WHERE accession_number='" . $accession_number_array[$i] . "'";
						$result_patient_duplicates = mysqli_query($con, $query);
						$row_patient_duplicates = mysqli_fetch_array($result_patient_duplicates);
						$query_patient_info = "SELECT * FROM patient_data WHERE accession_number='" . $accession_number_array[$i] . "'";
						$result_patient_info = mysqli_query($con, $query_patient_info);
						$row_patient_info = mysqli_fetch_array($result_patient_info);
						$query_standard = "SELECT * FROM standards WHERE lot_number='" . $row_patient_duplicates['standard_lot_number'] . "'";
						$result_standard = mysqli_query($con, $query_standard);
						$row_standard = mysqli_fetch_array($result_standard);
						
						if($row_patient_info['measurement_type'] == 'B1' || $row_patient_info['measurement_type'] == 'V1'){
							$standard_counts_1_normalized = standard_counts($row_patient_duplicates['standard_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['standard_1_volume'],$row_patient_duplicates['standard_1_count_time']);
							$standard_counts_2_normalized = standard_counts($row_patient_duplicates['standard_2_counts'],$row_patient_duplicates['background_2_counts'],$row_patient_duplicates['standard_2_volume'],$row_patient_duplicates['standard_2_count_time']);
							$dose = calc_dose(calc_average($standard_counts_1_normalized, $standard_counts_2_normalized),$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_patient_duplicates['weight_full_syringe'],$row_patient_duplicates['weight_empty_syringe']);
							$time_from_injection_sample_1 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_1_time']);
							$time_from_injection_sample_2 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_2_time']);
							$counts_1_normalized = counts_per_ml($row_patient_duplicates['sample_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_1_volume'],$row_patient_duplicates['sample_1_count_time']);
							$counts_2_normalized = counts_per_ml($row_patient_duplicates['sample_2_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_2_volume'],$row_patient_duplicates['sample_2_count_time']);

							if($row_patient_info['measurement_type'] == 'B1'){
								$stdGFR_sample_1_old = calc_stdGFR($time_from_injection_sample_1,$counts_1_normalized,$dose,calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model),False);
								$stdGFR_sample_2_old = calc_stdGFR($time_from_injection_sample_2,$counts_2_normalized,$dose,calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model),False);
							} else {
								$stdGFR_sample_1_old = calc_stdGFR($time_from_injection_sample_1,$counts_1_normalized,$dose,calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model));
								$stdGFR_sample_2_old = calc_stdGFR($time_from_injection_sample_2,$counts_2_normalized,$dose,calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model));
							}
							$stdGFR_old = calc_average($stdGFR_sample_1_old,$stdGFR_sample_2_old);
							$GFR_old = $stdGFR_old*(calc_BSA($row_patient_info['patient_height'],$row_patient_info['patient_weight'],$BSA_model)/1.73);
							
						} elseif($row_patient_info['measurement_type'] == 'B2_24' || $row_patient_info['measurement_type'] == 'V3_24'){
							$standard_counts_1_normalized = standard_counts($row_patient_duplicates['standard_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['standard_1_volume'],$row_patient_duplicates['standard_1_count_time']);
							$standard_counts_2_normalized = standard_counts($row_patient_duplicates['standard_2_counts'],$row_patient_duplicates['background_2_counts'],$row_patient_duplicates['standard_2_volume'],$row_patient_duplicates['standard_2_count_time']);
							$dose_standard_1 = calc_dose($standard_counts_1_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_patient_duplicates['weight_full_syringe'],$row_patient_duplicates['weight_empty_syringe']);
							$dose_standard_2 = calc_dose($standard_counts_2_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_patient_duplicates['weight_full_syringe'],$row_patient_duplicates['weight_empty_syringe']);
							$time_from_injection_sample_1 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_1_time']);
							$time_from_injection_sample_2 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_2_time']);
							$time_from_injection_sample_3 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_3_time']);
							$time_from_injection_sample_4 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_4_time']);
							$counts_1_normalized = counts_per_ml($row_patient_duplicates['sample_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_1_volume'],$row_patient_duplicates['sample_1_count_time']);
							$counts_2_normalized = counts_per_ml($row_patient_duplicates['sample_2_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_2_volume'],$row_patient_duplicates['sample_2_count_time']);
							$counts_3_normalized = counts_per_ml($row_patient_duplicates['sample_3_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_3_volume'],$row_patient_duplicates['sample_3_count_time']);
							$counts_4_normalized = counts_per_ml($row_patient_duplicates['sample_4_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_4_volume'],$row_patient_duplicates['sample_4_count_time']);

							$x_values = array($time_from_injection_sample_1,$time_from_injection_sample_2,$time_from_injection_sample_3,$time_from_injection_sample_4);
							$y_values = array(log($counts_1_normalized),log($counts_2_normalized),log($counts_3_normalized),log($counts_4_normalized));
							
							$regression_values_old = linear_regression($x_values,$y_values);
							$regression_values_old[intercept] = exp($regression_values_old[intercept]);
							$area_under_curve = abs($regression_values_old[intercept]/$regression_values_old[slope]);
							$dose_auc = calc_average($dose_standard_1,$dose_standard_2)/$area_under_curve;
							$GFR_old = calc_GFR_multipoint($dose_auc, calc_BSA($patient_height, $patient_weight, $BSA_model));
							$stdGFR_old = $GFR_old * 1.73/calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model);
							
							
						} elseif($row_patient_info['measurement_type'] == 'V3' || $row_patient_info['measurement_type'] == 'B3'){
							$standard_counts_1_normalized = standard_counts($row_patient_duplicates['standard_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['standard_1_volume'],$row_patient_duplicates['standard_1_count_time']);
							$standard_counts_2_normalized = standard_counts($row_patient_duplicates['standard_2_counts'],$row_patient_duplicates['background_2_counts'],$row_patient_duplicates['standard_2_volume'],$row_patient_duplicates['standard_2_count_time']);
							$dose_standard_1 = calc_dose($standard_counts_1_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_patient_duplicates['weight_full_syringe'],$row_patient_duplicates['weight_empty_syringe']);
							$dose_standard_2 = calc_dose($standard_counts_2_normalized,$row_standard['diluted_volume'],$row_standard['standard_weight'],$row_patient_duplicates['weight_full_syringe'],$row_patient_duplicates['weight_empty_syringe']);
							$time_from_injection_sample_1 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_1_time']);
							$time_from_injection_sample_2 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_2_time']);
							$time_from_injection_sample_3 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_3_time']);
							$time_from_injection_sample_4 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_4_time']);
							$time_from_injection_sample_5 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_5_time']);
							$time_from_injection_sample_6 = datesub($row_patient_duplicates['actual_injection_time'],$row_patient_duplicates['sample_6_time']);
							$counts_1_normalized = counts_per_ml($row_patient_duplicates['sample_1_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_1_volume'],$row_patient_duplicates['sample_1_count_time']);
							$counts_2_normalized = counts_per_ml($row_patient_duplicates['sample_2_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_2_volume'],$row_patient_duplicates['sample_2_count_time']);
							$counts_3_normalized = counts_per_ml($row_patient_duplicates['sample_3_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_3_volume'],$row_patient_duplicates['sample_3_count_time']);
							$counts_4_normalized = counts_per_ml($row_patient_duplicates['sample_4_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_4_volume'],$row_patient_duplicates['sample_4_count_time']);
							$counts_5_normalized = counts_per_ml($row_patient_duplicates['sample_5_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_5_volume'],$row_patient_duplicates['sample_5_count_time']);
							$counts_6_normalized = counts_per_ml($row_patient_duplicates['sample_6_counts'],$row_patient_duplicates['background_1_counts'],$row_patient_duplicates['sample_6_volume'],$row_patient_duplicates['sample_6_count_time']);
							
							$x_values = array($time_from_injection_sample_1,$time_from_injection_sample_2,$time_from_injection_sample_3,$time_from_injection_sample_4,$time_from_injection_sample_5,$time_from_injection_sample_6);
							$y_values_log = array(log($counts_1_normalized),log($counts_2_normalized),log($counts_3_normalized),log($counts_4_normalized),log($counts_5_normalized),log($counts_6_normalized));
							$y_values = array($counts_1_normalized,$counts_2_normalized,$counts_3_normalized,$counts_4_normalized,$counts_5_normalized,$counts_6_normalized);
		
							$regression_values_old = linear_regression($x_values,$y_values_log);
							$intercept_old = $regression_values_old[intercept];
							$slope_old = $regression_values_old[slope];
							$regression_values_old[intercept] = exp($regression_values_old[intercept]);
							$area_under_curve = abs($regression_values_old[intercept]/$regression_values_old[slope]);
							$dose_auc = calc_average($dose_standard_1,$dose_standard_2)/$area_under_curve;
							$GFR_old = calc_GFR_multipoint($dose_auc, calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model));
							$stdGFR_old = $GFR_old * 1.73/calc_BSA($row_patient_info['patient_height'], $row_patient_info['patient_weight'], $BSA_model);

						} else {
							$error_meas_type = True;
						}

						$query = "SELECT * FROM patient_data WHERE accession_number = '" . $row_patient_duplicates['accession_number'] . "'";
						$result_patient_duplicates_data = mysqli_query($con, $query);
						$row_patient_duplicates_data = mysqli_fetch_array($result_patient_duplicates_data);
						$age_old = calc_age($row_patient_duplicates_data['patient_birthday'],$row_patient_duplicates_data['patient_exam_date'],'%Y');
						if(!is_infinite($stdGFR_old) && !is_nan($stdGFR_old) && $stdGFR_old > 0){
							//save GFR-values for history plot
							array_push($history_array, array('stdGFR'=>round($stdGFR_old,0), 'GFR'=>round($GFR_old,0), 'patient_exam_date'=>$row_patient_duplicates_data['patient_exam_date'], 'patient_birthday'=>$row_patient_duplicates_data['patient_birthday']));
						?>
							<tr>
								<td align="center"><?php echo $row_patient_duplicates_data['patient_exam_date'];?></td>
								<td align="center"><?php echo number_format(calc_age($row_patient_duplicates_data['patient_birthday'], $row_patient_duplicates_data['patient_exam_date'], '%a')/365.25,2,'.','')?></td>
								<td align="center"><?php echo round($stdGFR_old,0);?></td>
								<td align="center"><?php echo round($GFR_old,0);?></td>
								<td align="center"><?php echo calc_GFR_CKD_EPI_CKiD($row_patient_duplicates_data['patient_s_crea'], $age_old, $patient_sex, $row_patient_duplicates_data['patient_height']);?></td>
								<td align="center"><?php echo $row_patient_duplicates_data['patient_s_crea'];?></td>
								<td align="center"><?php echo $row_patient_duplicates_data['measurement_type'];?></td>
							</tr>
							<?php
						} elseif($error_meas_type){
							?>
							<tr>
								<td align="center"><?php echo $row_patient_duplicates_data['patient_exam_date'];?></td>
								<td colspan="6">Fejl. Ingen måletype defineret på undersøgelsesdato.</td>
							</tr>
							<?php
						} else {
							$inf_value = True;
							?>
								<tr>
									<td align="center"><?php echo $row_patient_duplicates_data['patient_exam_date'];?></td>
									<td colspan="5">Ikke beregnet pga. manglende data i arbejdsark</td>
									<td align="center"><?php echo $row_patient_duplicates_data['measurement_type'];?></td>
								</tr>
							<?php
							}
					}
				} elseif(mysqli_num_rows($result_duplicates) < 1) {
					echo '<tr><td colspan="7">Der findes ingen tidligere målinger på ' . name_syntax($patient_name) . '</td></tr>';
				}
				
				//push the current measurement to the previous measurement array for comparison
                if(!is_infinite($stdGFR) && !is_nan($stdGFR)){
					array_push($history_array, array('stdGFR'=>round($stdGFR,0), 'GFR'=>round($stdGFR*calc_BSA($patient_height, $patient_weight, $BSA_model)/1.73,0), 'patient_exam_date'=>$patient_exam_date, 'patient_birthday'=>$patient_birthday));
				}
				//sort and reverse $history_array for dygraphs plotting speedup;
				usort($history_array, build_sorter('patient_exam_date'));
				array_reverse($history_array);
				#print_r($history_array);
				?>
			</table>
				
			<table class="sub_worksheet">
				<tr>
					<td align="center">
						<?php dygraph_history($history_array);?>
					</td>
				</tr>
			</table>
			
			</div>
			<!--end previous measurements-->
								
			<!--fit statistics-->
			<?php
				if($measurement_type == 'V3_24' || $measurement_type == 'B2_24' || $measurement_type == 'B3' || $measurement_type == 'V3'){
			?>
					<h3><a class="history" href="javascript:toggle('statistics')">Vis/skjul statistik</a></h3>
					<div id="statistics" style="display:block;page-break-before:always;">
						<?php 
						//regression for fitting
						$regression = array('slope'=>$slope,'intercept'=>$intercept,'r_squared'=>$r_squared);
						?>
						<table class="sub_worksheet">
							<tr>
								<td align="center">
									<?php dygraph_regression($x_values_stat,$y_values_stat,$regression);?>
								</td>
							</tr>
						</table>
					</div>
			<?php
				}
			?>
		</div>
		 
		<?php	
		echo(html_footer());
}
?>
