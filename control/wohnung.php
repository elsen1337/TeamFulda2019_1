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

<body class="whn"><h1>Wohnungen (Controlpanel)</h1><?php


GUI::printNotice('Bearbeitung von Stammdaten, Attributen und Bildern');


$pkey='wohn_id';
$ptbl='wohnung';

$type=array('visible'=>'selection','name'=>'text','beschr'=>'area','vm_id'=>'selection','str'=>'text','plz'=>'number','ort'=>'text','preis'=>'number=step>0.01','entf_meter'=>'number','entf_min'=>'number');

FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'wdel');



if (strlen($_GET['attr']) > 0) {
	
	
	$tokenKey='val';
	if (array_key_exists($tokenKey,$_POST) && is_array($_POST[$tokenKey])) {
		foreach ($_POST[$tokenKey] as $aid => $val) {
			if (strlen($val) > 0) {
				$sql='INSERT INTO w_attrvals (wohn_id,aid,val) VALUES ('.$_GET['attr'].', '.$aid.', "'.$val.'") ON DUPLICATE KEY UPDATE val=VALUES(val)';
				$msdb->query($sql);
			} else {
				$sql='DELETE FROM w_attrvals WHERE wohn_id='.$_GET['attr'].' AND aid='.$aid;
				$msdb->query($sql);
			}
		}
	}


	$sql='SELECT m.aid, m.name, v.val  FROM w_attrmeta AS m LEFT JOIN w_attrvals AS v ON m.aid=v.aid ORDER BY m.vsb DESC, m.rdr ASC';
	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'?attr='.$_GET['attr'].'" method="post"><table><tr><th>Eigenschaftname</th><th>Eigenschaftswert</th></tr>';
	
	$msr=$msdb->query($sql); echo $msdb->error;
	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td class="'.($row['vsb'] > 0 ? '' : 'hidden').'">'.$row['name'].'</td><td><input name="val['.$row['aid'].']" value="'.$row['val'].'" type="text"></td></tr>'."\n";
		
	}	
	
	echo' </table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';
	
	
} elseif (strlen($_GET['edit']) > 0) {



	$vmlist=FormFV::getSelectAdv('SELECT vm_id, CONCAT(nname, ", ", vname) AS label FROM vermieter');

	$sql='SELECT w.* FROM '.$ptbl.' AS w WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="post"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('vm_id'=>$vmlist,'visible'=>array(0=>'Zurückgehalten',1=>'Sichtbar')),true,false),array('Status','Bezeichnung','Beschreibung','Vermieter','Straße','PLZ/ZIP','Stadt/Ort','Preis/Woche','Entfernung zum Campus (Metern)','ÖPNV-Minuten zum Campus'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {


	$sql='SELECT w.*, v.* FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id'; $msr=$msdb->query($sql);
	echo '<table cellpadding="2" style="empty-cells:show"><tr><th>Bezeichnung</th><th>Vermieter</th><th>Optionen</th></tr>';

	$sql='SELECT w.wohn_id, w.visible, w.name, nname, vname FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id ORDER BY visible DESC';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td class="'.($row['visible'] > 0 ? '' : 'hidden').'">'.$row['name'].'</td><td>'.$row['nname'].', '.$row['vname'].'</td><td><a href="?edit='.$row[$pkey].'">Bearbeiten (Stammdaten)</a> &middot; <a href="?attr='.$row[$pkey].'">Bearbeiten (Attribute)</a> &middot; <a href="?wdel[]='.$row[$pkey].'">Löschen</a>'."</td></tr>\n";
		
	}

	echo '</table>';

	GUI::printInsert('<a href="?edit=new">Erstelle neue Wohnung</a>');

}

?></form>

</body></html>
