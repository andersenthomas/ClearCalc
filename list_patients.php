 <?php
	require('user_authentication.php');
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
			
			if(isset($_POST['submit'])){
				$measurement_type = $_POST['measurement_type'];
			
				$query = "SELECT id FROM patient_data ORDER BY id desc";
				$result = mysqli_query($con, $query);
			
				while ($row = mysqli_fetch_array($result)) {
					if(!$measurement_type[$row['id']] == ''){
						$query = "UPDATE patient_data SET measurement_type = '" . $measurement_type[$row['id']] . "' WHERE id = " . $row['id'];
						//echo $query;
						$result_update = mysqli_query($con, $query);
					} else {
						//echo 'skipping<br>';
					}
				}
			
				//print_r($list_measurement_type);
			}
			
			// check if sort and order_by has been passed in query string otherwise default
			$order_by = (isset($_GET['order_by'])) ? $_GET['order_by'] : 'patient_exam_date';
			$static_order_by = (isset($_GET['order_by'])) ? $_GET['order_by'] : 'patient_exam_date';
			$sorting = (isset($_GET['sorting'])) ? $_GET['sorting'] : 'desc';
			$static_sorting = (isset($_GET['sorting'])) ? $_GET['sorting'] : 'desc';
        
			//if the user presses the again we'll switch sorting            
			switch($sorting) {
				case "asc":
					$sort = 'desc';
					break;
				case "desc":
					$sort = 'asc';
					break;
			}
			
			//if ids are passed we should only display those
			if(isset($_GET['id'])){
                $id = $_GET['id'];
                $query = "SELECT * FROM patient_data WHERE id IN (" . implode(',', $id) . ")  ORDER BY " . $order_by . " " . $sorting;
                $result = mysqli_query($con, $query);
                $page_url = 'list_patients.php?';
                for($i=0;$i<sizeof($id);$i++){
                    $page_url = $page_url . 'id[]=' . $id[$i];
                    //this is not the last element. append & to $page_url string
                    if($i < sizeof($id)-1){
                        $page_url = $page_url . '&';
                    }
                }
			//if no ids are passed we will display all entries in the database        
			} else {
				$query = "SELECT * FROM patient_data ORDER BY " . $order_by . " " . $sorting;
				$result = mysqli_query($con, $query);
				$total_rows = mysqli_num_rows($result); 
				if(isset($_GET['page'])) {
					$page = preg_replace('#[^0-9]#i', '', $_GET['page']); // filter everything but numbers for security
				} else {
					$page = 1;
				}
				
				//items per page
				$items_per_page = 20;
				$last_page = ceil($total_rows / $items_per_page);

				if ($page < 1) {
					$page = 1;
				} elseif ($page > $last_page) {
					$page = $last_page;
				}
	
				// define limit for mysql query
				$limit = 'LIMIT ' . ($page - 1) * $items_per_page . ',' . $items_per_page;

				// limit number of rows returned
				$query = "SELECT * FROM patient_data ORDER BY " . $order_by . " " . $sorting . " " . $limit;
				$result = mysqli_query($con, $query);
		        $page_url = 'list_patients.php?';

				
			}
			
			
			// create numbers to click in between the next and back buttons
			$center_pages = "";
			$sub_1 = $page - 1;
			$sub_2 = $page - 2;
			$sub_3 = $page - 3;
			$sub_4 = $page - 4;
			$add_1 = $page + 1;
			$add_2 = $page + 2;
			$add_3 = $page + 3;
			$add_4 = $page + 4;
	
			if($page == 1) {
				$center_pages .= '&nbsp; <b>' . $page . '</b> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_2 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_3 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_3 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_4 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_4 . '</a> &nbsp;';
			} elseif($page == 2) {
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <b>' . $page . '</b> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_2 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_3 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_3 . '</a> &nbsp;';
			} elseif ($page == $last_page) {
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_4 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_4 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_3 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_3 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_2 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <b>' . $page . '</b> &nbsp;';
			} elseif ($page > 2 && $page < ($last_page - 1)) {
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_2 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <b>' . $page . '</b> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_2 . '</a> &nbsp;';
			} else if ($page > 1 && $page < $last_page) {
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_3 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_3 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_2 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_2 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $sub_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $sub_1 . '</a> &nbsp;';
				$center_pages .= '&nbsp; <b>' . $page . '</b> &nbsp;';
				$center_pages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $add_1 . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $add_1 . '</a> &nbsp;';
			}

			$pagination_display = "";
			// more than 1 page
			if ($last_page != "1"){
				// page x of y
				//$pagination_display .= 'Side <strong>' . $page . '</strong> af ' . $last_page. '&nbsp;  &nbsp;  &nbsp; ';
				$pagination_display .=  '&nbsp; <a title="Første side (1)" href="' . $_SERVER['PHP_SELF'] . '?page=1&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '"> << </a> ';
					$previous = $page - 1;
					$pagination_display .=  '&nbsp;  <a title="Forrige side" href="' . $_SERVER['PHP_SELF'] . '?page=' . $previous . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '"> < </a>';
				if ($page != 1) {
					//not first page
				}
				// center pages between << < & > >>
				//$pagination_display .= $center_pages . '... &nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?page=' . $last_page . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '">' . $last_page . '</a> ';
				$pagination_display .= $center_pages;
				if ($page != $last_page) {
					//not last page
				}
				$next_page = $page + 1;
				$pagination_display .=  '&nbsp;  <a title="Næste side" href="' . $_SERVER['PHP_SELF'] . '?page=' . $next_page . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting . '"> ></a> ';
				$pagination_display .=  '&nbsp;  <a title="Sidste side (' . $last_page . ')" href="' . $_SERVER['PHP_SELF'] . '?page=' . $last_page . '&order_by=' . $static_order_by . '&sorting=' . $static_sorting .'"> >></a> ';
			}

			echo(html_header());

			if(empty($_GET['id'])){
				echo '<div align="center" class="body_text">' . $pagination_display . '</div>';
			}
