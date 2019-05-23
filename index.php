 <?php

//require('core-auth.php');
require('./core-mysqla.php');

#require('./kernel/class-formhfv.php');
require('./kernel/class-ui.php');

session_start();

header('Content-Type: text/html; charset=UTF-8');



#phpinfo();
#error_reporting(E_ALL);
#ini_set('display_errors', 1);


$searchKeyGlobal='appsearch';
$searchKeyDistMtr='distmeter';
$searchKeyDistLPT='distopnv';
$searchKeyPrice='price';


?><!DOCTYPE HTML><html><head>
<title>HSF-MScAI-TP - StudyHome</title>
<link rel="stylesheet" href="../client/layout/base-messages.css">
<style type="text/css">
fieldset {display:inline-block; width:230px}
fieldset input[type='number'] {width:80px !important}
table.resultset a {font-size: 1.35em}
div#header {height:175px;margin-bottom:25px; border-bottom: 6px double silver; padding-bottom:25px} 
div#header img {display:block; margin-right:25px; float: left; height: inherit; margin-bottom: -85px;}
div#footer {border-top: 6px double silver; margin-top:25px;padding-top:10px}
</style>
</head>

<body class="">



<div id="header">

<img src="client/image/logo-studyhome.png">

<div style="clear: both; margin-left:300px">
<h1>Fulda Software Engineering Project, Spring 2019. For Demonstration Only</h1>
<h2>StudyHome - Find your Appartment for a Semester abroad in Fulda</h2>
</div>

</div>






<div id="filtermenu">


<form action="<?=$_SERVER['SCRIPT_NAME']?>" method='post'>


<fieldset><legend>Distance to Campus (Meter)</legend>
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyDistMtr.'][Min]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyDistMtr]['Min']?>" placeholder="Min">
-
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyDistMtr.'][Max]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyDistMtr]['Max']?>" placeholder="Max">
</fieldset>

<fieldset><legend>Distance to Campus (Minutes by <abbr title="Low-Range Personal Traffic">LPT</abbr>)</legend>
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyDistLPT.'][Min]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyDistLPT]['Min']?>" placeholder="Min">
-
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyDistLPT.'][Max]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyDistLPT]['Max']?>" placeholder="Max">
</fieldset>


<fieldset><legend>Price / Week</legend>
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyPrice.'][Min]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyPrice]['Min']?>" placeholder="Min">
-
<input type="number" name="<?=$searchKeyGlobal.'['.$searchKeyPrice.'][Max]'?>" value="<?=$_REQUEST[$searchKeyGlobal][$searchKeyPrice]['Max']?>" placeholder="Max">
</fieldset>


<fieldset><legend>Filter...</legend>
<input type="submit" value="Show Me the ResultSet">
</fieldset>

</form></div><?php



$maxEntriesPage=10;
$curPage=1;


#var_dump($msdb);

$sqlWhere=[];
$sqlOrder=[];


function addSQLWhereOrder(&$sqlWhere, &$sqlOrder, &$rangeOperatorMapping, $sqlField, &$inputArr) {
	
	foreach ($rangeOperatorMapping as $field => $operator) {
		if (array_key_exists($field,$inputArr) && strlen($inputArr[$field]) > 0) {
			//$sqlWhere[]=$inputArr[$field].' '.$operator.' '.$sqlField;
			$sqlWhere[]=$sqlField.' '.$operator.' '.$inputArr[$field];
			$sqlOrder[]=$sqlField.' ASC'; // Doppelt
		}
	}
	
}


