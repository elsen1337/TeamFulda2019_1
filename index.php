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
$searchKeyText='fulltext';



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

form#startform {width:475px; margin: 0 auto}
form#startform input[type='search'] {width:415px}
</style>
</head>

<body class="">



<div id="header">

<img src="client/image/logo-studyhome.png">

<div style="clear: both; margin-left:300px">
<h1>Fulda Software Engineering Project, Spring 2019. For Demonstration Only</h1>
<h2>StudyHome - Find your Appartment for a Semester abroad in Fulda</h2>
</div>

</div><?php



$maxEntriesPage=10;
$curPage=1;


#var_dump($msdb);

function addSQLWhereOrder(&$sqlWhere, &$sqlOrder, &$rangeOperatorMapping, $sqlField, &$inputArr) {
	
	foreach ($rangeOperatorMapping as $field => $operator) {
		if (array_key_exists($field,$inputArr) && strlen($inputArr[$field]) > 0) {
			$sqlWhere[]=$sqlField.' '.$operator.' '.$inputArr[$field];
			$sqlOrder[$sqlField]=$sqlField.' ASC';
		}
	}
	
}


$isSessionSearch=array_key_exists($searchKeyGlobal,$_SESSION);
$isRequestSearch=array_key_exists($searchKeyGlobal,$_REQUEST);


$searchParameters=array('val'=>$searchKeyText,'entf_meter'=>$searchKeyDistMtr,'entf_min'=>$searchKeyDistLPT,'preis'=>$searchKeyPrice);
$searchLabels=array('val'=>'FullText-Search','entf_meter'=>'Distance to Campus (Meter)','entf_min'=>'Distance to Campus (Minutes by <abbr title="Low-Range Personal Traffic">LPT</abbr>)','preis'=>'Price / Week');

#session_destroy();
#var_dump($_SESSION);


