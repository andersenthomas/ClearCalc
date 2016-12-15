<?php
/*
 * list_standards.php
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
        
echo(html_header());

if($connection_error){
    echo $error_message;
    echo(html_footer());
} else {

        // if sort has been passed in query string
        $order_by = (isset($_GET['order_by'])) ? $_GET['order_by'] : 'calibration_date';
        $sorting = (isset($_GET['sorting'])) ? $_GET['sorting'] : 'desc';
        
        //if the user presses the again we'll switch sorting            
        switch($sorting) {
            case "asc":
                $sort = 'desc';
                break;
            case "desc":
                $sort = 'asc';
                break;
        }
        
                                                           
        $query = "SELECT * FROM standards ORDER BY " . $order_by . " " . $sorting;
        if(!$result = mysqli_query($con, $query)){
            echo "Cannot parse query\n";
            echo $query;
        }
        
    ?>
            <div align="center">
				<table class="data">
					<tr>
						<td colspan="12" align="right">
							<input type="button" class="button" value="Tilføj standard" onclick="window.location.href='add_standard.php'">
						</td>
					</tr>
                    <tr>
                        <!--<th><a href="list.php?order_by=id&sorting=<?php echo $sort;?>">ID</a></th>-->
                        <th><a href="list_standards.php?order_by=lot_number&sorting=<?php echo $sort;?>">Lot#</a></th>
                        <th><a href="list_standards.php?order_by=calibration_date&sorting=<?php echo $sort;?>">Kal. dato</a></th>
                        <th><a href="list_standards.php?order_by=calibration_activity&sorting=<?php echo $sort;?>">Kal. aktivitet</a></th>
                        <th><a href="list_standards.php?order_by=standard_date&sorting=<?php echo $sort;?>">Std.-dato</a></th>
                        <th><a href="list_standards.php?order_by=standard_weight&sorting=<?php echo $sort;?>">Std.-vægt</a></th>
                        <th><a href="list_standards.php?order_by=diluted_volume&sorting=<?php echo $sort;?>">Fort.-volumen</a></th>
                        <th>Datodifferense</th>
                        <th>Hen.-faktor</th>
                        <th>Akt./ml</th>
                        <th>Ret standard</th>
                        <th>Slet standard</th>
                    </tr>
					<?php 
						while($row = mysqli_fetch_array($result)){
					?>
                    <tr>
                        <!--<td><?php echo $row['id']; ?></td>-->
                        <td><?php echo $row['lot_number']; ?></td>
                        <td><?php echo $row['calibration_date']; ?></td>
                        <td><?php echo $row['calibration_activity']; ?>  MBq</td>
                        <td><?php echo $row['standard_date']; ?></td>
                        <td><?php echo $row['standard_weight']; ?> g</td>
                        <td><?php echo $row['diluted_volume']; ?> ml</td>
                        <td><?php echo subtract_dates($row['calibration_date'],$row['standard_date']); ?> dage</td>
                        <td><?php echo round(decay_factor(subtract_dates($row['calibration_date'],$row['standard_date'])),5); ?></td>
                        <td><?php echo round($row['calibration_activity']*decay_factor(subtract_dates($row['calibration_date'],$row['standard_date'])),1); ?> MBq/ml</td>
                        <td><a href="edit_standard.php?id=<?php echo($row['id']);?>">Ret</a></td>
                        <td><a href="delete_standard.php?id=<?php echo($row['id']);?>" onclick="return onDelete('Ønsker du at slette denne standard?');">X</a></td>
                    </tr>
            	<?php  
                	}
            	?>
                
				</table>
            </div>

<?php
    echo(html_footer());
}
?>
