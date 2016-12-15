<?php
/*
 * index.php
 */

require('user_authentication.php');
include('common_functions.php');

if(isset($_POST['submit'])){
	//header("location:list_bookings.php");
	header("location:list_bookings.php?date=" . $_POST['date_to_show']);
}

$current_date = date('Y-m-d');

echo(html_header());
?>
    <div class="datacontainer">
        <div class="body_text">
			<hr>
			<ul>
				<li><h4><a href="create_bookinglist.php">Generer optrækliste</a></h4></li>
			</ul>
			<hr>
			<!--<ul>
				<li><h4><a href="javascript:toggle('injection')">Se og generer optrækslister</a></h4></li>
				<ul>
				<div id="injection" style="display:none">
					<li><h4 class="sublink"><a href="add_booked_patient.php">Tilføj booket patient til optrækliste</a></h4></li>
					<li><h4 class="sublink"><a href="list_bookings.php?date=<?php echo $current_date;?>">Se dagens bookede patienter</a></h4></li>
					<li>
						<form method="post" action="">
							<h4 class="sublink">Se bookede patienter fra dato:<br></h4>
							<input type="date" name="date_to_show" value="YYYY-mm-dd" size="8"><input type="submit" name="submit" value="Se patienter">
						</form>
					</li>
				</div>
				</ul>
			</ul>
			-->
			<ul>
				<li><h4><a href="list_patients.php">Se patienter</a></h4></li>
				<li><h4><a href="list_standards.php">Se/tilføj standard(er)</a></h4></li>
				<li><h4><a href="search_patient.php">Søg efter patient</a></h4></li>
			</ul>
				<hr>
			<ul>
				<li><h4><a href="statistics.php">Statistik</a></h4></li>
				<li><h4><a href="read_patients_date.php">Indlæs patienter fra RIS manuelt</a></h4></li>
				<li><h4><a href="list_datafiles.php">Vis datafiler fra tælleren</a></h4></li>
				<!--<li><h4><a href="javascript:toggle('extra')">Ekstra funktioner</a></h4></li>
				<ul>
				<div id="extra" style="display:none">
					<li><h4 class="sublink"><a href="read_patients_date.php">Indlæs patienter</a></h4></li>
				</div>
				</ul>-->
			</ul>
			<hr>
        </div>
    </div>
<?php
    echo(html_footer());
?>
