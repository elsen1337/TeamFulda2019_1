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

<body class="whn"><h1>Controlpanel</h1><form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post"><?php


GUI::printNotice('Wohnungen');


$pkey='wohn_id';
$type=array('visible'=>'selection','name'=>'text','beschr'=>'area','vm_id'=>'selection','str'=>'text','plz'=>'number','ort'=>'text','preis'=>'number=step>0.01','entf_meter'=>'number','entf_min'=>'number');

FormFV::updateDB($_POST,$type,'new','wohnung',$pkey,'wdel');



if (strlen($_GET['edit']) > 0) {


	$vmlist=FormFV::getSelectAdv('SELECT vm_id, CONCAT(nname, ", ", vname) AS label FROM vermieter');

	$sql='SELECT w.* FROM wohnung AS w WHERE wohn_id="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

	echo '<table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('vm_id'=>$vmlist,'visible'=>array(0=>'Zurückgehalten',1=>'Sichtbar')),true,false),array('Status','Bezeichnung','Beschreibung','Vermieter','Straße','PLZ/ZIP','Stadt/Ort','Preis/Woche','Entfernung zum Campus (Metern)','ÖPNV-Minuten zum Campus'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {


	$sql='SELECT w.*, v.* FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id'; $msr=$msdb->query($sql);
	echo '<table cellpadding="2" style="empty-cells:show"><tr><th>Bezeichnung</th><th>Vermieter</th><th>Optionen</th></tr>';

	$sql='SELECT w.wohn_id, w.visible, w.name, nname, vname FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id ORDER BY visible DESC';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td class="'.($row['visible'] > 0 ? '' : 'hidden').'">'.$row['name'].'</td><td>'.$row['nname'].', '.$row['vname'].'</td><td><a href="?edit='.$row[$pkey].'">Bearbeiten</a> &middot; <a href="?wdel[]='.$row[$pkey].'">Löschen</a>'."</td></tr>\n";
		
	}

	echo '</table>';

	GUI::printInsert('<a href="?edit=new">Erstelle neue Wohnung</a>');

}

?></form>

</body></html>
