<?php
/*
 * list_files.php
 */
 
include('common_functions.php');


echo(html_header());
echo('<div class="datacontainer">');

$output_mode = isset($_GET['mode']) ? $_GET['mode'] : 'all';

//initialization of array variables
$txtdir = 'ClearanceResults';
$txtfiles = array();
$csvdir = 'ClearanceRaw';
$csvfiles = array();
$match_dates = array();

//loop over the directory and find .txt files
if ($handle = opendir($txtdir)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && strtolower(substr($entry, strrpos($entry, '.') + 1)) == 'txt') {
			array_push($txtfiles, $entry);
        }
    }
    closedir($handle);
}

//loop over the directory and find .csv files
if ($handle = opendir($csvdir)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && strtolower(substr($entry, strrpos($entry, '.') + 1)) == 'csv') {
			$csv = array_map('str_getcsv',file('ClearanceRaw/'.$entry));
			if(trim($csv[1][1]) == 'VOXCLEAR 5000sec' || trim($csv[1][1]) == 'VOXCLEAR 1200sec'){
				array_push($csvfiles, $entry);
				//if($output_mode == 'matching'){
					for($i=0;$i<sizeof($txtfiles);$i++){
						if(abs(filemtime('ClearanceRaw/' . $entry) - filemtime('ClearanceResults/' . $txtfiles[$i])) < 100){
							array_push($match_dates, array("csvfile"=>$entry,"txtfile"=>$txtfiles[$i]));
						}
					}
				//}
			}
        }
    }
    closedir($handle);
}

?>
<input type="button" class="button" value="Vis alle datafiler" onclick="window.location.href='list_datafiles.php?mode=all'">
<input type="button" class="button" value="Vis tidsmatchede datafiler" onclick="window.location.href='list_datafiles.php?mode=matching'"><br/>
Totalt antal .csv-filer: <?php echo sizeof($csvfiles);?><br/>
Totalt antal .txt-filer: <?php echo sizeof($txtfiles);?><br/>
Antal tidsmatchede .csv- og .txt-filer: <?php echo sizeof($match_dates);?><br/>
<table class="sub_worksheet">
<th>#</th><th>Output fra WorkOut</th><th>Output fra Wizard</th>
<?php
//iterate over all the files in the directory
if($output_mode == 'matching'){
	$file_list = sizeof($match_dates);
} else {
	$file_list = max(sizeof($txtfiles),sizeof($csvfiles));
}
for($k=0; $k<$file_list; $k++){
	echo '<tr>';
	echo '	<td width="10%" align="center">'. $k .'</td>';
	if($output_mode == 'matching'){
		echo '	<td width="40%" align="center"><a href="' . $txtdir . '/' . $match_dates[$k]['txtfile'] . '"> ' . $match_dates[$k]['txtfile'] . '</a></td>';
		echo '	<td width="40%" align="center"><a href="' . $csvdir . '/' . $match_dates[$k]['csvfile'] . '"> ' . $match_dates[$k]['csvfile'] . '</a></td>';
	} else {
		echo '	<td width="40%" align="center"><a href="' . $txtdir . '/' . $txtfiles[$k] . '"> ' . $txtfiles[$k] . '</a></td>';
		echo '	<td width="40%" align="center"><a href="' . $csvdir . '/' . $csvfiles[$k] . '"> ' . $csvfiles[$k] . '</a></td>';
	}
	echo '</tr>';
}
?>
</table>

<?php
echo('<input type="button" class="button" value="Tilbage" onclick="javascript:history.back()">');
echo('</div>');
echo(html_footer());

    
?>
