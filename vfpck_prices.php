<?php
//This file/script is a part of an E-Learning portal "Farmers Agricultural Inventory & Repository - FAIR" and is completely free.
//This file/script is called and executed from FAIR to diaply fruits/vegetable prices extracted from vfpck.org
//This file/script contains only the code for displaying fruits/vegetable prices from local database. 
//If you wish to view the file/script having the data extraction technique for extracting the fruits/vegetable prices from vfpck.org, please refer "vfpck_data_grabber.php"
//The fruits/vegetable prices are taken from the database 'vfpck'

//moodle format
require_once('../config.php');//required by moodle
$PAGE->set_context(get_system_context());//required by moodle
$PAGE->set_title("Crops Price");//required by moodle
$PAGE->set_heading("Dev's C-DIT Moodle");//required by moodle
echo $OUTPUT->header();//required by moodle
//database connectivity
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'vfpck';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$days_this_month=date('t');
$hi=$_GET['hi'];
switch ($hi){
	case 1://Variable required for displaying commoditywise prices
		$par_table="commoditywise";
		$colname_for_ids="commoditywise_link_id";
		$title="<h1>Get the Market Prices for all Vegetables/Fruits here</h1>";
		break;
	case 2://Variable required for displaying marketwise prices
		$par_table="marketwise";
		$colname_for_ids="marketwise_link_id";
		$title="<h1>Get the Vegetables/Fruits for all Market Prices here</h1>";
		break;
}
?>

<!DOCTYPE html>
<head>
<!-- Libraries required for rgraph to work -->
 <script src="http://localhost/moodle/custom/libraries/RGraph.common.dynamic.js"></script>   <!-- Just needed for dynamic features (eg tooltips) -->
    <script src="http://localhost/moodle/custom/libraries/RGraph.common.annotate.js"></script>  <!-- Just needed for annotating -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.context.js"></script>   <!-- Just needed for context menus -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.effects.js"></script>   <!-- Just needed for visual effects -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.key.js"></script>       <!-- Just needed for keys -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.resizing.js"></script>  <!-- Just needed for resizing -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.tooltips.js"></script>  <!-- Just needed for tooltips -->
	<script src="http://localhost/moodle/custom/libraries/RGraph.common.zoom.js"></script>

    <script src="http://localhost/moodle/custom/libraries/RGraph.common.core.js" ></script>
    <script src="http://localhost/moodle/custom/libraries/RGraph.common.key.js" ></script>
    <script src="http://localhost/moodle/custom/libraries/RGraph.bar.js" ></script>
    <script src="http://localhost/moodle/custom/libraries/RGraph.hbar.js" ></script>
    
    <!--[if lt IE 9]><script src="http://localhost/test/RGraph/excanvas/excanvas.js"></script><![endif]-->

  	<script src="http://code.jquery.com/jquery-1.9.1.js"></script><!-- For Datepicker -->
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script><!-- For Datepicker -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript">
function submit()
	document.forms["form1"].submit();
</script>
<script>
$(function() {
$( "#datepicker1" ).datepicker();
$( "#datepicker2" ).datepicker();
});
</script>

<script type="text/javascript">
    function toggleVisibility(controlId)
    {
    var control = document.getElementById(controlId);
    if(control.style.visibility == "visible" || control.style.visibility == "")
    control.style.visibility = "hidden"; //make it "hedden if wanna toggle"
    else
    control.style.visibility = "visible";
    }
</script>
<script type="text/javascript">
$(document).ready(function () {
	//$('.hideMe').hide(100);

	$('#battan1').click(function() {
		if (!$('#form1').is(':visible')) 
        $('.hideMe').hide(100);
        $('#form1').show(100);
    });
    
    $('#battan2').click(function() {
    	if (!$('#form2').is(':visible'))
        $('.hideMe').hide(100);
        $('#form2').show(100);
    });
});
</script>

</head>

<body>
<?php echo $title;?>
<form id="form1" class="hideMe" action="<?php echo $_SERVER['PHP_SELF']."?hi=".$hi; ?>" method="POST"  style="visibility: visible" >

<?php
echo "<h4>View Commodity Prices Datewise</h4>";
//Date Selection starts
echo "<select name='selecteddate'>";
$dates=$mysqli->query("select date from dates order by id desc");
$num_dates=$dates->num_rows;
$date_commodities=array();
for ($i=$num_dates;$i>0;$i--) {
	$datearray=$dates->fetch_row();
	array_push($date_commodities,$datearray[0]);
	echo "<option value='$datearray[0]'>{$datearray[0]}</option>";
}
echo "</select>";
//Date Selection ends

$resultset=$mysqli->query("select $colname_for_ids from dates where id=(select max(id) from dates)");
$link_ids=$resultset->fetch_row();
$ids = explode(',',$link_ids[0]);
//var_dump($link_ids);
$maxdatearray=$mysqli->query("select date from dates where id=(select max(id) from dates)");
$maxdate=$maxdatearray->fetch_row();

//crop or market selection starts
echo "<select name='crop'>";

