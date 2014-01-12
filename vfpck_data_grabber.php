
<?php 
//This file/script is a part of an E-Learning portal "Farmers Agricultural Inventory & Repository - FAIR" and is completely free.
//This file/script extracts/grabs fruits/vegetable price data from vfpck.org and stores the data into the local database 'vfpck'
//To view the code for displaying fruits/vegetable prices from local database onto FAIR, please refer "vfpck_prices.php"
//

//database connectivity
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'vfpck';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

for ($urlcount=0;$urlcount<2;$urlcount++) {
	switch ($urlcount){
		case 0:// Cropwise Prices
			$partialurl='http://www.vfpck.org/docs/mwiseprice.asp?ID=';
			$partialtable='marketwise';
			$startname_string='Market</strong>:';
			$namesub_length=8;
			break;
		case 1:// Marketwise Prices
			$partialurl='http://www.vfpck.org/docs/vegprice.asp?ID=';
			$partialtable='commoditywise';
			$startname_string='Vegetable/Fruit</strong>:';
			$namesub_length=17;
			break;
	}
	echo "<br><br><br><br><br>";;
	$link_ids=null;
	for ($id=1;$id<99;$id++) {
		$url= $partialurl.$id;
		$raw = @file_get_contents($url);//getting the url
		if($raw === FALSE){
			//echo "error";
			continue;
		}
		else {
			//data grabbing starts here
			if ($link_ids==null)
				$link_ids=$id;
			else
				$link_ids=$link_ids.",".$id;
			$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B","Â");

			$content = str_replace($newlines, "", html_entity_decode($raw));
			
			preg_match_all("|[0-9]+/[0-9]+/[0-9][0-9][0-9][0-9]|U",$content,$datearray);
			$date = $datearray[0][0];
			
			$startname=strpos($content,$startname_string);
			$endname=strpos($content,"Date:");
			$nameraw=substr($content,$startname,$endname-$startname);
			$nameraw=strip_tags($nameraw);
			$name = substr($nameraw, $namesub_length);

			$start = strpos($content,'<TABLE width="500" border=1 align=center cellpadding="3" cellspacing="0" bordercolor="#CC9900" bgcolor=lightgoldenrodyellow');
			$end = strpos($content,'td></tr><tr><td><ul class="contenthome">* WP - Wholesale Price <br>',$start) + 8;
			$table = substr($content,$start,$end-$start);
			//echo $table;
			$table=utf8_encode($table);
			preg_match_all("|<TR bordercolor=(.*)</TR>|U",$table,$rows);
			//var_dump($rows);
			$date=str_replace('/','s',"$date");
			$tablename=$partialtable.$id."_".$date;
			//creating the table in the database
			if (isset($rows[0][0])){
				$mysqli->query("drop table if exists $tablename");
				$mysqli->query("
						create table $tablename
						(
						id int not null AUTO_INCREMENT,
						cropname varchar(255),
						wholesalep varchar(255),
						retailp varchar(255),
						primary key(id)
						);
								");
				$mysqli->prepare("insert into $tablename (cropname,wholesalep) values ('$name','$date')")->execute();
				foreach ($rows[0] as $row){
					preg_match_all("|<TD(.*)</TD>|U",$row,$cells);
					$n0 = strip_tags($cells[0][0]);
					preg_match_all("/[a-z]+(.*)/i",$n0,$y0);
					$cropname=$y0[0][0];
					
					$n1 = strip_tags($cells[0][1]);
					preg_match_all("/[0-9]+/i",$n1,$y1);
					$wholesalep=$y1[0][0];
					
					$n2 = strip_tags($cells[0][2]);
					preg_match_all("/[0-9]+/i",$n2,$y2);
					$retailp=$y2[0][0];
					
					If ($wholesalep==0 && $retailp==0)
						continue;
					if($wholesalep==0)
						$wholesalep="data unavailable";
					if($retailp==0)
						$retailp="data unavailable";
// Updating value in the database
					$mysqli->prepare("insert into $tablename (cropname,wholesalep,retailp) values ('$cropname','$wholesalep','$retailp')")->execute();
					echo " {$cropname} |{$wholesalep} |{$retailp}<br>\n";
				}
			}
		}
	}
	if ($urlcount==0)
	$marketwise_link_ids=$link_ids;
	elseif ($urlcount==1)
	$commoditywise_link_ids=$link_ids;
	
}
echo $marketwise_link_ids."<br>".$commoditywise_link_ids;
if (isset ($date) && isset ($commoditywise_link_ids) && isset ($marketwise_link_ids)){
	$date=str_replace('s','/',"$date");
	$mysqli->prepare("insert into dates (date,marketwise_link_id,commoditywise_link_id) values ('$date','$marketwise_link_ids','$commoditywise_link_ids')")->execute();
}
$mysqli->close();
?>
