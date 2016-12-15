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

echo(html_header());
if($connection_error){
    echo $error_message;
    echo html_footer();
} else {
            if(isset($_POST['submit'])){
                $lot_number = $_POST['lot_number'];
                $calibration_date = $_POST['calibration_date'];
                $calibration_activity = $_POST['calibration_activity'];
                $standard_date = $_POST['standard_date'];
                $standard_weight = $_POST['standard_weight'];
                $standard_weight = str_replace(",", ".", $standard_weight); //replace , for . to adhere to MySQL standards
                $diluted_volume = $_POST['diluted_volume'];
                $query = "insert into standards set lot_number='$lot_number', calibration_date='$calibration_date', calibration_activity='$calibration_activity', standard_date='$standard_date', standard_weight='$standard_weight', diluted_volume='$diluted_volume'";
                $result = mysqli_query($con, $query);
                if($result){
                    header("location:list_standards.php");
                } else {
                    echo "Oops! Something went wrong...";
                }
            }
        ?>
        
            <form method="post" action="">
                <table class="edit">
                    <tr>
                        <td>Lot#:</td> 
                        <td><input type="number" step="1" name="lot_number" value="xxxx"></td>
                    </tr>
                    <tr>
                        <td>Kalibreringsdato:</td> 
                        <td><input type="date" name="calibration_date" value="yyyy-mm-dd"></td>
                    </tr>
                    <tr>
                        <td>Aktivitet:</td> 
                        <td><input type="number" step="0.1" name="calibration_activity" value="3.7"> MBq</td>
                    </tr>
                    <tr>
                        <td>Standarddato:</td>
                        <td><input type="date" name="standard_date" value="yyyy-mm-dd"></td>
                    </tr>
                    <tr>
                        <td>Standardvægt:</td>
                        <td><input type="number" step="0.0001" name="standard_weight" value=""> g</td>
                    </tr>
                    <tr>
                        <td>Fortyndingsvolumen:</td>
                        <td><input type="number" step="1" name="diluted_volume" value="250"> ml</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <input type="button" value="Annuller" onClick="window.location.href='list_standards.php'">
                            <input type="submit" name="submit" value="Tilføj standard">
                        </td>
                </table>
            </form>
        
<?php

    echo(html_footer());
}

?>
