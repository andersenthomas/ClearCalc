<?php
/*
 * search_patient.php
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

echo '<div class="body_text">Wildcards er allerede inkluderet
                             i søgningen (søgningen "søren" vil også giv hit på "sørensen"). Anvend derfor ikke * eller lignende'; 

if($connection_error){
    echo $error_message;
    echo html_footer();
} else {
            if(isset($_POST['submit'])){
                if(!empty($_POST['patient_cpr'])){
                    $patient_cpr = $_POST['patient_cpr'];
                    $query = "select * from patient_data where patient_cpr like '%$patient_cpr%'";
                } elseif(!empty($_POST['patient_name'])){
                    $patient_name = $_POST['patient_name'];
                    $query = "select * from patient_data where patient_name like '%$patient_name%'";
                } elseif(!empty($_POST['patient_exam_date'])){
                    $patient_exam_date = $_POST['patient_exam_date'];
                    $query = "select * from patient_data where patient_exam_date like '%$patient_exam_date%'";
                } else {
                    echo '<div class="body_text">Der er ikke angivet nogen søgekriterier</div>';
                    echo(html_footer());
                    break;
                }
                $result = mysqli_query($con, $query);
                $list_of_ids = array();
                //foreach($result as $row){
                while($row = mysqli_fetch_array($result)){
                    array_push($list_of_ids,$row['id']);
                }
                if(sizeof($list_of_ids) == 0){
                    echo '<div class="body_text">Ingen resultater. Prøv venligst igen</div>';
                } else {
                    $redirect_url = 'list_patients.php?';
                    //foreach($list_of_ids as $key){
                    for($i=0;$i<sizeof($list_of_ids);$i++){
                        $redirect_url = $redirect_url . 'id[]=' . $list_of_ids[$i];
                        //do not add '&' in the last iteration
                        if($i<sizeof($list_of_ids)-1){
                            $redirect_url = $redirect_url . '&';
                        }
                    }
                    echo $redirect_url;
                    header("location:" . $redirect_url);
                }
            }
        ?>

        <form method="post" action="">
                <table class="edit">
                    <tr>
                        <td>CPR</td>
                        <td colspan="2">
                            <input type="number" name="patient_cpr" size="30">
                        </td>
                    </tr>
                    <tr>
                        <td>Navn</td>
                        <td colspan="2">
                            <input type="text" name="patient_name" size="30">
                        </td>
                    </tr>
                    <tr>
                        <td>Undersøgelsesdato</td>
                        <td>
                            <input type="date" name="patient_exam_date" size="10">
                        </td>
                        <td>
                            format: YYYY-mm-dd
                    </tr>
                    <tr>
                        <td colspan="3" align="right">
                            <input type="button" value="Annuller" onClick="window.location.href='index.php'">
                            <input type="reset" value="Slet">
                            <input type="submit" name="submit" value="Søg">
                        </td>
                    </tr>
                </table>
            </form>
<?php

    echo(html_footer());
}
        
?>
