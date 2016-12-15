<?php
/*
 * read_patients_date.php
 */
 

include('common_functions.php');
putenv('TMPDIR=/srv/www/htdocs/clearance/patient_ris_data/tmp');
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

function return_files_directory($directory, $file_type){
    if ($handle = opendir($directory)) {
        $files = array();
        //loop over the directory
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && strtolower(substr($entry, strrpos($entry, '.') + 1)) == $file_type) {
                //echo "$entry\n";
                array_push($files, $entry);
            }
        }
        
        closedir($handle);
    } else {
        $files = 'Error';
    }
    return($files);
}

function change_file_permission($files){
	foreach($files as $file){
		chmod($file, 0777);
	}
}

function get_RIS_data($quiet=True, $todays_date){
	if($quiet == True){
        try{
            //shell_exec("findscu -q -W -X -k 0008,0052 -k 0010,0020 -k 0010,0010 -k 0010,0040 -k 0010,0030 -k 0010,1030 -k 0010,1020 -k 0020,0010 -k 0008,0050 -k '(0040,0100)[0].0040,0002=" . $todays_date . "'  -k '(0040,0100)[0].0040,0003' -k 0032,1060 -aet CLEAR -aec MEDORA 10.254.128.212 5004");
            shell_exec("findscu -q -W -X -k 0008,0052 -k 0010,0020 -k 0010,0010 -k 0010,0040 -k 0010,0030 -k 0010,1030 -k 0010,1020 -k 0020,0010 -k 0008,0050 -k '(0040,0100)[0].0040,0002=" . $todays_date . "'  -k '(0040,0100)[0].0040,0003' -k 0032,1060 -k 0032,1032 -aet VENUSNU -aec MEDORA 10.254.128.212 5004");
        } catch (Exception $e) {
            throw new Exception( 'Something really gone wrong', 0, $e);  
        } 
        
    } else {
        try{
            //shell_exec("findscu -v -X -W -k 0008,0052 -k 0010,0020 -k 0010,0010 -k 0010,0040 -k 0010,0030 -k 0010,1030 -k 0010,1020 -k 0020,0010 -k 0008,0050 -k '(0040,0100)[0].0040,0002=" . $todays_date . "'  -k '(0040,0100)[0].0040,0003' -k 0032,1060 -aet CLEAR -aec MEDORA 10.254.128.212 5004");
            shell_exec("findscu -v -W -X -k 0008,0052 -k 0010,0020 -k 0010,0010 -k 0010,0040 -k 0010,0030 -k 0010,1030 -k 0010,1020 -k 0020,0010 -k 0008,0050 -k '(0040,0100)[0].0040,0002=" . $todays_date . "'  -k '(0040,0100)[0].0040,0003' -k 0032,1060 -k 0032,1032 -aet VENUSNU -aec MEDORA 10.254.128.212 5004");
        } catch (Exception $e) {
            throw new Exception( 'Something really gone wrong', 0, $e);
        }
    }
}

function convert_dcm2txt($files, $path){
    foreach($files as $file){
		$command = 'dcmdump --print-tree ' . $path . '/' . $file . ' > ' . $path . '/' . $file . '.txt 2>&1';
		try{
            shell_exec($command);
        } catch (Exception $e) {
			throw new Exception('Error: ', 0, $e);
        }
    }
}

function move_files($files, $source, $destination){
    // Cycle through all source files
    $source = $source . '/';
    $destination = $destination . '/';
    if(!sizeof($files)==0){
        foreach($files as $file){
            // If we copied this successfully, mark it for deletion
            if (copy($source.$file, $destination.$file)) {
                $delete[] = $source.$file;
            }
        }
        // Delete all successfully-copied files
        foreach ($delete as $file) {
            unlink($file);
        }
    } else {
        
    }
}

echo(html_header());

?>

<div class="datacontainer">
<form name="date_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	Dato: <input type="date" name="date" value="<?php echo date('Y-m-d');?>">
	<input type="submit" name="submit" value="Submit"><br>
</div>

<?php

