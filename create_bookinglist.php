 <?php
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

	if($connection_error){
            echo $error_message;
            echo(html_footer());
        } else {
			echo(html_header());
		?>
			<div class="error_message">
				Indtast dato for undersøgelse (dags dato hvis tom)
				<form name="date_form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
				Dato (yyyymmdd): <input type="text" name="date">
				<input type="submit" name="submit" value="Vis liste"><br>
			</div>
		<?php
			if(isset($_GET['submit'])){
                $date = $_GET['date'];
                if($date == ''){
					$date = date('Ymd');
				}
                $query = "SELECT * FROM patient_data WHERE patient_exam_date = '" . $date . "'";
                $result = mysqli_query($con, $query);
			
				$num_results = mysqli_num_rows($result);

				
			
				if ($num_results > 0){
								
					while($row = mysqli_fetch_array($result)){
				?>
							<table class="bookings">
									<tr>
										<td colspan="4"><h3><sup>51</sup>Cr-EDTA Clearance - Nuklearmedicinsk afdeling</h3></td>
									</tr>
									<tr>
										<td width="10%">Undersøgelsesdato</td>
										<td width="15%"><?php echo $row['patient_exam_date']; ?></td>
										<td>Henv.-afd.</td>
										<td></td>
									</tr>
									<tr>
										<td>Undersøgelsestype</td>
										<td colspan="3"><?php echo type_converter($row['measurement_type']); ?></td>
									</tr>
									<tr>
										<td width="15%">Navn</td>
										<td colspan="3"><?php echo name_syntax($row['patient_name']);?></td>
									</tr>
									<tr>
										<td width="15%">Køn</td>
										<td ><?php echo danify_sex($row['patient_sex']); ?></td>
										<td width="15%">Kreatinin</td>
										<td></td>
									</tr>
									<tr>
										<td width="10%">CPR</td>
										<td width="15%"><?php echo $row['patient_cpr'];?></td>
										<td>Krea.-dato</td>
										<td></td>
									</tr>
									<tr>
										<td>Højde</td>
										<td width="30%"></td>
										<td colspan="2">cm</td>
									</tr>
									<tr>
										<td>Vægt</td>
										<td width="30%"></td>
										<td colspan="2">kg</td>
									</tr>
									<tr>
										<td width="30%">Standard</td>
										<td colspan="3"><!-- standard input --></td>
									</tr>
									<tr>
										<td>Bioanalytiker</td>
										<td colspan="3"><!-- bioanalytiker input --></td>
									</tr>
									<tr>                                         
										<td>Aktivitet af sprøjte</td>
										<td width="30%"></td>
										<td colspan="2">MBq</td>
									</tr>
									<tr>                                   
										<td>Vægt fyldt sprøjte</td>
										<td width="30%"></td>
										<td colspan="2">g</td>
									</tr>
									<tr>
										<td>Vægt tom sprøjte</td>
										<td width="30%"></td>
										<td colspan="2">g</td>
									</tr>
									<tr>
										<td width="30%">Injektionstidspunkt</td>
										<td colspan="3"><!-- standard input --></td>
									</tr>
							</table>
							
							<table class="bloodbookings">
								<tr>
									<th width="30%">Prøve</th>
									<th width="30%">Tidspunkt</th>
									<th>Volumen (ml)</th>
									<th>Tælletid (s)</th>
								</tr>
								<tr>
									<td>Standard</td>
									<td></td>
									<td>1</td>
								<?php 
									if($row['measurement_type'] == 'V3' || $row['measurement_type'] == 'V1' || $row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B3'){
								?>
									<td>1200</td>
								<?php
								} else {
								?>	
									<td>5000</td>
								<?php
								}
								?>
								</tr>
								<tr>
									<td>0-prøve</td>
									<td></td>
								<?php 
									if($row['measurement_type'] == 'B2_24'  || $row['measurement_type'] == 'B2'){
								?>
									<td></td>
								<?php
								} else {
								?>		
									<td>2</td>
								<?php
								}
									if($row['measurement_type'] == 'V3'  || $row['measurement_type'] == 'V1' || $row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B3'){
								?>
									<td>1200</td>
								<?php
								} else {
								?>	
									<td>5000</td>
								<?php
								}
								?>
								</tr>
								<tr>
								<?php 
									if($row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B2_24'){
								?>
									<td>120-1</td>
								<?php 
									} elseif($row['measurement_type'] == 'B3'){
								?>
									<td>120-1</td>
								<?php
									} else {
								?>		
									<td>180-1</td>
								<?php
									}
								?>
									<td></td>
								<?php 
								if($row['measurement_type'] == 'B2_24' || $row['measurement_type'] == 'B2'){
								?>
									<td></td>
								<?php
								} else {
								?>		
									<td>2</td>
								<?php
								}
									if($row['measurement_type'] == 'V3'  || $row['measurement_type'] == 'V1' || $row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B3'){
								?>
									<td>1200</td>
								<?php
								} else {
								?>	
									<td>5000</td>
								<?php
								}
								?>
								</tr>
								<tr>
								<?php 
									if($row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B2_24'){
								?>
									<td>120-2</td>
								<?php
									} elseif($row['measurement_type'] == 'B3'){
								?>
									<td>120-2</td>
								<?php
									} else {
								?>		
									<td>180-2</td>
								<?php
									}
								?>
									<td></td>
								<?php 
								if($row['measurement_type'] == 'B2_24' || $row['measurement_type'] == 'B2'){
								?>
									<td></td>
								<?php
								} else {
								?>		
									<td>2</td>
								<?php
								}
									if($row['measurement_type'] == 'V3'  || $row['measurement_type'] == 'V1' || $row['measurement_type'] == 'B1' || $row['measurement_type'] == 'B3'){
								?>
									<td>1200</td>
								<?php
								} else {
								?>	
									<td>5000</td>
								<?php
								}
								?>
								</tr>
							<?php 
								if($row['measurement_type'] == 'V3_24' || $row['measurement_type'] == 'B2_24'){
							?>
									<tr>
										<td>24-1</td>
										<td></td>
									<?php 
									if($row['measurement_type'] == 'B2_24' || $row['measurement_type'] == 'B2'){
									?>
										<td></td>
									<?php
									} else {
									?>		
										<td>2</td>
									<?php
									}
									?>
										<td>5000</td>
									</tr>
									<tr>
										<td>24-2</td>
										<td></td>
									<?php 
									if($row['measurement_type'] == 'B2_24' || $row['measurement_type'] == 'B2'){
									?>
										<td></td>
									<?php
									} else {
									?>		
										<td>2</td>
									<?php
									}
									?>
										<td>5000</td>
									</tr>
									<?php
								} elseif($row['measurement_type'] == 'V3'){
									?>
									<tr>
										<td>240-1</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>240-2</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>300-1</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>300-1</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<?php
								} elseif($row['measurement_type'] == 'B3'){
									?>
									<tr>
										<td>150-1</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>150-2</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>240-1</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<tr>
										<td>240-2</td>
										<td></td>
										<td>2</td>
										<td>1200</td>
									</tr>
									<?php
								}
									
						?>
					</table>
					<?php 
					}
				} else {
				?>
					<div class="error_message">
						Der er ingen bookinger tilgængelige på dato <?php echo $date; ?> <br/>
					</div>
				<?php
				} 
				?>
				
			 
<?php
	}
	echo(html_footer());
}
?>
     
</body>
</html> 