if (array_key_exists($searchKeyGlobal,$_REQUEST)) {

	$searchRef=&$_REQUEST[$searchKeyGlobal];
	$rangeOperatorMapping=array('Min'=>'<=','Max'=>'>=' );
	
	// array('entf_meter'=>$searchKeyDistMtr,'entf_min'=>$searchKeyDistLPT,'preis'=>$searchKeyPrice);
	
	if (array_key_exists($searchKeyDistMtr,$searchRef)) {
		$distRef=&$searchRef[$searchKeyDistMtr];
		
		addSQLWhereOrder($sqlWhere, $sqlOrder, $rangeOperatorMapping, 'entf_meter', $searchRef[$searchKeyDistMtr]);
		
		/*
		foreach ($rangeOperatorMapping as $field => $operator) {
			if (array_key_exists($field,$distRef) && strlen($distRef[$field]) > 0) {
				$sqlWhere[]=$distRef[$field].' '.$operator.' entf_meter';
				$sqlOrder[]='entf_meter ASC';
			}
		}
		*/
		
		/*
		if (count(array_filter($distRef)) > 1) {
			$sqlWhere[]='entf_meter BETWEEN '.$distRef['Min'].' AND '.$distRef['Max'];
			$sqlOrder[]='entf_meter ASC';
		}
		*/
	}
	
	if (array_key_exists($searchKeyDistLPT,$searchRef)) {
		$distRef=&$searchRef[$searchKeyDistLPT];
		if (count(array_filter($distRef)) > 1) {
			$sqlWhere[]='entf_min BETWEEN '.$distRef['Min'].' AND '.$distRef['Max'];
			$sqlOrder[]='entf_min ASC';
		}
	}
	
	if (array_key_exists($searchKeyPrice,$searchRef)) {
		$distRef=&$searchRef[$searchKeyPrice];
		if (count(array_filter($distRef)) > 1) {
			$sqlWhere[]='preis BETWEEN '.$distRef['Min'].' AND '.$distRef['Max'];
			$sqlOrder[]='preis ASC';
		}
	}

	
}


#print_r($sqlWhere);

$sql='SELECT w.*, v.anrede, v.nname, COUNT(f.m_id) AS cnt FROM wohnung AS w JOIN vermieter AS v ON v.vm_id=w.vm_id LEFT JOIN favorit AS f ON f.wohn_id=w.wohn_id WHERE w.visible > 0 '.(count($sqlWhere) > 0 ? 'AND '.implode(' AND ',$sqlWhere) : '').' GROUP BY w.wohn_id ORDER BY '.(count($sqlOrder) > 0 ? implode(',',$sqlOrder) : 'cnt DESC').' LIMIT '.(($curPage-1)*$maxEntriesPage).','.$maxEntriesPage;
$mrs=$msdb->query($sql); echo $msdb->error; echo $sql;

if ($mrs->num_rows > 0) {

	echo '<table class="resultset" cellpadding="5">';
	echo '<tr><th colspan="3"></th><th colspan="2">Distance 2 Campus</th></tr>';
	echo '<tr><th>Image</th><th>Appartment</th><th>Price</th><td>Meters</td><td>Minutes</td><th>Options</th></tr>';

	while ($row=$mrs->fetch_assoc()) {
		
		echo '<tr><td>First Image</td><td><a href="?show='.$row['wohn_id'].'">'.$row['name'].'</a><br>'.$row['plz'].' '.$row['ort'].', '.$row['str'].'';
		echo '<td>'.$row['preis'].'</td><td>'.$row['entf_meter'].'</td><td>'.$row['entf_min'].'</td><td>Bookmark, Chat [...]</td>'."</td></tr>\n";
		
	}

	echo '</table>';


} else {


GUI::printWarn('Keine Ergebnisse');

}

echo '<div id="footer"><a href="about">Profiles (Index)</a> ';

$ourNames = ['manuel'=>"Manuel Schmitt", 'michael'=>"Michael Iglhaut", 'moritz'=>"Moritz Mrosek", 'ramon'=>"Ramon Wilhelm", 'simon'=>"Simon Leister"];

$aboutDir='about/';
foreach (glob($aboutDir . '*.profile.html') as $phpfile)
{
	$fileParts=explode('.',basename($phpfile));
	echo ' &middot; <a href="' . ($phpfile) . '">' . $ourNames[$fileParts[0]] . '</a>';

}


?></div>



</body></html>