$appartDetailKey='showAppart';
if (array_key_exists($appartDetailKey,$_GET)) {
	
	$wid=$_GET[$appartDetailKey];
	
	$sql='SELECT w.*, v.nname, COUNT(f.m_id) AS cnt, AVG(f.score) AS score FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id WHERE w.wohn_id='.$wid.' GROUP BY w.wohn_id';
	$mrs=$msdb->query($sql);echo $msdb->error; $whn=$mrs->fetch_object(); #$whn=$msdb->query($sql)->fetch_object(); 
	
	echo '<h1>'.$whn->name.' ($'.$whn->preis.')</h1>';
	echo '<p>'.$whn->plz.' '.$whn->ort.', '.$whn->str.' - Distance to Campus (Meter / Minutes via LPT): '.$whn->entf_meter.' / '.$whn->entf_min.'</p>';
	echo '<h2>Description</h2>';
	echo '<p>'.$whn->beschr.'</p>';
	
	
	$sql='SELECT m.name,a.val FROM w_attrvals AS a JOIN w_attrmeta AS m ON a.aid=m.aid WHERE a.wohn_id='.$wid.' AND m.vsb > 0 ORDER BY m.rdr';
	$mrs=$msdb->query($sql);
	
	if ($mrs->num_rows > 0) {
		echo '<h2>Properties</h2><table>';
		while (list($key,$val)=$mrs->fetch_array()) {
			
			echo '<th>'.$key.'</th><td>'.$val.'</td>'."\n";
			
		}
		echo '</table>';
	}	
	
	$sql='SELECT alt,bild,bild_id FROM w_image WHERE wohn_id='.$wid.' ORDER BY rdr';
	$mrs=$msdb->query($sql);
	
	if ($mrs->num_rows > 0) {
		echo '<h2>Images</h2>';
		require('kernel/class-appartimg.php');
		while ($row=$mrs->fetch_object()) {
			
			echo '<a href="images/'.AppartImage::$dirOrg.'/'.$row->bild.'"><img alt="'.$row->alt.'" src="images/'.AppartImage::$dirThumb.'/'.AppartImage::formThumbFileName($row->bild).'"></a>'."\n";
			
		}
	}
	
	echo '<h2>Optionen (Login)</h2><ul>';
	echo '<li><a href="">Kontaktieren via Chat</a></li>';
	echo '<li><a href="">Merken als Favorit</a></li>';
	echo '</ul>';
	
	echo '<p><a href="?">Zurück zur Übersicht</a></p>';
	


	
	
} elseif ($isRequestSearch || $isSessionSearch) {


	$sqlWhere=[];
	$sqlOrder=[];


	$searchRef=&$_REQUEST[$searchKeyGlobal];
	$sessionRef=&$_SESSION[$searchKeyGlobal];
	$rangeOperatorMapping=array('Min'=>'>=','Max'=>'<=' );
	
	if ($isSessionSearch===false) {$_SESSION[$searchKeyGlobal]=array();}

	
	
	$fullTextSearch=null;
	foreach ($searchParameters as $sqlKey => $formKey) {
		if ($isRequestSearch && array_key_exists($formKey,$searchRef)) {
			$sessionRef[$formKey]=$searchRef[$formKey];
		}
		if (array_key_exists($formKey,$sessionRef)) {
			
			if ($sqlKey != 'val') {
				addSQLWhereOrder($sqlWhere, $sqlOrder, $rangeOperatorMapping, $sqlKey, $sessionRef[$formKey]);
			} else {
			
				$fullTextSearch=&$sessionRef[$formKey];
				$sqlWhere[]='( m.name LIKE "%'.$fullTextSearch.'%" OR MATCH(a.val) AGAINST("'.$fullTextSearch.'") OR w.name LIKE "%'.$fullTextSearch.'%" OR MATCH(w.beschr) AGAINST ("'.$fullTextSearch.'") )';
			}
		}

		
	}
	
	
	if ($fullTextSearch != null) {
		// sqlOrder Unset on Fulltext... Order By Match Score AUTOMATICALLY from MATCH AGAINST Clause / FullTextSearch 
		$sqlOrder=[];		
	}


	#print_r($sqlWhere);


	// SUBSTRING_INDEX(GROUP_CONCAT(ColName ORDER BY ColName DESC), ',', 5)
	$sql='SELECT w.*, v.anrede, v.nname, GROUP_CONCAT(i.alt ORDER BY i.rdr SEPARATOR " // ") AS imgalt, SUBSTRING_INDEX(GROUP_CONCAT(i.bild ORDER BY i.rdr), ",", 1) AS imgpath, AVG(f.score) AS score, COUNT(DISTINCT f.m_id) AS cnt FROM wohnung AS w JOIN vermieter AS v ON v.vm_id=w.vm_id LEFT JOIN w_image AS i ON w.wohn_id=i.wohn_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id LEFT JOIN w_attrvals AS a ON a.wohn_id=w.wohn_id LEFT JOIN w_attrmeta AS m ON m.aid=a.aid WHERE w.visible > 0 '.(count($sqlWhere) > 0 ? 'AND '.implode(' AND ',$sqlWhere) : '').' GROUP BY w.wohn_id ORDER BY '.(count($sqlOrder) > 0 ? implode(',',$sqlOrder) : 'cnt DESC').' LIMIT '.(($curPage-1)*$maxEntriesPage).','.$maxEntriesPage;
	$mrs=$msdb->query($sql); echo $msdb->error;

	if ($mrs->num_rows > 0) {
		
		echo '<div id="filtermenu"><form action="'.$_SERVER['SCRIPT_NAME'].'" method="post">';
		
		foreach ($searchParameters as $sqlKey => $formKey) {
			
			echo '<fieldset><legend>'.$searchLabels[$sqlKey].'</legend>';
			
			
			if ($sqlKey != 'val') {
				
				foreach ($rangeOperatorMapping as $fieldKey => $operSym) {
					
					echo '<input type="number" name="'.$searchKeyGlobal.'['.$formKey.']['.$fieldKey.']" value="'.$_SESSION[$searchKeyGlobal][$formKey][$fieldKey].'" placeholder="'.$fieldKey.'">';
					
				}
				
			
			} else {
				
				echo '<input type="search" name="'.$searchKeyGlobal.'['.$formKey.']" value="'.$_SESSION[$searchKeyGlobal][$formKey].'" placeholder="Type any search value... We\'re hoping to match your request...">';
					
			}

			echo '</fieldset>'."\n";
			
		}
		
		echo '<fieldset><legend>Filter...</legend>
<input type="submit" value="Show Me the ResultSet">
</fieldset></form></div>';
		

		echo '<table class="resultset" cellpadding="5">';
		echo '<tr><th colspan="3"></th><th colspan="2">Distance 2 Campus</th></tr>';
		echo '<tr><th>Image</th><th>Appartment</th><th>Price</th><td>Meters</td><td>Minutes</td><th>Options</th></tr>';
		
		require('kernel/class-appartimg.php');

		while ($row=$mrs->fetch_object()) {
			
			echo '<tr><td title="'.$row->imgalt.'">'. (strlen($row->imgpath) > 0 ? '<img src="images/thumb/'.AppartImage::formThumbFileName($row->imgpath).'" alt="">' : 'First Image').'</td>';
			echo '<td><a href="?'.$appartDetailKey.'='.$row->wohn_id.'">'.$row->name.'</a><br>'.$row->plz.' '.$row->ort.', '.$row->str.'';
			echo '<td>'.$row->preis.'</td><td>'.$row->entf_meter.'</td><td>'.$row->entf_min.'</td><td>&#9733;'.$row->score.' ('.$row->cnt.')</td>'."</td></tr>\n";
			
		}

		echo '</table>';


	} else {


		GUI::printWarn('Keine Ergebnisse');

	}

	
} else {
	
	echo '<form method="post" id="startform"><input type="search" name="'.$searchKeyGlobal.'['.$searchKeyText.']" placeholder="Type any search value... We\'re hoping to match your request..."><input type="submit" value="Go..."></form>';
	
	
}



echo '<div id="footer"><a href="about">Profiles (Index)</a> ';

$ourNames = ['manuel'=>"Manuel Schmitt", 'michael'=>"Michael Iglhaut", 'moritz'=>"Moritz Mrosek", 'ramon'=>"Ramon Wilhelm", 'simon'=>"Simon Leister"];

$aboutDir='about/';
foreach (glob($aboutDir . '*.profile.html') as $phpfile)
{
	$fileParts=explode('.',basename($phpfile));
	echo ' &middot; <a href="' . ($phpfile) . '">' . $ourNames[$fileParts[0]] . '</a>';

}

echo ' &middot; <a href="AngularJS-Prototype/index.html">AngularJS-Prototype</a>';


?></div>



</body></html>
