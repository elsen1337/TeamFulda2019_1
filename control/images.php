<?php

//require('core-auth.php');


require('../core-mysqla.php');

require('../kernel/class-formhfv.php');
require('../kernel/class-ui.php');


header('Content-Type: text/html; charset=UTF-8');


?><!DOCTYPE HTML><html><head>
<title>HSF-MScAI-TP - StudyHome</title>
<link rel="stylesheet" href="../client/layout/base-messages.css">
<link rel="stylesheet" href="control.css"></head>

<body class="wimg"><h1>Wohnungsbilder (Controlpanel)</h1><?php


require('../kernel/class-appartimg.php');

$type=array('wohn_id'=>'selection','rdr'=>'number','alt'=>'text');
$ptbl='w_image';
$pkey='bild_id';
$nkey='new';


//FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey);


$uploadBaseDir='../'.AppartImage::$uploadBaseDir;
$dirThumb=AppartImage::$dirThumb;
$dirOrg=AppartImage::$dirOrg;

if (file_exists($uploadBaseDir)==false) {mkdir($uploadBaseDir);}
chdir($uploadBaseDir);

if (file_exists($dirThumb)==false) {mkdir($dirThumb);}
if (file_exists($dirOrg)==false) {mkdir($dirOrg);}



function reduceIndexedFields2Array(&$src,$fields,$keys,$reqkeys=array()) {

	$retarr=array();
	foreach ($fields as $field) {
		
		if (array_key_exists($field,$src) && is_array($src[$field])) {
			
			$ref=&$src[$field];
			$sng=is_array($keys)===false;
			if ($sng) {$keys=array($keys);}
			foreach ($keys as $key) {
				if (array_key_exists($key,$ref)) {
					$retarr[$key][$field]=$ref[$key];
				}
			}
			
		}
		
		
	}
	
	foreach ($retarr as &$obj) {
		
	}
	
	return $retarr;
	
}


$iDelKey='imgDel';
if (array_key_exists($iDelKey,$_GET)) {
	
	foreach ($_GET[$iDelKey] as $bid) {
		
		AppartImage::removeImage($bid);
	
	}

}



$uKey='bild';
if (array_key_exists($uKey,$_FILES)) {
	
	require('../kernel/class-string.php');
	require('../kernel/image-support-thumb.php');
	
	// In PHP 7.3 BuiltIn
	#require('../kernel/image-support-bmp.php');


	#print_r($_FILES);
	
	$redArr=reduceIndexedFields2Array($_POST,array_keys($type),$nkey);
	#print_r($redArr);
	
	$bildUpload=&$_FILES[$uKey];
	AppartImage::uploadImage($bildUpload,$redArr[$nkey]);
	
	
	foreach ($bildUpload['error'] as $bid => $val) {

		$_GET['wid']=$_POST['wohn_id'][$bid];
	
	}
	 
	
	
}




if (strlen($_GET['edit']) > 0) {


	GUI::printNotice('JPG/PNG/GIF/BMP mit maximal 4MB. Vorschaugenerierung als JPG.');

	$whlist=FormFV::getSelectAdv('SELECT wohn_id, name FROM wohnung WHERE wohn_id='.$_GET['wid']);


	$sql='SELECT * FROM '.$ptbl.' WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';$row['wohn_id']=$_GET['wid'];}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?wid='.$_GET['wid'].'" method="post" enctype="multipart/form-data"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type+array(),$row[$pkey],array('wohn_id'=>$whlist),true,false),array('Wohnungname','Reihenfolge','Untertitel'));
	echo '</table><p><input type="file" name="bild['.$row[$pkey].']" style="font-variant:small-caps"></p><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {

	GUI::printNotice('Filterung nach Wohnung über Link aus Wohnungsübersicht.');

	echo '<table cellpadding="2"><tr><th>#</th><th>Beschreibung</th><th>Name</th><th>Optionen</th></tr>';

	$wohnView=strlen($_GET['wid']) > 0;
	$sql='SELECT i.bild_id, i.rdr, i.alt, i.bild, i.wohn_id, w.name FROM '.$ptbl.' AS i JOIN wohnung AS w ON w.wohn_id=i.wohn_id'.($wohnView ? ' WHERE i.wohn_id='.$_GET['wid'] : '').' ORDER BY i.rdr';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td>#'.$row['rdr'].'<td>'.$row['alt'].'<td>'.($row['bild'] ? '<img title="'.$row['bild'].'" src="'.implode('/', array($uploadBaseDir,$dirThumb,AppartImage::formThumbFileName($row['bild']) ) ).'">' : $row['bild']);
		echo '<td><a href="?edit='.$row[$pkey].'&wid='.$row['wohn_id'].'">Bearbeiten</a> &middot; <a href="?'.$iDelKey.'[]='.$row[$pkey].'">Löschen</a>'.'</td></tr>'."\n";
		
	}

	echo '</table>';

	if ($wohnView) {
		GUI::printInsert('<a href="?edit=new&wid='.$_GET['wid'].'">Erstelle neues Bild</a>');
	}

}

?></body></html>