foreach ($ids as $id) {
	if (!isset($_POST['selecteddate']))
		$date=$maxdate[0];
	else
		$date=$_POST['selecteddate'];
		$selecteddate=str_replace('/','s',$date);
		$tablename=$par_table.$id."_".$selecteddate;
		
		$cropnames=$mysqli->query("select cropname from $tablename");
		if(!isset ($cropnames))
			continue;
		$cropname=$cropnames->fetch_row();
		echo "<option value={$id} onclick='submit();'>{$cropname[0]}</option>\n";
}

echo "</select>";
echo "</form>";

//displaying the price data in tabular and graphical format
if (isset ($_POST['crop']) && isset ($_POST['selecteddate'])){
$selecteddate=str_replace('/','s',$_POST['selecteddate']);
$selectedcrop=$par_table.$_POST['crop']."_".$selecteddate;
$query ="select cropname,wholesalep,retailp from $selectedcrop"; ///this is the query string
$resultset = $mysqli->query($query); // result for query
$rows=$resultset->fetch_row();
$date=str_replace('s','/',$rows[1]);
echo "<p>Displaying Prices of <strong>$rows[0]</strong> in different markets</p>";
echo "<P>Date updated: <strong>$date</strong></P>";
echo "<p>Source: <strong>http://vfpck.org/</strong></p><br>";
//echo "Result of your query <br>$query<br/><br/><br/>";
  // printing entire table in tabular format
echo '<table border=2><tr><td><strong>Market Name</strong></td><td><strong>Wholesale Price   </strong></td><td><strong>Retail Price</strong></td>';
// printing fields separately
//printing rows
$rowlength=$resultset->num_rows;
for($i=1;$i<$rowlength;$i++) { // outer for loop for printinf a row
	echo '<tr>';
	$rows=$resultset->fetch_row();
	for ($j=0;$j<sizeof($rows);$j++)	{	// inner for loop for printing the columns in a row
		echo '<td align=center>'.$rows[$j].'</td>';
		if ($j==0) {
			if ($i==1)
				$name="'$rows[$j]'";
			else
				$name=$name.",'$rows[$j]'";
		}
		if ($j==1) {
			if ($i==1) {
				$prices="[$rows[1],$rows[2]]";
				$tooltips="'$rows[1]','$rows[2]'";
			}
			else {
				$prices=$prices.",[$rows[1],$rows[2]]";
				$tooltips=$tooltips.",'$rows[1]','$rows[2]'";
			}
		}
	}
	echo '</tr>';
}
echo '</table>';
$resultset->close();
?>
<!-- Displaying the Graph -->
<br><p><strong>Price Chart with price per KG</strong></p>
<?php
if ($hi=='1') { ?>

    <canvas id="cvs" width="800" height="250">[No canvas support]</canvas>
    <script>
        window.onload = function ()
        {
            var bar = new RGraph.Bar('cvs', [<?php echo $prices;?>])
                <!--.Set('background.image', 'http://localhost/test/RGraph/images/bg.png') !>
                .Set('background.grid', true)
                .Set('tooltips', [<?php echo $tooltips;?>])
                .Set('labels', [<?php echo $name;?>])
                .Set('labels.above', true)
                .Set('key', ['Wholesale Price per KG', 'Retail Price per KG'])
                .Set('key.position.gutter.boxed', false)
                .Set('key.position', 'gutter')
                .Set('key.background', 'rgb(255,255,255)')
                .Set('colors', ['blue', 'green'])
                .Set('shadow', true)
                .Set('shadow.blur', 15)
                .Set('shadow.offsetx', 0)
                .Set('shadow.offsety', 0)
                .Set('shadow.color', '#aaa')
                .Set('strokestyle', 'rgba(0,0,0,0)')
                .Set('gutter.left', 55)
                .Set('gutter.right', 5)
                .Set('hmargin.grouped', 1)
            RGraph.Effects.Fade.In(bar, {'duration': 500, 'frames': 10});
        }
    </script>
<?php
}
if ($hi=='2') { ?>
	   <canvas id="cvs" width="800" height="600">[No canvas support]</canvas>

    <script>
        window.onload = function ()
        {
            var hbar = new RGraph.HBar('cvs', [<?php echo $prices;?>])
                .Set('background.grid.hlines', false)
                .Set('scale.decimals', 1)
                .Set('colors', ['#164366','#164366','#164366','#164366','#164366'])
                .Set('colors.sequential', true)
                .Set('labels', [<?php echo $name;?>])
                .Set('gutter.left', 125)
                .Set('labels.above', true)
                .Set('labels.above.decimals', 1)
                .Set('noxaxis', true)
                .Set('xlabels', false)
				.Set('colors', ['blue', 'green'])
				.Set('key', ['Wholesale Price per KG', 'Retail Price per KG'])
				.Set('key.position', 'gutter')
            RGraph.Effects.HBar.Grow(hbar);
        }
    </script>
<?php
}
?>


<?php 
}

echo "<br><br><br><br><br><br><br><br><br><br><br>";
echo $OUTPUT->footer();//moodle format
?>
