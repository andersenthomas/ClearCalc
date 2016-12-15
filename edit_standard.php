<?php
/*
 * edit_standard.php
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
            //get the elements from the form and insert them into the database
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                if(isset($_POST['submit'])){
                    $lot_number = $_POST['lot_number'];
                    $calibration_date = $_POST['calibration_date'];
                    $calibration_activity = $_POST['calibration_activity'];
                    $standard_date = $_POST['standard_date'];
                    $standard_weight = $_POST['standard_weight'];
                    $standard_weight = str_replace(",", ".", $standard_weight); //replace , for . to adhere to MySQL standards
                    $diluted_volume = $_POST['diluted_volume'];
                    
                    $query = "update standards set lot_number='$lot_number', calibration_date='$calibration_date', calibration_activity='$calibration_activity', standard_date='$standard_date', standard_weight='$standard_weight', diluted_volume='$diluted_volume' where id='$id'";
                    $result_update = mysqli_query($con, $query);
                    $result = mysqli_query($con, $query);
                    if($result){
                        header("location:list_standards.php");
                    } else {
                        echo "Oops! Something went wrong...";
                    }
                
                }
                $result_id = mysqli_query($con, "select * from standards where id='$id'");
                $row_id = mysqli_fetch_array($result_id);
        ?>
        <form method="post" action="">
            <table class="edit">
                <tr>
                    <td>Lot#:</td>
                    <td><input type="number" name="lot_number" value="<?php echo $row_id['lot_number']; ?>" /></td>
                </tr>
                <tr>
                    <td>Kalibreringsdato:</td>
                    <td><input type="date" name="calibration_date" value="<?php echo $row_id['calibration_date']; ?>" /></td>
                </tr>
                <tr>
                    <td>Aktivitet</td>
                    <td><input type="number" name="calibration_activity" value="<?php echo $row_id['calibration_activity'] == '' ? date('Y-m-d') : $row_id['calibration_activity']; ?>" /> MBq</td>
                </tr>
                <tr>
                    <td>Standarddato:</td>
                    <td><input type="date" name="standard_date" value="<?php echo $row_id['standard_date']; ?>" /></td>
                </tr>
                <tr>
                    <td>Standardvægt:</td>
                    <td><input type="number" name="standard_weight" value="<?php echo $row_id['standard_weight']; ?>" /> g</td>
                </tr>
                <tr>
                    <td>Fortyndingsvolumen</td>
                    <td><input type="number" name="diluted_volume" value="<?php echo $row_id['diluted_volume']; ?>" /> ml</td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input type="button" value="Annuller" onClick="window.location.href='list_standards.php'">
                        <input type="submit" name="submit" value="Opdater standard" /></td>
                </tr>
            </table>
        </form>
        <?php
            }
    echo(html_footer());
}
?>