if($connection_error){
    echo $error_message;
    echo(html_footer());
} else {
	
	if(isset($_POST['submit'])){
		$date_from_form = $_POST['date'];
		$date_from_form = str_replace('-', '', $date_from_form);
	    $date = get_current_date('Y-m-d');
	    $timestamp = date('H:i:s');
		mkdir(getcwd() . '/ris_data/' . $date_from_form . '-' . $timestamp);
		$current_dir = getcwd() . '/ris_data/' . $date_from_form . '-' . $timestamp;
		
	    get_RIS_data(False, $date_from_form);
	    
	    $dcm_files = return_files_directory(getcwd(), 'dcm');
	    move_files($dcm_files, getcwd(), $current_dir);
	    convert_dcm2txt($dcm_files, $current_dir);
	    $txt_files = return_files_directory($current_dir, 'txt');  
	    
	    //iterate over all the files in the directory
	    $number_of_inserts = 0;
	    $number_of_moves = 0;
	    for ($i=0; $i<sizeof($txt_files); $i++){
	        $file = file_get_contents($current_dir . '/' . $txt_files[$i]);
	        $file_encoded = mb_convert_encoding($file, 'UTF-8',mb_detect_encoding($file, 'UTF-8, ISO-8859-1', true)); 
	        $output = explode("\n", $file_encoded);
	    
	        //define between which tags the data is
	        $start_tag = '[';
	        $end_tag = ']';
	  
	        $data = array();
		
	        //select text inside []
	        for ($j=0; $j<sizeof($output); $j++){
	            if (preg_match('/'.preg_quote($start_tag).'(.*?)'.preg_quote($end_tag).'/s', $output[$j], $matches)) {
	                //echo $matches[1] . '<br>';
	                array_push($data, $matches[1]);
	            }
	        }
	        
	        
	        if(substr($data[11],0,8) == 'Glomerul'){//GFR patient
				//remove dashes from cpr string
				$data[7] = str_replace("-", "", $data[7]);
	        
				//todays date
				$creation_date = get_current_date('Y-m-d H:i:s');
				//$patient_age = calc_age($data[8], $date, '%Y') . '.' . round(calc_age($data[8], $date, '%m')/12*10,0);
				$patient_age = number_format(calc_age($data[8], $date, '%a')/365.25,2,'.','');
				
				if(strpos($data[11], 'flere') != false) { //24h exam
					if($patient_age > 15.0){
						$query_patient_data = 'INSERT INTO patient_data (accession_number, patient_name, patient_cpr, patient_exam_date, creation_date, patient_sex, measurement_type, patient_birthday, department) 
						SELECT "' . $data[5] . '", "' . $data[6] . '", "' . $data[7] . '", "' . $date_from_form . '", "' . $creation_date . '", "' . $data[9] . '", "V3", "' . $data[8] . '", "' . $data[10] . '" FROM patient_data 
						WHERE NOT EXISTS(SELECT accession_number FROM patient_data WHERE accession_number = "' . $data[5] . '") LIMIT 1';
					} else {
						$query_patient_data = 'INSERT INTO patient_data (accession_number, patient_name, patient_cpr, patient_exam_date, creation_date, patient_sex, measurement_type, patient_birthday, department) 
						SELECT "' . $data[5] . '", "' . $data[6] . '", "' . $data[7] . '", "' . $date_from_form . '", "' . $creation_date . '", "' . $data[9] . '", "B3", "' . $data[8] . '", "' . $data[10] . '" FROM patient_data 
						WHERE NOT EXISTS(SELECT accession_number FROM patient_data WHERE accession_number = "' . $data[5] . '") LIMIT 1';
					}
				} else { //1point exam
					if($patient_age > 15.0){
						$query_patient_data = 'INSERT INTO patient_data (accession_number, patient_name, patient_cpr, patient_exam_date, creation_date, patient_sex, measurement_type, patient_birthday, department) 
						SELECT "' . $data[5] . '", "' . $data[6] . '", "' . $data[7] . '", "' . $date_from_form . '", "' . $creation_date . '", "' . $data[9] . '", "V1", "' . $data[8] . '", "' . $data[10] . '" FROM patient_data 
						WHERE NOT EXISTS(SELECT accession_number FROM patient_data WHERE accession_number = "' . $data[5] . '") LIMIT 1';
					} else {
						$query_patient_data = 'INSERT INTO patient_data (accession_number, patient_name, patient_cpr, patient_exam_date, creation_date, patient_sex, measurement_type, patient_birthday, department) 
						SELECT "' . $data[5] . '", "' . $data[6] . '", "' . $data[7] . '", "' . $date_from_form . '", "' . $creation_date . '", "' . $data[9] . '", "B1", "' . $data[8] . '", "' . $data[10] . '" FROM patient_data 
						WHERE NOT EXISTS(SELECT accession_number FROM patient_data WHERE accession_number = "' . $data[5] . '") LIMIT 1';
					}
				}
				
				
				////check if acc number is already in the database - if so update date
				//$query_moved_patient = 'SELECT accession_number,patient_exam_date FROM patient_data WHERE accession_number = "' . $data[5] . '" AND patient_exam_date < "' . $date . '"';
				//$result_moved_patient = mysqli_query($con, $query_moved_patient);
				//if(mysqli_num_rows($result_moved_patient) > 0){
					//$query_update = 'UPDATE patient_data SET patient_exam_date = "' . $date . '" WHERE accession_number = "' . $data[5] . '"';
					//$result_update = mysqli_query($con,$query_update);
					//if($result_update){
						//$number_of_moves = $number_of_moves + 1;
					//}
				//} else {
					//$query_patient_values = 'INSERT INTO patient_values (accession_number) SELECT "' . $data[5] . '" FROM patient_values WHERE NOT EXISTS(SELECT accession_number FROM patient_values
						//WHERE accession_number = "' . $data[5] . '") LIMIT 1';
					//$result_patient_data = mysqli_query($con, $query_patient_data);
					//$result_patient_values = mysqli_query($con, $query_patient_values);
				//}
				//$number_of_inserts = $number_of_inserts + 1;
			}
	    }
	    echo '<div class="datacontainer">';
	    echo '<div class="body_text">Antal patienter booket til ' . $date_from_form . ' indsat i databasen: ' . $number_of_inserts . ' </div>'; //mysqli_affected_rows($con)
	    echo '<div class="body_text">Antal flyttede patienter fra ' . $date_from_form . ' opdateret i databasen: ' . $number_of_moves . ' </div>';
	    echo '<div class="body_text">Data er gemt i ' . $current_dir . '</div>';
	    echo '<div class="body_text"><a href="list_patients.php">Se patienter</a></div>';
	    echo '</div>';
	}

    echo(html_footer());
}

?>