?>
		<div align="center">
			<form method="post" action="">
				<table class="data">
					<tr>
						<td colspan="12" align="right">
							<!--<input type="button" class="button" value="Tilføj patient" onclick="window.location.href='add_patient.php'">-->
							<input type="submit" name="submit" value="Gem måletype(r)">
						</td>
					</tr>
                    <tr>
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=id&sorting=" . $sort;?>">ID</a></th>
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_name&sorting=" . $sort;?>">Navn</a></th>
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_cpr&sorting=" . $sort;?>">CPR</a></th>
                        <th>Alder</th>
                        <!--<th><a href="<?php echo $page_url . "&order_by=department&sorting=" . $sort;?>">Afd.</a></th>-->
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_exam_date&sorting=" . $sort;?>">Undersøgelsesdato</a></th>
                        <!--<th><a href="<?php echo $page_url . "&order_by=accession_number&sorting=" . $sort;?>">Acc. #</a></th>-->
                        <!--<th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_weight&sorting=" . $sort;?>">Vægt</a></th>-->
                        <!--<th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_height&sorting=" . $sort;?>">Højde</a></th>-->
                        <!--<th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_sex&sorting=" . $sort;?>">Køn</a></th>-->
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_s_crea&sorting=" . $sort;?>">Kreatinin</a></th>
                        <!--<th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=patient_injection_time&sorting=" . $sort;?>">Inj.-tid</a></th>-->
                        <th><a href="<?php echo $page_url . "&page=" . $page . "&order_by=measurement_type&sorting=" . $sort;?>">Måletype</a></th>

                        <!--<th>Ret basaldata</th>-->
                        <th>Arbejdsark</th>
                        <th>Resultatark</th>
                        <!--<th>Slet?</th>-->
                    </tr>
			<?php
				while($row = mysqli_fetch_array($result)){
                $patient_age = number_format(calc_age($row['patient_birthday'], $row['patient_exam_date'], '%a')/365.25,2,'.','');
			?>
					<tr>
                        <td><?php echo $row['id']; ?></td>
                        <td align="left"><?php echo name_syntax($row['patient_name']); ?></td>
                        <td><?php echo $row['patient_cpr']; ?></td>
                        <td><?php echo $patient_age;?></td>
                        <!--<td><?php echo $row['department']; ?></td>-->
                        <td><?php echo $row['patient_exam_date']; ?></td>
                        <!--<td><?php echo $row['accession_number']; ?></td>-->
                        <!--<td><?php echo $row['patient_weight']; ?></td>-->
                        <!--<td><?php echo $row['patient_height']; ?></td>-->
                        <!--<td><?php echo danify_sex($row['patient_sex']); ?></td>-->
                        <td><?php echo $row['patient_s_crea']; ?></td>
                        <!--<td><?php echo $row['patient_injection_time']; ?></td>-->
                        <!--<td><?php echo $row['patient_sex']; ?></td>-->
                        <!--<td><?php echo $row['measurement_type']; ?></td>-->
                        <td>
							<select name="measurement_type[<?php echo($row['id']);?>]">
								<option value="" <?php echo $row['measurement_type'] == '' ? 'selected=selected' : '';?>>-- Vælg type --</option>
								<option value="V1" <?php echo $row['measurement_type'] == 'V1' ? 'selected=selected' : '';?>>Voksen, 1-punkt</option>
								<option value="V3_24" <?php echo $row['measurement_type'] == 'V3_24' ? 'selected=selected' : '';?>>Voksen, 3+24 timer</option>
								<option value="V3" <?php echo $row['measurement_type'] == 'V3' ? 'selected=selected' : '';?>>Voksen, 3-punkt</option>
								<option value="B1" <?php echo $row['measurement_type'] == 'B1' ? 'selected=selected' : '';?>>Barn, 1-punkt</option>
								<option value="B2_24" <?php echo $row['measurement_type'] == 'B2_24' ? 'selected=selected' : '';?>>Barn, 2+24 timer</option>
								<option value="B3" <?php echo $row['measurement_type'] == 'B3' ? 'selected=selected' : '' ;?>>Barn, 3-punkt</option>
							</select>
						</td>
                        <!--<td><a href="edit_patient.php?id=<?php echo($row['id']);?>&page=<?php echo $page;?>&order_by=<?php echo $static_order_by;?>&sorting=<?php echo $static_sorting;?>">Ret</a></td>-->
                        <td><a href="create_worksheet.php?page=<?php echo $page;?>&id=<?php echo($row['id']);?>&measurement_type=<?php echo($row['measurement_type']);?>&patient_cpr=<?php echo($row['patient_cpr']);?>&patient_injection_time=<?php echo($row['patient_injection_time']);?>&patient_s_crea=<?php echo($row['patient_s_crea']);?>&patient_height=<?php echo($row['patient_height']);?>&patient_weight=<?php echo($row['patient_weight']);?>&department=<?php echo($row['department']);?>&patient_name=<?php echo($row['patient_name']);?>&patient_sex=<?php echo($row['patient_sex']);?>&patient_exam_date=<?php echo($row['patient_exam_date']);?>&accession_number=<?php echo($row['accession_number']);?>">Arbejdsark</a></td>
                        <td><a href="create_answer.php?page=<?php echo $page;?>&id=<?php echo($row['id']);?>&measurement_type=<?php echo($row['measurement_type']);?>&patient_cpr=<?php echo($row['patient_cpr']);?>&patient_injection_time=<?php echo($row['patient_injection_time']);?>&patient_s_crea=<?php echo($row['patient_s_crea']);?>&patient_height=<?php echo($row['patient_height']);?>&patient_weight=<?php echo($row['patient_weight']);?>&department=<?php echo($row['department']);?>&patient_name=<?php echo($row['patient_name']);?>&patient_sex=<?php echo($row['patient_sex']);?>&patient_exam_date=<?php echo($row['patient_exam_date']);?>&accession_number=<?php echo($row['accession_number']);?>">Resultatark</a></td>
                        <!--<td><a href="delete.php?id=<?php echo($row['id']);?>" onclick="return onDelete();">x</a></td>-->
                    </tr>
		<?php  
			}
		?>
				</table>
			 </form>
			 </div>
<?php
	if(empty($_GET['id'])){
				echo '<div align="center" class="body_text">' . $pagination_display . '</div>';
			}

	echo(html_footer());
}
?>
     
</body>
</html> 
