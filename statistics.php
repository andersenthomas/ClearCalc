<?php
/*
 * add_standard.php
 */
//require('user_authentication.php');
include('common_functions.php');
$config_parameters = parse_ini_file("../../config_clearance.ini", true);

//configuration of database
$db_host = $config_parameters['database_setup']['host'];
$db_user = $config_parameters['database_setup']['user'];
$db_password = $config_parameters['database_setup']['password'];
$db = $config_parameters['database_setup']['database'];

$connection_error = False;
$con = mysqli_connect($db_host,$db_user,$db_password,$db);

// Check connection
if (mysqli_connect_errno()){
    $connection_error = True;
    $error_message = '<div class="body_text">Failed to connect to MySQL: ' . mysqli_connect_error() . ' </div>';
}

/* like min() but casts to int and ignores 0 */
function min_not_null(Array $values){
	return min(array_diff(array_map('intval', $values), array(0)));
}

/* returns standard deviation */
function stddev($array, $sample = true){
	if(sizeof($array) == 1){
		$value = 'NA';
	} else {
		$mean = array_sum($array)/count($array);
		$variance = 0.0;
		foreach($array as $i){
			$variance += pow($i-$mean,2);
		}
		$variance /= ($sample ? count($array) - 1 : count($array));
		$value = round((float)sqrt($variance),1);
	}
	return $value;
}

