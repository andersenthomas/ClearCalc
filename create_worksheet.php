<?php
/*
 * create_worksheet.php
 */
//require('user_authentication.php');
include('common_functions.php');
$config_parameters = parse_ini_file("../../config_clearance.ini", true);

//configuration of database
$db_host = $config_parameters['database_setup']['host'];
$db_user = $config_parameters['database_setup']['user'];
$db_password = $config_parameters['database_setup']['password'];
$db = $config_parameters['database_setup']['database'];

//model parameters
$BSA_model = $config_parameters['model_parameters']['BSA_model'];
$count_time_short = $config_parameters['counting_parameters']['counting_time_short'];
$count_time_long = $config_parameters['counting_parameters']['counting_time_long'];

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
	
				if(isset($_GET['page'])){
					$page = $_GET['page'];
				} else {
					$page = 1;
				}
				$id = $_GET['id'];
				$measurement_type = $_GET['measurement_type'];
				$patient_cpr = $_GET['patient_cpr'];
				$patient_name = $_GET['patient_name'];
				$patient_injection_time = $_GET['patient_injection_time'];
				$patient_height = $_GET['patient_height'];
				$patient_weight = $_GET['patient_weight'];
				$patient_sex = $_GET['patient_sex'];
				$patient_s_crea = $_GET['patient_s_crea'];
				$patient_exam_date = $_GET['patient_exam_date'];
				$department = $_GET['department'];
				$accession_number = $_GET['accession_number'];
				$page_url = 'http://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
				if($patient_injection_time == ''){
					$patient_injection_time = $patient_exam_date;
				}
        
            if(isset($_POST['submit'])){
					$id = $_GET['id'];

					$result_link = mysqli_query($con, "select * from patient_data where id='$id'");
					$row_link = mysqli_fetch_array($result_link);
				
					//$measurement_type = $_POST['measurement_type'];
                    $bioanalyst = $_POST['bioanalyst'];
                    $activity_syringe = $_POST['activity_syringe'];
                    $activity_syringe = str_replace(",", ".", $activity_syringe); //replace , for . to adhere to MySQL standards
                    $weight_full_syringe = $_POST['weight_full_syringe'];
                    $weight_full_syringe = str_replace(",", ".", $weight_full_syringe); //replace , for . to adhere to MySQL standards
                    $weight_empty_syringe = $_POST['weight_empty_syringe'];
                    $weight_empty_syringe = str_replace(",", ".", $weight_empty_syringe); //replace , for . to adhere to MySQL standards
                    $actual_injection_time = $_POST['actual_injection_time'];
                    
                    
                    $sample_0_time = $_POST['sample_0_time'];
                    $sample_1_time = $_POST['sample_1_time'];
                    $sample_2_time = $_POST['sample_2_time'];
                    $sample_3_time = $_POST['sample_3_time'];
                    $sample_4_time = $_POST['sample_4_time'];
                    $sample_5_time = $_POST['sample_5_time'];
                    $sample_6_time = $_POST['sample_6_time'];
                    
                    $sample_0_volume = $_POST['sample_0_volume'];
                    $sample_1_volume = $_POST['sample_1_volume'];
                    $sample_2_volume = $_POST['sample_2_volume'];
                    $sample_3_volume = $_POST['sample_3_volume'];
                    $sample_4_volume = $_POST['sample_4_volume'];
                    $sample_5_volume = $_POST['sample_5_volume'];
                    $sample_6_volume = $_POST['sample_6_volume'];
                    
                    $injection_volume = $_POST['injection_volume'];
                    $background_1_volume = $_POST['background_1_volume'];
                    $background_2_volume = $_POST['background_2_volume'];
                    $standard_1_volume = $_POST['standard_1_volume'];
                    $standard_2_volume = $_POST['standard_2_volume'];
                    
                    $background_1_counts = $_POST['background_1_counts'];
                    $background_2_counts = $_POST['background_2_counts'];
                    $standard_1_counts = $_POST['standard_1_counts'];
                    $standard_2_counts = $_POST['standard_2_counts'];
                    $sample_0_counts = $_POST['sample_0_counts'];
                    $sample_1_counts = $_POST['sample_1_counts'];
                    $sample_2_counts = $_POST['sample_2_counts'];
                    $sample_3_counts = $_POST['sample_3_counts'];
                    $sample_4_counts = $_POST['sample_4_counts'];
                    $sample_5_counts = $_POST['sample_5_counts'];
                    $sample_6_counts = $_POST['sample_6_counts'];
                    
                    $background_1_count_time = $_POST['background_1_count_time'];
                    $background_2_count_time = $_POST['background_2_count_time'];
                    $standard_1_count_time = $_POST['standard_1_count_time'];
                    $standard_2_count_time = $_POST['standard_2_count_time'];
                    $sample_0_count_time = $_POST['sample_0_count_time'];
                    $sample_1_count_time = $_POST['sample_1_count_time'];
                    $sample_2_count_time = $_POST['sample_2_count_time'];
                    $sample_3_count_time = $_POST['sample_3_count_time'];
                    $sample_4_count_time = $_POST['sample_4_count_time'];
                    $sample_5_count_time = $_POST['sample_5_count_time'];
                    $sample_6_count_time = $_POST['sample_6_count_time'];
                    
                    $standard_lot_number = $_POST['standard_lot_number'];
                    
                    if($measurement_type == 'V1' ||  $measurement_type == 'B1'){
						$query = "update patient_values set bioanalyst='$bioanalyst', activity_syringe='$activity_syringe', weight_full_syringe='$weight_full_syringe', weight_empty_syringe='$weight_empty_syringe', actual_injection_time='$actual_injection_time', sample_0_time='$sample_0_time', sample_1_time='$sample_1_time', sample_2_time='$sample_2_time', sample_0_volume='$sample_0_volume', sample_1_volume='$sample_1_volume', sample_2_volume='$sample_2_volume', injection_volume='$injection_volume', background_1_volume='$background_1_volume', background_2_volume='$background_2_volume', standard_1_volume='$standard_1_volume', standard_2_volume='$standard_2_volume', background_1_counts='$background_1_counts', background_2_counts='$background_2_counts', standard_1_counts='$standard_1_counts', standard_2_counts='$standard_2_counts', sample_0_counts='$sample_0_counts', sample_1_counts='$sample_1_counts', sample_2_counts='$sample_2_counts', background_1_count_time='$background_1_count_time', background_2_count_time='$background_2_count_time', standard_1_count_time='$standard_1_count_time', standard_2_count_time='$standard_2_count_time', sample_0_count_time='$sample_0_count_time', sample_1_count_time='$sample_1_count_time', sample_2_count_time='$sample_2_count_time', standard_lot_number='$standard_lot_number' where accession_number='$accession_number'";
					} elseif($measurement_type == 'V3_24' ||  $measurement_type == 'B2_24') {
						$query = "update patient_values set bioanalyst='$bioanalyst', activity_syringe='$activity_syringe', weight_full_syringe='$weight_full_syringe', weight_empty_syringe='$weight_empty_syringe', actual_injection_time='$actual_injection_time', sample_0_time='$sample_0_time', sample_1_time='$sample_1_time', sample_2_time='$sample_2_time', sample_3_time='$sample_3_time', sample_4_time='$sample_4_time', sample_0_volume='$sample_0_volume', sample_1_volume='$sample_1_volume', sample_2_volume='$sample_2_volume', sample_3_volume='$sample_3_volume', sample_4_volume='$sample_4_volume', injection_volume='$injection_volume', background_1_volume='$background_1_volume', background_2_volume='$background_2_volume', standard_1_volume='$standard_1_volume', standard_2_volume='$standard_2_volume', background_1_counts='$background_1_counts', background_2_counts='$background_2_counts', standard_1_counts='$standard_1_counts', standard_2_counts='$standard_2_counts', sample_0_counts='$sample_0_counts', sample_1_counts='$sample_1_counts', sample_2_counts='$sample_2_counts', sample_3_counts='$sample_3_counts', sample_4_counts='$sample_4_counts', background_1_count_time='$background_1_count_time', background_2_count_time='$background_2_count_time', standard_1_count_time='$standard_1_count_time', standard_2_count_time='$standard_2_count_time', sample_0_count_time='$sample_0_count_time', sample_1_count_time='$sample_1_count_time', sample_2_count_time='$sample_2_count_time', sample_3_count_time='$sample_3_count_time', sample_4_count_time='$sample_4_count_time', standard_lot_number='$standard_lot_number' where accession_number='$accession_number'";		
					} elseif($measurement_type == 'B3' || $measurement_type == 'V3') {
						$query = "update patient_values set bioanalyst='$bioanalyst', activity_syringe='$activity_syringe', weight_full_syringe='$weight_full_syringe', weight_empty_syringe='$weight_empty_syringe', actual_injection_time='$actual_injection_time', sample_0_time='$sample_0_time', sample_1_time='$sample_1_time', sample_2_time='$sample_2_time', sample_3_time='$sample_3_time', sample_4_time='$sample_4_time', sample_5_time='$sample_5_time', sample_6_time='$sample_6_time', sample_0_volume='$sample_0_volume', sample_1_volume='$sample_1_volume', sample_2_volume='$sample_2_volume', sample_3_volume='$sample_3_volume', sample_4_volume='$sample_4_volume', sample_5_volume='$sample_5_volume', sample_6_volume='$sample_6_volume', injection_volume='$injection_volume', background_1_volume='$background_1_volume', background_2_volume='$background_2_volume', standard_1_volume='$standard_1_volume', standard_2_volume='$standard_2_volume', background_1_counts='$background_1_counts', background_2_counts='$background_2_counts', standard_1_counts='$standard_1_counts', standard_2_counts='$standard_2_counts', sample_0_counts='$sample_0_counts', sample_1_counts='$sample_1_counts', sample_2_counts='$sample_2_counts', sample_3_counts='$sample_3_counts', sample_4_counts='$sample_4_counts', sample_5_counts='$sample_5_counts', sample_6_counts='$sample_6_counts', background_1_count_time='$background_1_count_time', background_2_count_time='$background_2_count_time', standard_1_count_time='$standard_1_count_time', standard_2_count_time='$standard_2_count_time', sample_0_count_time='$sample_0_count_time', sample_1_count_time='$sample_1_count_time', sample_2_count_time='$sample_2_count_time', sample_3_count_time='$sample_3_count_time', sample_4_count_time='$sample_4_count_time', sample_5_count_time='$sample_5_count_time', sample_6_count_time='$sample_6_count_time', standard_lot_number='$standard_lot_number' where accession_number='$accession_number'";
					}
                    $result_values = mysqli_query($con, $query);
                    echo $query;

                    $patient_weight = $_POST['patient_weight'];
                    $patient_weight = replace_comma($patient_weight);
                    $patient_height = $_POST['patient_height'];
                    $patient_height = replace_comma($patient_height);
                    $patient_s_crea = $_POST['patient_s_crea'];
                    $patient_s_crea = replace_comma($patient_s_crea);
                    $patient_s_crea_date = $_POST['patient_s_crea_date'];
                    $patient_tracer = $_POST['patient_tracer'];
                    $department = $_POST['department'];
                    
                    $query_patient_update = "update patient_data set measurement_type='$measurement_type', department='$department', patient_weight='$patient_weight', patient_height='$patient_height', patient_s_crea='$patient_s_crea', patient_s_crea_date='$patient_s_crea_date', patient_tracer='$patient_tracer' where accession_number='$accession_number'";
                    $result_values_patient = mysqli_query($con, $query_patient_update);

                    if($result_values && $result_values_patient){
                        header('location:create_worksheet.php?id=' . $id . '&measurement_type=' . $measurement_type . '&patient_cpr=' . $row_link["patient_cpr"] . '&patient_injection_time=' . $row_link["patient_injection_time"] . '&patient_s_crea=' . $row_link["patient_s_crea"] . '&patient_height=' . $row_link["patient_height"] . '&patient_weight=' . $row_link["patient_weight"] . '&department=' . $row_link["department"] . '&patient_name=' . $row_link["patient_name"] . '&patient_sex=' . $row_link["patient_sex"] . '&patient_exam_date=' . $row_link["patient_exam_date"] . '&accession_number=' . $row_link["accession_number"]);
                    } else {
                        echo mysqli_errno($con);
                        echo '<br>';
                        echo mysqli_error($con);
                    }
                    
					
            }
             
            //submit previous GFR measurement				
			if(isset($_POST['previous_patient_submit'])){
											
				$previous_patient_date = $_POST['previous_patient_date'];
				$previous_patient_gfr = $_POST['previous_patient_gfr'];
				$previous_department = $_POST['previous_department'];
				$previous_measurement_type = $_POST['previous_measurement_type'];
					
				$creation_date = date('Y-m-d H:i:s');
									
				$query_previous_patient = "INSERT INTO previous_patient_data SET patient_name='$patient_name', patient_exam_date='$previous_patient_date', patient_gfr='$previous_patient_gfr', department='$previous_department', measurement_type='$previous_measurement_type', creation_date='$creation_date', patient_cpr='$patient_cpr'";
				$result_previous_patient = mysqli_query($con, $query_previous_patient);
	
				if($result_previous_patient){
					header('location: ' . $_SERVER['REQUEST_URI']);
				} else {
					echo "Ups! Dette er pinligt - der er sket en fejl...";
				}
			}
            
            if(!empty($id)){
				if(empty($measurement_type)){
					$measurement_type = 'V1';
				}
				
                //find the specific measurement data from the patient
                $result = mysqli_query($con, "select * from patient_data where id='$id'");
                $row = mysqli_fetch_array($result);
                
                //find measured data from the patient examination. accession_number is unique so no reason for "limit 1"
                $result_examination = mysqli_query($con, "select * from patient_values where accession_number='$accession_number'");
                $row_examination = mysqli_fetch_array($result_examination);
                
                //find previous measurements from patient
                $result_duplicates = mysqli_query($con, "select * from patient_data where patient_cpr='$patient_cpr' and (patient_exam_date < '$patient_exam_date' or patient_exam_date > '$patient_exam_date') ORDER BY patient_exam_date DESC");
                
                //find newest standard
                $result_standard = mysqli_query($con, "select * from standards order by standard_date desc");
                
                //reused parameters
                $patient_age = number_format(calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a')/365.25,2,'.','');
                
                ?>

                <div class="datacontainer">
                    <form method="post" action="">
                                    <!--header info-->
                                    <table class="submit_table">
										<tr>
											<td colspan="2" align="right">
												<input type="button" class="button" value="Patientliste" onclick="window.location.href='list_patients.php?page=<?php echo $page;?>'">
												<input type="submit" name="submit" value="Gem arbejdsarkdata">
												<input type="button" class="button" value="Svarark" onclick="window.location.href='create_answer.php?page=<?php echo $page;?>&id=<?php echo($row['id']);?>&measurement_type=<?php echo($row['measurement_type']);?>&patient_cpr=<?php echo($row['patient_cpr']);?>&patient_injection_time=<?php echo($row['patient_injection_time']);?>&patient_s_crea=<?php echo($row['patient_s_crea']);?>&patient_height=<?php echo($row['patient_height']);?>&patient_weight=<?php echo($row['patient_weight']);?>&department=<?php echo($row['department']);?>&patient_name=<?php echo($row['patient_name']);?>&patient_sex=<?php echo($row['patient_sex']);?>&patient_exam_date=<?php echo($row['patient_exam_date']);?>&accession_number=<?php echo($row['accession_number']);?>'">
											</td>
										</tr>
									</table>
									<table class="sub_worksheet">
                                        <tr>
                                            <td width="50%"><h3><sup>51</sup>Cr-EDTA Clearance</h3></td>
                                            <td align="right"><b>Odense Universitetshospital</b><br>Nuklearmedicinsk afdeling</td>
                                        </tr>
                                        <tr>
                                            <td width="50%">Måletype: <?php echo type_converter($row['measurement_type'])?></td>
                                            <td align="right">Henvisende afdeling: <input type="text" size="8" name="department" tabindex="2" value="<?php echo $row['department'];?>"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="right">Arbejdsark dannet d. <?php echo get_current_date('d/m/Y'); ?> </td>
                                        </tr>
                                    </table>
                                    <!--end header info-->
                                    
                                    
                                    <!--patient info-->
                                    <table class="sub_worksheet">
										<tr>
											<td colspan="4"><b>Patientinformation</b></td>
										</tr>
										<tr>
                                            <td width="15%">Undersøgelsesdato</td>
                                            <td  colspan="3"><?php echo $patient_exam_date; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Navn</td>
                                            <td colspan="3"><?php echo name_syntax($patient_name);?></td>
                                        </tr>
                                        <tr>
											<td width="15%">Køn</td>
											<td><?php echo danify_sex($patient_sex); ?></td>
											<td width="30%" align="right">Kreatinin</td>
                                            <td><input type="number" step="1" name="patient_s_crea" size="10" tabindex="5" value="<?php echo $row['patient_s_crea'] == '' ? '' : $row['patient_s_crea']; ?>"> &#181;mol/l</td>
										</tr>
                                        <tr>
                                            <td width="30%">CPR</td>
                                            <td width="20%"><?php echo $patient_cpr;?></td>
                                            <td align="right">Kreatinin-dato (yyyy-mm-dd)</td>
                                            <td><input type="date" name="patient_s_crea_date" size="10" tabindex="6" value="<?php echo $row['patient_s_crea_date'] == '' ? '' : $row['patient_s_crea_date']; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td width="30%">Højde</td>
                                            <td width="20%"><input type="number" step="0.1" name="patient_height" size="8" tabindex="3" value="<?php echo $row['patient_height'] == '' ? '' : $row['patient_height']; ?>">cm</td>
                                            <td align="right">eGFR (aldersbaseret)</td>
                                            <td><?php echo round(calc_expected_stdGFR($patient_age, calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a'), $patient_sex),0);?> ml/min/1.73m<sup>2</sup></td>
                                        </tr>
                                        <tr>
                                            <td width="30%">Vægt</td>
                                            <td width="20%"><input type="number" step="0.1" name="patient_weight" size="8" tabindex="4" value="<?php echo $row['patient_weight'] == '' ? '' : $row['patient_weight']; ?>">kg</td>
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
											<td align="right">eGFR (<?php echo $patient_age < 17 ? 'CKiD<sub>bedside</sub>' : 'CKD-EPI';?>)</td>
                                            <td><?php echo calc_GFR_CKD_EPI_CKiD($row['patient_s_crea'],$patient_age,$patient_sex, $row['patient_height']);?> ml/min/1.73m<sup>2</sup></td>
                                        <?php
										}
										?>
                                        </tr>
                                        <tr>
                                            <td width="30%">Overfladeareal</td>
                                            <td width="20%"><?php echo round(calc_BSA($row['patient_height'], $row['patient_weight'], $BSA_model),2);?> m<sup>2</sup></td>
                                            <td align="right">ECV<sub>est</sub> (Bird's estimate)</td>
                                            <td><?php echo round(calc_ECV_Bird($row['patient_height'],$row['patient_weight']),1);?> liter</td>
                                        </tr>
                                    </table>
                                    <!--end patient info-->
                                    
                                    <!--standard info-->
                                    <table class="sub_worksheet">
                                        <tr>
                                            <td width="30%">Standard:</td>
                                            <td>
                                                <select name="standard_lot_number" tabindex="7">
													<option value="">---</option>
                                                <?php
                                                
                                                    while($select_fill = mysqli_fetch_array($result_standard)){
                                                        
                                                ?>
													
                                                    <option value="<?php echo $select_fill['lot_number']?>" <?php echo $select_fill['lot_number'] == $row_examination['standard_lot_number'] ? ' selected="selected"' : '';?>><?php echo '#' . $select_fill['lot_number'];?></option>
                                                    
                                                <?php
                                                
                                                    }
                                                ?>
                                                
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tracer:</td>
                                            <td>
												<select name="patient_tracer" tabindex="8">
													<option value="51Cr" <?php echo $row['patient_tracer'] == '51Cr' ? ' selected="selected"' : '';?>>51Cr</option>
													<option value="99mTc" <?php echo $row['patient_tracer'] == '99mTc' ? ' selected="selected"' : '';?>>99mTc</option>
												</select>
											</td>
                                        </tr>
                                        <tr>
                                            <td>Bioanalytiker:</td>
                                            <td><input type="text" name="bioanalyst" tabindex="9" value="<?php echo $row_examination['bioanalyst'] == '' ? '' : $row_examination['bioanalyst']; ?>"></td>
                                        </tr>
                                        <tr>
<!--
                                            <td>Standard dato:</td>
                                            <td><?php echo $row_standard['standard_date']; ?></td>
-->
                                            
                                            <td>Aktivitet af sprøjte:</td>
                                            <td><input type="number" step="0.0001" name="activity_syringe" tabindex="10" value="<?php echo $row_examination['activity_syringe'] == '' ? '0' : $row_examination['activity_syringe']; ?>">MBq</td>
                                        </tr>
                                        <tr>
<!--
                                            <td>Afvejet:</td>
                                            <td><?php echo $row_standard['standard_weight']; ?> g</td>
-->
                                            
                                            <td>Vægt fyldt sprøjte:</td>
                                            <td><input type="number" step="0.00001" id="weight_full_syringe" name="weight_full_syringe" onblur="checkField()" tabindex="11" value="<?php echo $row_examination['weight_full_syringe'] == '' ? '0' : $row_examination['weight_full_syringe']; ?>">g</td>
                                        </tr>
                                        <tr>
<!--
                                            <td>Fortyndet til</td>
                                            <td><?php echo $row_standard['diluted_volume']; ?> ml</td>
-->
                                            <td>Vægt tom sprøjte:</td>
                                            <td><input type="number" step="0.00001" id="weight_empty_syringe" name="weight_empty_syringe" onblur="checkField()" tabindex="12" value="<?php echo $row_examination['weight_empty_syringe'] == '' ? '0' : $row_examination['weight_empty_syringe']; ?>">g</td>
                                        </tr>
                                        <tr>
											<td>Nettovægt af sprøjte:</td>
                                            <td><span id="net_weight_syringe" name="net_weight_syringe"></span></td>
                                            <!--<?php echo $row_examination['weight_full_syringe']-$row_examination['weight_empty_syringe'] == '' ? '0' : $row_examination['weight_full_syringe']-$row_examination['weight_empty_syringe']; ?>-->
                                    </table>
                                    <!--end standard info-->
                                    
                                    <!--generic worksheet-->
                                    <table class="sub_worksheet_data">
                                        <tr>
                                            <th width="15%"></th>
                                            <th>Tidspunkt</th>
                                            <th>Volumen</th>
                                            <th>Counts</th>
                                            <th>Tælletid</th>
                                        </tr>
                                        <tr>
                                            <th>Injecering:</th>
                                            <td><input type="datetime-local" name="actual_injection_time" tabindex="13" value="<?php echo $row_examination['actual_injection_time'] == '' ? $row['patient_exam_date'] . 'T08:00' : substr(date('c', strtotime($row_examination['actual_injection_time'])),0,-6); ?>"></td>
                                            <td><!--<input type="text" name="injection_volume" value="<?php echo $row_examination['injection_volume'] == '' ? '' : $row_examination['injection_volume']; ?>" size="4">ml--></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        
                                        
                                        <tr>
                                            <th>Baggrund I:</th>
                                            <td colspan="2"></td>
                                            <td><input type="number" step="1" name="background_1_counts" tabindex="25" value="<?php echo $row_examination['background_1_counts'] == '' ? '' : $row_examination['background_1_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="background_1_count_time" value="<?php echo $row_examination['background_1_count_time'] == '' ? $count_time_long : $row_examination['background_1_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="background_1_count_time" value="<?php echo $row_examination['background_1_count_time'] == '' ? $count_time_short : $row_examination['background_1_count_time']; ?>" size="5"></td>
											<?php
												} 
											?>
                                        </tr>
                                        
                                        <tr>
                                            <th>Standard I:</th>
                                            <td></td>
                                            <td><input type="number" step="0.1" size="4" name="standard_1_volume" tabindex="18" value="<?php echo $row_examination['standard_1_volume'] == '' ? '1' : $row_examination['standard_1_volume']; ?>" value="1">ml</td>
                                            <td><input type="number" step="1" name="standard_1_counts" tabindex="26" value="<?php echo $row_examination['standard_1_counts'] == '' ? '' : $row_examination['standard_1_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="standard_1_count_time" value="<?php echo $row_examination['standard_1_count_time'] == '' ? $count_time_long : $row_examination['standard_1_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="standard_1_count_time" value="<?php echo $row_examination['standard_1_count_time'] == '' ? $count_time_short : $row_examination['standard_1_count_time']; ?>" size="5"></td>
											<?php
												} 
											?>
                                        </tr>
                                        
                                        <tr>
                                            <th>0-prøve:</th>
                                            <td><?php echo $patient_injection_time; ?></td>
                                            <?php
												if($measurement_type == 'B1' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_0_volume" tabindex="19" value="<?php echo $row_examination['sample_0_volume'] == '' ? '2' : $row_examination['sample_0_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_0_volume" tabindex="19" value="<?php echo $row_examination['sample_0_volume'] == '' ? '2' : $row_examination['sample_0_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_0_counts" tabindex="27" value="<?php echo $row_examination['sample_0_counts'] == '' ? '' : $row_examination['sample_0_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="sample_0_count_time" value="<?php echo $row_examination['sample_0_count_time'] == '' ? $count_time_long : $row_examination['sample_0_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="sample_0_count_time" value="<?php echo $row_examination['sample_0_count_time'] == '' ? $count_time_short : $row_examination['sample_0_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>

                                        </tr>
                                        
                                        <tr>
                                            <th>1.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_1_time" tabindex="14" value="<?php echo $row_examination['sample_1_time'] == '' ? $row['patient_exam_date'] . 'T12:00' : substr(date('c', strtotime($row_examination['sample_1_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B1' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_1_volume" tabindex="20" value="<?php echo $row_examination['sample_1_volume'] == '' ? '2' : $row_examination['sample_1_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_1_volume" tabindex="20" value="<?php echo $row_examination['sample_1_volume'] == '' ? '2' : $row_examination['sample_1_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_1_counts" tabindex="28" value="<?php echo $row_examination['sample_1_counts'] == '' ? '' : $row_examination['sample_1_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="sample_1_count_time" value="<?php echo $row_examination['sample_1_count_time'] == '' ? $count_time_long : $row_examination['sample_1_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="sample_1_count_time" value="<?php echo $row_examination['sample_1_count_time'] == '' ? $count_time_short : $row_examination['sample_1_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>
                                        
                                        <tr>
                                            <th>2.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_2_time" tabindex="15" value="<?php echo $row_examination['sample_2_time'] == '' ? $row['patient_exam_date'] . 'T12:00' : substr(date('c', strtotime($row_examination['sample_2_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B1' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_2_volume" tabindex="21" value="<?php echo $row_examination['sample_2_volume'] == '' ? '2' : $row_examination['sample_2_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_2_volume" tabindex="21" value="<?php echo $row_examination['sample_2_volume'] == '' ? '2' : $row_examination['sample_2_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_2_counts" tabindex="29" value="<?php echo $row_examination['sample_2_counts'] == '' ? '' : $row_examination['sample_2_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="sample_2_count_time" value="<?php echo $row_examination['sample_2_count_time'] == '' ? $count_time_long : $row_examination['sample_2_count_time']; ?>" size="5"></td>
                                            <?php
												} else {
											?>
													<td><input type="number" step="1" name="sample_2_count_time" value="<?php echo $row_examination['sample_2_count_time'] == '' ? $count_time_short : $row_examination['sample_2_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>
                                        <!--end generic worksheet-->
                
            <?php
				if($measurement_type == 'V1' || $measurement_type == 'B1'){
					//dummy case - no information added
				} elseif($measurement_type == 'V3' || $measurement_type == 'B3') { 
            ?>
                                    <!--V3/B3 worksheet-->
                                        <tr>
                                            <th>3.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_3_time" tabindex="16" value="<?php echo $row_examination['sample_3_time'] == '' ? $row['patient_exam_date'] . 'T14:00' : substr(date('c', strtotime($row_examination['sample_3_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B3'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_3_volume" tabindex="22" value="<?php echo $row_examination['sample_3_volume'] == '' ? '2' : $row_examination['sample_3_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_3_volume" tabindex="22" value="<?php echo $row_examination['sample_3_volume'] == '' ? '2' : $row_examination['sample_3_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_3_counts" tabindex="30" value="<?php echo $row_examination['sample_3_counts'] == '' ? '' : $row_examination['sample_3_counts']; ?>" size="8"></td>
                                            <td><input type="number" step="1" name="sample_3_count_time" value="<?php echo $row_examination['sample_3_count_time'] == '' ? $count_time_short : $row_examination['sample_3_count_time']; ?>" size="5"></td>
                                        </tr>
                                        <tr>
                                            <th>4.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_4_time" tabindex="16" value="<?php echo $row_examination['sample_4_time'] == '' ? $row['patient_exam_date'] . 'T14:00' : substr(date('c', strtotime($row_examination['sample_4_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B3'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_4_volume" tabindex="22" value="<?php echo $row_examination['sample_4_volume'] == '' ? '2' : $row_examination['sample_4_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_4_volume" tabindex="22" value="<?php echo $row_examination['sample_4_volume'] == '' ? '2' : $row_examination['sample_4_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_4_counts" tabindex="30" value="<?php echo $row_examination['sample_4_counts'] == '' ? '' : $row_examination['sample_4_counts']; ?>" size="8"></td>
                                            <td><input type="number" step="1" name="sample_4_count_time" value="<?php echo $row_examination['sample_4_count_time'] == '' ? $count_time_short : $row_examination['sample_4_count_time']; ?>" size="5"></td>
                                        </tr>
                                        <tr>
                                            <th>5.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_5_time" tabindex="16" value="<?php echo $row_examination['sample_5_time'] == '' ? $row['patient_exam_date'] . 'T15:00' : substr(date('c', strtotime($row_examination['sample_5_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B3'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_5_volume" tabindex="22" value="<?php echo $row_examination['sample_5_volume'] == '' ? '2' : $row_examination['sample_5_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_5_volume" tabindex="22" value="<?php echo $row_examination['sample_5_volume'] == '' ? '2' : $row_examination['sample_5_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_5_counts" tabindex="30" value="<?php echo $row_examination['sample_5_counts'] == '' ? '' : $row_examination['sample_5_counts']; ?>" size="8"></td>
                                            <td><input type="number" step="1" name="sample_5_count_time" value="<?php echo $row_examination['sample_5_count_time'] == '' ? $count_time_short : $row_examination['sample_5_count_time']; ?>" size="5"></td>
                                        </tr>
                                        <tr>
                                            <th>6.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_6_time" tabindex="16" value="<?php echo $row_examination['sample_6_time'] == '' ? $row['patient_exam_date'] . 'T15:00' : substr(date('c', strtotime($row_examination['sample_6_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B3'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_6_volume" tabindex="22" value="<?php echo $row_examination['sample_6_volume'] == '' ? '2' : $row_examination['sample_6_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_6_volume" tabindex="22" value="<?php echo $row_examination['sample_6_volume'] == '' ? '2' : $row_examination['sample_6_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_6_counts" tabindex="30" value="<?php echo $row_examination['sample_6_counts'] == '' ? '' : $row_examination['sample_6_counts']; ?>" size="8"></td>
                                            <td><input type="number" step="1" name="sample_6_count_time" value="<?php echo $row_examination['sample_6_count_time'] == '' ? $count_time_short : $row_examination['sample_6_count_time']; ?>" size="5"></td>
                                        </tr>
                                    <!--end V3/B3 worksheet-->
                                    
                <?php
                } elseif($measurement_type == 'V3_24' || $measurement_type == 'B2_24') {
                    //create V4_24 worksheet and B2_24 (similar)
                ?>
                                        <!--V4_24 worksheet-->
                                        <tr>
                                            <th>3.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_3_time" tabindex="16" value="<?php echo $row_examination['sample_3_time'] == '' ? date('Y-m-d',strtotime($row['patient_exam_date'] . '+1 days')) . 'T09:00' : substr(date('c', strtotime($row_examination['sample_3_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B1' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_3_volume" tabindex="22" value="<?php echo $row_examination['sample_3_volume'] == '' ? '2' : $row_examination['sample_3_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_3_volume" tabindex="22" value="<?php echo $row_examination['sample_3_volume'] == '' ? '2' : $row_examination['sample_3_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_3_counts" tabindex="30" value="<?php echo $row_examination['sample_3_counts'] == '' ? '' : $row_examination['sample_3_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="sample_3_count_time" value="<?php echo $row_examination['sample_3_count_time'] == '' ? $count_time_long : $row_examination['sample_3_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="sample_3_count_time" value="<?php echo $row_examination['sample_3_count_time'] == '' ? $count_time_short : $row_examination['sample_3_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>
                                        
                                        <tr>
                                            <th>4.-prøve:</th>
                                            <td><input type="datetime-local" name="sample_4_time" tabindex="17" value="<?php echo $row_examination['sample_4_time'] == '' ? date('Y-m-d',strtotime($row['patient_exam_date'] . '+1 days')) . 'T09:00' : substr(date('c', strtotime($row_examination['sample_4_time'])),0,-6); ?>"></td>
                                            <?php
												if($measurement_type == 'B1' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="0.1" size="4" name="sample_4_volume" tabindex="23" value="<?php echo $row_examination['sample_4_volume'] == '' ? '2' : $row_examination['sample_4_volume']; ?>">ml</td>
											<?php	
												} else {
											?>
													<td><input type="number" step="0.1" size="4" name="sample_4_volume" tabindex="23" value="<?php echo $row_examination['sample_4_volume'] == '' ? '2' : $row_examination['sample_4_volume']; ?>">ml</td>
											<?php	
												}
											?>
                                            <td><input type="number" step="1" name="sample_4_counts" tabindex="31" value="<?php echo $row_examination['sample_4_counts'] == '' ? '' : $row_examination['sample_4_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="sample_4_count_time" value="<?php echo $row_examination['sample_4_count_time'] == '' ? $count_time_long : $row_examination['sample_4_count_time']; ?>" size="5"></td>
											<?php
												} else {
											?>
													<td><input type="number" step="1" name="sample_4_count_time" value="<?php echo $row_examination['sample_4_count_time'] == '' ? $count_time_short : $row_examination['sample_4_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>
                                        
                                        
                                    <!--end V4_24 worksheet-->
                <?php
                } else {
                    echo '<div class="body_text">Der eksisterer ikke arbejdsark til den pågældende type</div>';
                }
                
                ?>
									<tr>
                                            <th>Standard II</th>
                                            <td></td>
                                            <td><input type="number" step="0.1" size="4" name="standard_2_volume" tabindex="24" value="<?php echo $row_examination['standard_2_volume'] == '' ? '1' : $row_examination['standard_2_volume']; ?>">ml</td>
                                            <td><input type="number"  step="1" name="standard_2_counts" tabindex="32" value="<?php echo $row_examination['standard_2_counts'] == '' ? '' : $row_examination['standard_2_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="standard_2_count_time" value="<?php echo $row_examination['standard_2_count_time'] == '' ? $count_time_long : $row_examination['standard_2_count_time']; ?>" size="5"></td>
											<?php	
												} else {
											?>
													<td><input type="number" step="1" name="standard_2_count_time" value="<?php echo $row_examination['standard_2_count_time'] == '' ? $count_time_short : $row_examination['standard_2_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>
                                        
                                        <tr>
                                            <th>Baggrund II:</th>
                                            <td colspan="2"></td>
                                            <td><input type="number"  step="1" name="background_2_counts" tabindex="33" value="<?php echo $row_examination['background_2_counts'] == '' ? '' : $row_examination['background_2_counts']; ?>" size="8"></td>
                                            <?php 
												if($measurement_type == 'V3_24' || $measurement_type == 'B2_24'){
											?>
													<td><input type="number" step="1" name="background_2_count_time" value="<?php echo $row_examination['background_2_count_time'] == '' ? $count_time_long : $row_examination['background_2_count_time']; ?>" size="5"></td>
											<?php	
												} else {
											?>
													<td><input type="number" step="1" name="background_2_count_time" value="<?php echo $row_examination['background_2_count_time'] == '' ? $count_time_short : $row_examination['background_2_count_time']; ?>" size="5"></td>
											<?php	
												}
											?>
                                        </tr>

                                    </table>
							
                                    <!--submit-->
                                    <table class="submit_table">
                                        <tr>
                                            <td>
												<input type="button" value="Hent tællinger" onclick="window.location.href='read_counts.php?<?php echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY); ?>&std=<?php echo $row_examination['standard_lot_number'];?>'">
                                                <input type="submit" name="submit" value="Gem arbejdsarkdata">
                                                <input type="button" class="button" value="Svarark" onclick="window.location.href='create_answer.php?page=<?php echo $page;?>&id=<?php echo($row['id']);?>&measurement_type=<?php echo($row['measurement_type']);?>&patient_cpr=<?php echo($row['patient_cpr']);?>&patient_injection_time=<?php echo($row['patient_injection_time']);?>&patient_s_crea=<?php echo($row['patient_s_crea']);?>&patient_height=<?php echo($row['patient_height']);?>&patient_weight=<?php echo($row['patient_weight']);?>&department=<?php echo($row['department']);?>&patient_name=<?php echo($row['patient_name']);?>&patient_sex=<?php echo($row['patient_sex']);?>&patient_exam_date=<?php echo($row['patient_exam_date']);?>&accession_number=<?php echo($row['accession_number']);?>'">
                                            </td>
                                        </tr>
                                    </table>
                                    <!--end submit-->
                                    
                                    <!--previous measurements-->
                                    <table class="sub_worksheet">
                                        <tr>
                                            <td width="100%"><h3>Historik</td>
                                        </tr>
                                    </table>
                                    <table class="sub_worksheet">
                                        <tr>
                                            <td width="50%"><b>Undersøgelsesdato</b></td>
                                            <td width="50%"><b>Undersøgelsestype</b></td>
                                        </tr>
                                    <?php
                                        //echo mysqli_num_rows($result_duplicates);
                                        if(mysqli_num_rows($result_duplicates) >= 1){
                                            $accession_number_array = array();
                                            while($row_duplicates = mysqli_fetch_array($result_duplicates)){
                                                ?>
                                                <tr>
                                                    <td width="50%"><?php echo $row_duplicates['patient_exam_date'];?></td>
                                                    <td width="50%"><?php echo $row_duplicates['measurement_type'];?></td>
                                                </tr>
                                                <?php
                                            }
										}

										if(mysqli_num_rows($result_duplicates) < 1) {
                                            echo '<tr><td colspan="2">Der findes ingen tidligere målinger på ' . name_syntax($patient_name) . '</td></tr>';
                                        }
                                    ?>
                                    
                                    </table>
                                    <!--end previous measurements-->
                                    
                </form>
            </div>
                <?php
            } else {
                echo '<div class="body_text">Der er ikke angivet noget patient-ID.</div>';
            }

    echo(html_footer());
}
?>
