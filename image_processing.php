<?php
//This file/script is a part of an E-Learning portal "Farmers Agricultural Inventory & Repository - FAIR" and is completely free.
//This file/script has the image processing script to digitize the precipitation chart images from fallingrain.com for different locations of Kerala.
//The file/script which is accessed in FAIR is 'pchart.php' present in the same folder as this and it calls the function "getchartarray" present in 
//this file and returns an array containing the digitised 'x_coordinate' and 'y_coordinate' of the PNG image.
//'x_coordinate' in the returned array has the timestamp and 'y_coordinate' has the precipitation value.
//Use var_dump function to view the array structure.
//
function getchartarray($image) {
date_default_timezone_set("Asia/Calcutta"); //setting the default timezone to IST
$details=new stdclass();

//// Show the image values
$im = imagecreatefrompng($image);//$image can be replace by the the PNG image location of precipitation chart from fallingrain.com if this file/script is intended to be used as standalone under testing/development

////search for pixel location of all x-coordinates

$days=array();
$k=0;
for ($x=15;$x<=478;$x++){
	for ($y=183;$y<=186;$y++){
		$rgb = imagecolorat($im, $x, $y);
		//echo $rgb;
		if ($rgb=='3' && !isset($days[$k])){
			$days[$k]=$x;
			$k++;
			break;
		}
		
	}
	//echo "<br>";
}
//var_dump($days);


////$days has the pixel locations of x coordinates now

////calibrating y-coordinates against the each pixel on y axis (for any scale of y axis)
$precip=array();
$y_scale=array();
$timehrs=array(
		'1'=>array(0,0,0,0,0,0,0,3,0,0,0,3,3,3,3,3,3,3,0,0,0,0,0,3),
		'2'=>array(0,3,0,0,0,3,3,0,0,0,3,3,3,0,0,3,0,3,0,3,3,0,0,3),
		'3'=>array(0,3,0,0,3,0,3,0,0,0,0,3,3,0,3,0,0,3,0,3,0,3,3,0),
		'4'=>array(0,0,3,3,0,0,0,3,0,3,0,0,3,3,3,3,3,3,0,0,0,3,0,0),
		'5'=>array(3,3,3,0,3,0,3,0,3,0,0,3,3,0,3,0,0,3,3,0,0,3,3,0),
		'6'=>array(0,3,3,3,3,0,3,0,0,3,0,3,3,0,3,0,0,3,0,0,0,3,3,0),
		'7'=>array(),
		'8'=>array(0,3,0,3,3,0,3,0,3,0,0,3,3,0,3,0,0,3,0,3,0,3,3,0),
		'9'=>array(0,3,3,0,0,0,3,0,0,3,0,3,3,0,3,0,0,3,0,3,3,3,3,0),
		'0'=>array(0,0,0,0,0,0,0,3,3,3,3,0,3,0,0,0,0,3,0,3,3,3,3,0)
		//'z'=>array(3,0,0,0,3,3,3,0,0,3,0,3,3,0,3,0,0,3,3,3,0,0,0,3)
);
for ($x=0,$i=0;$x<=3;$x++){
	for ($y=14;$y<=19;$y++,$i++){
		$rgb = imagecolorat($im, $x, $y);
		$y_scale[$i]=$rgb;
	}
}
foreach ($timehrs as $key=>$num){
	if ($num==$y_scale)
		$y_upper_index=$key*10;
}
for ($m=1,$key=186;$m<=170;$m++,$key--) {   // 170 is the height of the graph in pixels
	$value=$y_upper_index*$m/170;
	$precip[$key]=$value; // here the key is the y coordinate of pixel grid
}

foreach($precip as $precipvalue) {
	//echo "$precipvalue<br>";
}
//// $preci[186-17] has the precipitation values for y axis from picel no 186 to 17


//// getting the precipitation values for all x axis-pixels between 21-478 on the graph
$precip_graph=array();
for ($x=21;$x<=472;$x++){
	for ($y=17;$y<=186;$y++){
		$rgb = imagecolorat($im, $x, $y);
		if ($rgb==1) {
			$precip_graph[$x]=$precip[$y];
			break;
		}
	}
} 
//var_dump($precip_graph);
//// to calibrate x axis- time stamp is needed
//// Search for getting the date (month and year can be taken from inbuilt function date() )
$datenum=array(
		'1'=>array(0,0,3,0,0,0,0,3,0,3,0,0,0,0,0,3,3,3,3,3,3,3,3,3,0,0,0,0,0,0,0,3,0,0,0,0,0,0,0,3),
		'2'=>array(0,3,0,0,0,0,3,3,3,0,0,0,0,3,0,3,3,0,0,0,3,0,0,3,3,0,0,3,0,0,0,3,0,3,3,0,0,0,0,3),
		'3'=>array(0,3,0,0,0,0,3,0,3,0,0,0,0,0,0,3,3,0,0,3,0,0,0,3,3,0,0,3,0,0,0,3,0,3,3,0,3,3,3,0),
		'4'=>array(0,0,0,3,3,3,0,0,0,0,3,0,0,3,0,0,0,3,0,0,0,3,0,0,3,3,3,3,3,3,3,3,0,0,0,0,0,3,0,0),
		'5'=>array(3,3,3,3,0,0,3,0,3,0,3,0,0,0,0,3,3,0,3,0,0,0,0,3,3,0,3,0,0,0,0,3,3,0,0,3,3,3,3,0),
		'6'=>array(0,0,3,3,3,3,3,0,0,3,0,3,0,0,0,3,3,0,0,3,0,0,0,3,3,0,0,3,0,0,0,3,3,0,0,0,3,3,3,0),
		'7'=>array(3,0,0,0,0,0,0,0,3,0,0,0,0,0,3,3,3,0,0,0,3,3,0,0,3,0,3,3,0,0,0,0,3,3,0,0,0,0,0,0),
		'8'=>array(0,3,3,0,3,3,3,0,3,0,0,3,0,0,0,3,3,0,0,3,0,0,0,3,3,0,0,3,0,0,0,3,0,3,3,0,3,3,3,0),
		'9'=>array(0,3,3,3,0,0,0,3,3,0,0,0,3,0,0,3,3,0,0,0,3,0,0,3,3,0,0,0,3,0,3,0,0,3,3,3,3,3,0,0),
		'0'=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
		);

$date=array();
for ($x=303,$i=0;$x<=307;$x++){
	for ($y=3;$y<=10;$y++,$i++){
		$rgb = imagecolorat($im, $x, $y);
		$date[$i]=$rgb;
		}
}
foreach ($datenum as $haha=>$hihi) {
	if ($hihi==$date)
	$datemsb=$haha;
}

for($x=309,$i=0;$x<=313;$x++) {
	for($y=3;$y<=10;$y++,$i++) {
		$rgb = imagecolorat($im, $x, $y);
		$date[$i]=$rgb;
	}
}
foreach ($datenum as $haha=>$hihi) {
	if ($hihi==$date)
		$datelsb=$haha;
}
$details->date=$datemsb.$datelsb;

//// Search of the time like 3z or 15z etc
$timehrs=array(
		'1'=>array(0,0,0,0,0,0,0,3,0,0,0,3,3,3,3,3,3,3,0,0,0,0,0,3),
		'2'=>array(0,3,0,0,0,3,3,0,0,0,3,3,3,0,0,3,0,3,0,3,3,0,0,3),
		'3'=>array(0,3,0,0,3,0,3,0,0,0,0,3,3,0,3,0,0,3,0,3,0,3,3,0),
		'4'=>array(0,0,3,3,0,0,0,3,0,3,0,0,3,3,3,3,3,3,0,0,0,3,0,0),
		'5'=>array(3,3,3,0,3,0,3,0,3,0,0,3,3,0,3,0,0,3,3,0,0,3,3,0),
		'6'=>array(0,3,3,3,3,0,3,0,0,3,0,3,3,0,3,0,0,3,0,0,0,3,3,0),
		'7'=>array(),
		'8'=>array(0,3,0,3,3,0,3,0,3,0,0,3,3,0,3,0,0,3,0,3,0,3,3,0),
		'9'=>array(0,3,3,0,0,0,3,0,0,3,0,3,3,0,3,0,0,3,0,3,3,3,3,0),
		'0'=>array(0,0,0,0,0,0,0,3,3,3,3,0,3,0,0,0,0,3,0,3,3,3,3,0)
		//'z'=>array(3,0,0,0,3,3,3,0,0,3,0,3,3,0,3,0,0,3,3,3,0,0,0,3)
);
$timehr=array();
$temp=array();
$foundz=0;
for ($i=0;$i<39;$i++) {
	for($x=$i,$k=0;$x<=$i+3;$x++) {
		for($y=192;$y<=197;$y++,$k++) {
			$rgb = imagecolorat($im, $x, $y);
			$temp[$k]=$rgb;
		}
	}
	
	foreach ($timehrs as $hahaha=>$hihihi) {
			if ($hihihi==$temp){
				if (!isset($timelsb))
					$timel=$hahaha;
				else  
					$timeh=$hahaha;
				
			}
		}
}
$details->time=$timel.@$timeh;
$mydate=date('y')."-".date('m')."-".$details->date;
$details->timestamp=strtotime($mydate."+ $details->time hours +5 hours +30 minute ");
$details->dateopening=date('h-i-A d-m-Y',$details->timestamp);

//// converting individual pixels of x coordinates to timestamps
$hour_perpixel_perday=array();
for ($i=0;$i<sizeof($days)-1;$i++)
	$hour_perpixel_perday[$i]=24/($days[$i+1]-$days[$i]);
$pixel_time=array();

$temp=$details->timestamp;
for ($k=0;$k<sizeof($hour_perpixel_perday);$k++) {
	$myy=$temp;
	$pixel_time[$days[$k]]=$myy;
	for ($i=1;$i<=($days[$k+1]-$days[$k]);$i++) {
		$ji=$hour_perpixel_perday[$k]*($i)*60*60;
		$myy=$temp + $ji; 
		//echo "$myy<br>";
		$pixel_time[$days[$k]+$i]=$myy;
	}
	//echo "<br>";
	$temp=$temp+$ji;
}

$xlabel=array();
$graph=array();
foreach ($pixel_time as $key=>$timestamp) {
	$element_no=$key-20;
	$keyy['x_coordinate']=date('h-i-A d-m-Y',$details->timestamp);
	$keyy['y_coordinate']=$precip_graph[$key];
	if (date('H',$timestamp)==00 && !isset($dayofweek)) {
		$keyy['label']=date('D',$timestamp);
		$dayofweek='set';
	}
	elseif(date('H',$timestamp)==02)
		unset($dayofweek);
	$graph[$element_no]=$keyy;
	unset ($keyy);
	//echo "For element $element_no: x coordinate: $key=>$timestamp And y coordinate: $precip_graph[$key]<br>";
}
return($graph);
}
//var_dump(getchartarray("oct16+0z.png"));
?>
