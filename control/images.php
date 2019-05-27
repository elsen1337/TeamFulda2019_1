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


$type=array('alt'=>'text','wohn_id'=>'selection');
$ptbl='w_image';
$pkey='bild_id';


FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey);


$uploadBaseDir='../images';
$dirThumb='thumb';
$dirOrg='normal';


function formThumbFileName($img) {
	$pathInfo=pathinfo($img);
	return $pathInfo['filename'].'.jpg';
}


$iDelKey='imgDel';
if (array_key_exists($iDelKey,$_GET)) {
	foreach ($_GET[$iDelKey] as $bid) {
		
		$row=$msdb->query('SELECT * FROM '.$ptbl.' WHERE '.$pkey.'='.$bid)->fetch_assoc();
		
		chdir($uploadBaseDir); $accRes=0;

		$accRes+=(int)@unlink($dirThumb.'/'.formThumbFileName($row['bild']));
		$accRes+=(int)@unlink($dirOrg.'/'.$row['bild']);
		
		$msdb->query('DELETE FROM '.$ptbl.' WHERE '.$pkey.'='.$bid);
	
	}

}



$uKey='bild';
if (array_key_exists($uKey,$_FILES)) {
	
	require('../kernel/class-string.php');
	require('../kernel/image-support-thumb.php');
	
	// In PHP 7.3 BuiltIn
	#require('../kernel/image-support-bmp.php');

	
	chdir($uploadBaseDir);
	if (file_exists($dirThumb)==false) {mkdir($dirThumb);}
	if (file_exists($dirOrg)==false) {mkdir($dirOrg);}
	
	#print_r($_FILES);
	
	$bildUpload=&$_FILES[$uKey];
	foreach ($bildUpload['error'] as $bid => $val) {
		
		if ($val > 0) {continue;}
		if ($bildUpload['size'][$bid] > 4 * 1024 * 1024 || $bildUpload['size'][$bid] == 0) {continue;}
		if (strpos($bildUpload['type'][$bid],'image') === false) {continue;}
		
		# stringNormalize::normalizeURL File
		$oldFileName = $bildUpload['name'][$bid];
		$pathInfoOld=pathinfo($oldFileName);
	
		// Neu erstellter Datensatz für Bild
		$bsqlid=is_int($bid)===false ? $msdb->insert_id : $bid;
		$_GET['wid']=$_POST['wohn_id'][$bid];
			
		// $r=trim(preg_replace('/(\d+)/i', '-$1-', $r),'-');
		// $r=str_replace('.', '', $r); $r=preg_replace('/\-+/i', '-', $r);

		$newFileName = str_pad($bsqlid, 5, "0", STR_PAD_LEFT).'-'.stringNormalize::normalizeURL($pathInfoOld['filename']).'.'.$pathInfoOld['extension'];
		$targetFile = implode('/',array($dirOrg, $newFileName));

		move_uploaded_file($bildUpload['tmp_name'][$bid],$targetFile);
		file_put_contents($dirThumb .'/'.substr($newFileName,0,(false==$p=strrpos($newFileName,'.')) ? strlen($newFileName) : $p).'.jpg', image_get_thumb_file ($targetFile, 100, 100, 65));
	
		$msdb->query('UPDATE w_image SET bild = "'.$newFileName.'" WHERE bild_id='.$bsqlid);
		
	}
	
}




if (strlen($_GET['edit']) > 0) {


	GUI::printNotice('JPG/PNG/GIF/BMP mit maximal 4MB. Vorschaugenerierung als JPG.');

	$whlist=FormFV::getSelectAdv('SELECT wohn_id, name FROM wohnung WHERE wohn_id='.$_GET['wid']);


	$sql='SELECT * FROM '.$ptbl.' WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';$row['wohn_id']=$_GET['wid'];}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?wid='.$_GET['wid'].'" method="post" enctype="multipart/form-data"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type+array(),$row[$pkey],array('wohn_id'=>$whlist),true,false),array('Untertitel','Wohnungname'));
	echo '</table><p><input type="file" name="bild['.$row[$pkey].']" style="font-variant:small-caps"></p><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {

	GUI::printNotice('Filterung nach Wohnung über Link aus Wohnungsübersicht.');

	echo '<table cellpadding="2" style="empty-cells:show"><tr><th>Beschreibung</th><th>Name</th><th>Optionen</th></tr>';

	$wohnView=strlen($_GET['wid']) > 0;
	$sql='SELECT i.alt, i.bild_id, i.bild, i.wohn_id, w.name FROM '.$ptbl.' AS i JOIN wohnung AS w ON w.wohn_id=i.wohn_id'.($wohnView ? ' WHERE i.wohn_id='.$_GET['wid'] : '');
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td>'.$row['alt'].'<td>'.($row['bild'] ? '<img title="'.$row['bild'].'" src="'.implode('/', array($uploadBaseDir,$dirThumb,formThumbFileName($row['bild']) ) ).'">' : $row['bild']).'<td><a href="?edit='.$row[$pkey].'&wid='.$row['wohn_id'].'">Bearbeiten</a> &middot; <a href="?'.$iDelKey.'[]='.$row[$pkey].'">Löschen</a>'.'</td></tr>'."\n";
		
	}

	echo '</table>';

	if ($wohnView) {
		GUI::printInsert('<a href="?edit=new&wid='.$_GET['wid'].'">Erstelle neues Bild</a>');
	}

}

?></body></html>
