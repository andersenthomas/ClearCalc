<?php
/*
 * delete_standard.php
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

if($connection_error){
    echo $error_message;
    echo(html_footer());
} else {
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $result = mysqli_query($con, "delete from standards where id='$id'");
        if($result){
            header('location:list_standards.php');
        } else {
            echo(html_header());
            echo '<div class="body_text">Ups! Pinligt! Der er sket en fejl.</div>';
            echo(html_footer());
        }
    }
}

?>