echo(html_header());
if($connection_error){
    echo $error_message;
    echo html_footer();
} else {
				// get all patients and unique patients
        		$query = "SELECT COUNT(DISTINCT patient_cpr), count(*) from patient_data";
				$tot_unique_patients = single_sql_value($con, $query, 0);
				$tot_patients = single_sql_value($con, $query, 1);        
                		
				$query = "SELECT COUNT(DISTINCT patient_cpr) FROM patient_data WHERE MOD(patient_cpr,2)=1";
				$tot_males = single_sql_value($con, $query, 0);
				$tot_females = $tot_unique_patients - $tot_males;

				$query = "SELECT MAX(patients_per_day), AVG(patients_per_day) FROM (SELECT COUNT(*) AS patients_per_day FROM patient_data GROUP BY DATE(patient_data.patient_exam_date)) AS counts";
				$max_per_day = single_sql_value($con, $query, 0);
				$avg_per_day = single_sql_value($con, $query, 1);
				
				$query = "SELECT measurement_type, COUNT(*) as count FROM `patient_data` WHERE measurement_type != 'NULL' GROUP BY measurement_type ORDER BY count DESC";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$meas_type[] = array('meas_type' => $row['measurement_type'], 'count' => $row['count']);
				}
				mysqli_free_result($result);
				
				$query = "SELECT YEAR(patient_exam_date) as time, COUNT(*) as n_patients FROM patient_data GROUP BY YEAR(patient_exam_date)";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_patients_year[] = array('time' => $row['time'], 'n_patients' => $row['n_patients']);
				}
				mysqli_free_result($result);
			
				$query = "SELECT YEAR(patient_exam_date) as year, MONTH(patient_exam_date) as month, COUNT(id) as npatients FROM patient_data WHERE YEAR(patient_exam_date)='2016' GROUP BY MONTH(patient_exam_date)";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_patients_2016[] = array('year' => $row['year'], 'month' => $row['month'], 'npatients' => $row['npatients']);
				}
				mysqli_free_result($result);
				
				$query = "SELECT YEAR(patient_exam_date) as year, MONTH(patient_exam_date) as month, COUNT(id) as npatients FROM patient_data WHERE YEAR(patient_exam_date)='2015' GROUP BY MONTH(patient_exam_date)";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_patients_2015[] = array('year' => $row['year'], 'month' => $row['month'], 'npatients' => $row['npatients']);
				}
				mysqli_free_result($result);
				
				$query = "SELECT YEAR(patient_exam_date) as year, MONTH(patient_exam_date) as month, COUNT(id) as npatients FROM patient_data WHERE YEAR(patient_exam_date)='2014' GROUP BY MONTH(patient_exam_date)";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_patients_2014[] = array('year' => $row['year'], 'month' => $row['month'], 'npatients' => $row['npatients']);
				}
				mysqli_free_result($result);
				
				$query = "SELECT MONTH(patient_exam_date) as month, DAY(patient_exam_date) as day, COUNT(*) as count FROM patient_data WHERE YEAR(patient_exam_date) ='2016' GROUP BY DATE_FORMAT(patient_exam_date, '%m%d') ORDER BY patient_exam_date ASC";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_avg_patients_2016[] = array('month' => $row['month'], 'day' => $row['day'], 'count' => $row['count']);
				}
				mysqli_free_result($result);
				
				$days_pr_month_16 = array_count_values(array_map(function($foo){return $foo['month'];}, $n_avg_patients_2016));
				$patients_pr_month_16 = array();
				foreach($n_avg_patients_2016 as $pair){
					$patients_pr_month_16[$pair['month']] += $pair['count'];
				}
				$patients_pr_day_16 = array();		
				for($i=1;$i<=sizeof($patients_pr_month_16);$i++){
					$patients_pr_day_16[$i] = $patients_pr_month_16[$i]/$days_pr_month_16[$i];
				}
				
				$query = "SELECT MONTH(patient_exam_date) as month, DAY(patient_exam_date) as day, COUNT(*) as count FROM patient_data WHERE YEAR(patient_exam_date) ='2015' GROUP BY DATE_FORMAT(patient_exam_date, '%m%d') ORDER BY patient_exam_date ASC";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_avg_patients_2015[] = array('month' => $row['month'], 'day' => $row['day'], 'count' => $row['count']);
				}
				mysqli_free_result($result);
				
				$days_pr_month_15 = array_count_values(array_map(function($foo){return $foo['month'];}, $n_avg_patients_2015));
				$patients_pr_month_15 = array();
				foreach($n_avg_patients_2015 as $pair){
					$patients_pr_month_15[$pair['month']] += $pair['count'];
				}
				$patients_pr_day_15 = array();		
				for($i=1;$i<=sizeof($patients_pr_month_15);$i++){
					$patients_pr_day_15[$i] = $patients_pr_month_15[$i]/$days_pr_month_15[$i];
				}
				
				$query = "SELECT MONTH(patient_exam_date) as month, DAY(patient_exam_date) as day, COUNT(*) as count FROM patient_data WHERE YEAR(patient_exam_date) ='2014' GROUP BY DATE_FORMAT(patient_exam_date, '%m%d') ORDER BY patient_exam_date ASC";
				$result = mysqli_query($con,$query);
				while($row = mysqli_fetch_array($result)){
					$n_avg_patients_2014[] = array('month' => $row['month'], 'day' => $row['day'], 'count' => $row['count']);
				}
				mysqli_free_result($result);
				
				$days_pr_month_14 = array_count_values(array_map(function($foo){return $foo['month'];}, $n_avg_patients_2014));
				$patients_pr_month_14 = array();
				foreach($n_avg_patients_2014 as $pair){
					$patients_pr_month_14[$pair['month']] += $pair['count'];
				}
				$patients_pr_day_14 = array();		
				for($i=1;$i<=sizeof($patients_pr_month_14);$i++){
					$patients_pr_day_14[$i] = $patients_pr_month_14[$i]/$days_pr_month_14[$i];
				}
								
				$query = "SELECT COUNT(*) as counts, patient_exam_date FROM patient_data WHERE YEAR(patient_exam_date)>2013 GROUP BY DATE(patient_data.patient_exam_date)";
				$result = mysqli_query($con, $query);
				$counts = array();
				$dates = array();
				

				while($row = mysqli_fetch_array($result)){
					$counts[] = $row['counts'];
					$dates[] = $row['patient_exam_date'];
				}
				
				for($i=0;$i<sizeof($dates);$i++){
					$dates[$i] = str_replace('-', '/', $dates[$i]);
				}
				
				$x_regression = array();
				array_push($x_regression,0);
				for($i=0;$i<sizeof($dates)-1;$i++){
					$tmpdate1 = date_create($dates[0]);
					$tmpdate2 = date_create($dates[$i+1]);
					$diff = date_diff($tmpdate1,$tmpdate2);
					$diff_formatted = $diff->format("%a");
					array_push($x_regression,$diff_formatted);
				}
				$fit_count = linear_regression($x_regression,$counts);
				$yfit = array();
				foreach($x_regression as $key=>$value){
					$fit_value = $fit_count['slope']*$value+$fit_count['intercept'];
					array_push($yfit,$fit_value);
				}
				$datastring = '';
				for($i=0;$i<sizeof($dates);$i++){
					if ($i==sizeof($dates)-1){
						$datastring .= '"'.$dates[$i].','.$counts[$i].','.$yfit[$i].'\n",';
					} else {
						$datastring .= '"'.$dates[$i].','.$counts[$i].','.$yfit[$i].'\n" +';
					} 
				}
				
				$year_array = array($n_patients_year[1]['time'], $n_patients_year[2]['time'], $n_patients_year[3]['time']);
				$counts_array = array($n_patients_year[1]['n_patients'], $n_patients_year[2]['n_patients'], $n_patients_year[3]['n_patients']);
				
				echo '<div class="datacontainer">';
				echo '<div class="body_text">';
				echo 'Der er ' . $tot_patients . ' patienter i databasen, hvoraf ' . $tot_unique_patients . ' er unikke. Af disse er ' . $tot_males . ' mænd og ' . $tot_females . ' kvinder.<br/>';
				echo 'Det maksimale antal patienter per dag er ' . $max_per_day . ', mens gennemsnittet per dag (hvor der er patienter booket) er ' . $avg_per_day . '.<br/><br/>';
				
				echo '<div align="left">';
				echo "<div class=\"chart\" id=\"dygraphstatisticsyear\" style=\"width:800px;height:600px;\"></div>\n";
				echo '<script type="text/javascript">
							g = new Dygraph(document.getElementById("dygraphstatisticsyear"),
							"år,antal\n" + ';
				echo '		"' . $year_array[0] .',' . $counts_array[0] . '\n"+';
				echo '		"' . $year_array[1] .',' . $counts_array[1] . '\n",';
				#echo '		"' . $year_array[2] .',' . $counts_array[2] . '\n",';
				echo '{
						legend: \'always\',
						valueRange: ['.$ymin.','.$ymax.'],
						xlabel: \'år\',
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
								color: \'black\',
								drawPoints: true,
								pointSize:4,
								strokeWidth: 1.2,
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
						rollPeriod: 1,
						showRoller: true,
						title: \'\',
						digitsAfterDecimal: 5,
					  });
				</script>';
				echo '</div>';
				
				echo '<div align="left">';
				echo "<div class=\"chart\" id=\"dygraphstatistics\" style=\"width:800px;height:600px;\"></div>\n";
				echo '<script type="text/javascript">
							g = new Dygraph(document.getElementById("dygraphstatistics"),
							"date,antal,fit\n" + ';
				echo $datastring;
				echo '{
						legend: \'always\',
						valueRange: ['.$ymin.','.$ymax.'],
						xlabel: \'dato\',
						ylabel: \'antal patienter pr. dag\',
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
								color: \'black\',
								drawPoints: false,
								strokeWidth: 1.2,
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
						rollPeriod: 1,
						showRoller: true,
						title: \'\',
						digitsAfterDecimal: 5,
					  });
				</script>';
				echo '</div>';
				
				echo '<p>Regressionsparametre:</p>';
				echo '<table border="1" cellpadding="5px" style="border-collapse:collapse;">
						<tr><td align="center">hældning (stigning antal patienter/dag)</td><td align="center">skæring (antal patienter)</td><td align="center">R<sup>2</sup></td><td align="center">r</td></tr>
						<tr><td>'.$fit_count['slope'].'</td><td>'.$fit_count['intercept'].'</td><td>'.r_squared($x_regression, $counts, $fit_count['slope'], $fit_count['intercept']).'</td><td>'.coeff_deter($x_regression, $counts, $fit_count['slope'], $fit_count['intercept']).'</td></tr>
					  </table>';
					  
				echo '<p></p>';
				
				echo '<table border="1" cellpadding="5px" style="border-collapse:collapse;">';
				echo '<tr><th align="left">Måned/år</th><th align="left">2016 (patienter/dag)</th><th align="left">2015 (patienter/dag)</th><th align="left">2014 (patienter/dag)</th></tr>';
				$sum_month_2016 = array();
				$sum_month_2015 = array();
				$sum_month_2014 = array();
				$avg_day_2016 = array();
				$avg_day_2015 = array();
				$avg_day_2014 = array();
				for($i=0;$i<12;$i++){
					$month_number = $i + 1;
					echo '<tr>';
					echo '<td>' . $month_number . '</td><td>' . $n_patients_2016[$i]['npatients'] . ' (' . number_format($patients_pr_day_16[$month_number],2) . ') </td><td>' . $n_patients_2015[$i]['npatients'] . ' (' . number_format($patients_pr_day_15[$month_number],2) . ')</td><td>' . $n_patients_2014[$i]['npatients'] .  ' (' . number_format($patients_pr_day_14[$month_number],2) . ')</td>';
					echo '</tr>';
					
					array_push($sum_month_2016, $n_patients_2016[$i]['npatients']);
					array_push($sum_month_2015, $n_patients_2015[$i]['npatients']);
					array_push($sum_month_2014, $n_patients_2014[$i]['npatients']);
					array_push($avg_day_2016, $patients_pr_day_16[$month_number]);
					array_push($avg_day_2015, $patients_pr_day_15[$month_number]);
					array_push($avg_day_2014, $patients_pr_day_14[$month_number]);
					
				}
				$sum_month_2016 = array_filter($sum_month_2016);
				$sum_month_2015 = array_filter($sum_month_2015);
				$sum_month_2014 = array_filter($sum_month_2014);
				$avg_day_2016 = array_filter($avg_day_2016);
				$avg_day_2015 = array_filter($avg_day_2015);
				$avg_day_2014 = array_filter($avg_day_2014);
				
				echo '<tr><td><b>Middelværdi</b></td><td>' . round(array_sum($sum_month_2016)/count($sum_month_2016),1) . ' (' . round(array_sum($avg_day_2016)/count($avg_day_2016),2) . ')</td><td>' . round(array_sum($sum_month_2015)/count($sum_month_2015),1) . ' (' . round(array_sum($avg_day_2015)/count($avg_day_2015),2) . ')</td><td>' . round(array_sum($sum_month_2014)/count($sum_month_2014),1) . ' (' . round(array_sum($avg_day_2014)/count($avg_day_2014),2) . ')</td></tr>';
				echo '<tr><td><b>Range</b></td><td>' . max($sum_month_2016) . ' - ' . min_not_null($sum_month_2016) . ' (' . round(max($avg_day_2016),2) . ' - ' . number_format(min($avg_day_2016),2) . ')</td><td>' . max($sum_month_2015) . ' - ' . min($sum_month_2015) . ' (' . round(max($avg_day_2015),2) . ' - ' . number_format(min($avg_day_2015),2) . ')</td><td>' . max($sum_month_2014) . ' - ' . min_not_null($sum_month_2014) . ' (' . round(max($avg_day_2014),2) . ' - ' . number_format(min($avg_day_2014),2) . ')</td></tr>';
				echo '<tr><td><b>&sigma;</b></td><td>' . stddev($sum_month_2016) . ' (' . stddev($avg_day_2016) . ')</td><td>' . stddev($sum_month_2015) . ' (' . stddev($avg_day_2015) . ')</td><td>' . stddev($sum_month_2014) . ' (' . stddev($avg_day_2014) . ')</td></tr>';
				echo '<tr><td><b>Total</b></td><td>' . round(array_sum($sum_month_2016),1) . '</td><td>' . round(array_sum($sum_month_2015),1) . '</td><td>' . round(array_sum($sum_month_2014),1) . '</td></tr>';
				echo '</table>';
				
				echo '<p></p>';
				
				echo '<table border="1" cellpadding="5px" style="border-collapse:collapse;">';
				echo '<tr><th align="left">Undersøgelsestype</th><th align="left">Antal</th></tr>';
				foreach($meas_type as $type){
					echo '<tr>';
					echo '<td>' . $type['meas_type'] . '</td><td>' . $type['count'] . '</td>';
					echo '</tr>';				
				}
				echo '</table>';
				
				echo '</div>';
				echo '</div>';

    echo(html_footer());
}

?>
