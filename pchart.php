<!DOCTYPE html>
<head>
<?php 
//This file/script is a part of an E-Learning portal "Farmers Agricultural Inventory & Repository - FAIR" and is completely free.
//Th‎is file/script is called from FAIR to show the Rainfall graph.
// This file/script displays the pricipitation chart for different locations in Kerala. The graph is plotted using RGraph template
// This file/script queries the database to get link of the precipitaiton chart and provide this link to function 'getchartarray'.
//This file/script calls a function 'getchartarray' present in file/script named 'image_processing.php' and gets an array having the 'x_coordinate' & 'y_coordinate' of 
// the precipitaion image as return from 'getchartarray'.

require 'image2.php';//include the image processinig script
require_once('../config.php');?/ //required by moodle
$PAGE->set_context(get_system_context());required by moodle
$PAGE->set_title("Weather Forecast And Anlaysis");
$PAGE->set_url($CFG->wwwroot.'custom/test3.php'); //filename hererequired by moodle
echo $OUTPUT->header();required by moodle
//database connectivity
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'fallingrain';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}


?>
<!-- Libraries required for rgraph to work -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.core.js"></script>
<script src="http://localhost/moodle/custom/libraries/RGraph.common.dynamic.js"></script>   <!-- Just needed for dynamic features (eg tooltips) -->

<script src="http://localhost/moodle/custom/libraries/RGraph.common.annotate.js"></script>  <!-- Just needed for annotating -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.context.js"></script>   <!-- Just needed for context menus -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.effects.js"></script>   <!-- Just needed for visual effects -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.key.js"></script>       <!-- Just needed for keys -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.resizing.js"></script>  <!-- Just needed for resizing -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.tooltips.js"></script>  <!-- Just needed for tooltips -->
<script src="http://localhost/moodle/custom/libraries/RGraph.common.zoom.js"></script>      <!-- Just needed for zoom -->

<script src="http://localhost/moodle/custom/libraries/RGraph.bar.js"></script>              <!-- Just needed for Bar charts -->
<script src="http://localhost/moodle/custom/libraries/RGraph.line.js"></script>             <!-- Just needed for Line charts -->

</head>
<body>
<?php 

//echo $label;
//echo $x;
//echo $y;
?>

<h1>Weather Forecast And Anlaysis</h1>
<form id="form1" class="hideMe" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"> 
<h4>Current Weather</h4>
<select name="location">
<option>Choose Location</option>
<?php
$dbarray=$mysqli->query("select name,urlname from urls_for_fallingrain");
$num_rows=$dbarray->num_rows;
for ($i=0;$i<$num_rows;$i++) {
	$dbrow=$dbarray->fetch_row();
	echo "<option value='$dbrow[1]'>$dbrow[0]</option>";
}
echo "</select>";
if (isset ($_POST['location'])) {
$xy=getchartarray($_POST['location']);//calliing the image processing script
$x=null;
$y=null;
$label=null;
$flag=0;
// converting the returned array to comma delimited value so as to be used in the Rgraph format
foreach ($xy as $key) {
	if ($flag==0) {
		$x="'".$key['x_coordinate'];
		$y=$key['y_coordinate'];
		if (isset($key['label']))
			$label="'".$key['label'];
		else
			$label="'','";
		$flag=1;
	}
	else {
		$x=$x."','".$key['x_coordinate'];
		$y=$y.",".$key['y_coordinate'];
		if (isset($key['label']))
			$label=$label."','".$key['label'];
		else
			$label=$label."','";
	}
}
}
?>

<button type='submit'>Submit</button>
</form>
<!-- //RGrpah plotting function -->
<script> 
    window.onload = function ()
    {
    	var line = new RGraph.Line('cvs', [<?php echo $y;?>])
        .Set('labels', [<?php echo $label;?>'])
        .Set('tooltips', [<?php echo $x;?>'])
        .Set('events.click', function (e, shape) {alert('The Line chart click event');})
        .Draw();
    }
</script>



<P><Strong>Precipitation chart for Trivandrum from Sunday Nov 17<br>Precipitation in cm/last 3 hrs</Strong></P>
 <!-- //Calling the RGraph plotting function -->
<canvas id="cvs" width="600" height="250">[No canvas support]</canvas>
 
Source: fallingrain.com
<?php echo $OUTPUT->footer();?><!-- Required for moodle -->

</body>
</html>
