<?php

//require('core-auth.php');
require('../core-mysqla.php');

require('../kernel/class-formhfv.php');
require('../kernel/class-ui.php');


header('Content-Type: text/html; charset=UTF-8');


/*
wohn_id	int(11) Auto-Inkrement	
vm_id	int(11)	
name	varchar(47)	
beschr	varchar(3070)	
plz	varchar(15)	
ort	varchar(47)	
preis	decimal(6,2)	
entf_meter	int(11)	
entf_min	int(11)
*/

?><!DOCTYPE HTML><html><head>
<title>HSF-MScAI-TP - StudyHome</title></head>
<body><h1>Controlpanel</h1><form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post"><?php


GUI::printNotice('Wohnungen');


$pkey='wohn_id';
$type=array('name'=>'text','beschr'=>'area','vm_id'=>'selection','plz'=>'number','ort'=>'text','preis'=>'number=step>0.01','entf_meter'=>'number','entf_min'=>'number');

FormFV::updateDB($_POST,$type,'new','wohnung',$pkey);

#var_dump($msdb);


if (strlen($_GET['edit']) > 0) {


$vmlist=FormFV::getSelectAdv('SELECT vm_id, CONCAT(nname, ", ", vname) AS label FROM vermieter');

$sql='SELECT w.* FROM wohnung AS w WHERE wohn_id="'.$_GET['edit'].'"';
$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

echo '<table>';
echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('vm_id'=>$vmlist),true,false),array('Bezeichnung','Beschreibung','Vermieter','ZIPCode','Ort','Preis','Entfernung zur UNI in Metern','Entfernung zur UNI in Minuten per ÖPNV'));
echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {


$sql='SELECT w.*, v.* FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id'; $msr=$msdb->query($sql);
echo '<table cellpadding="2" style="empty-cells:show"><tr><th class="trash">&#x232B;</th><th>Bezeichnung</th><th>Vermieter</th><th>Optionen</th></tr>';

$sql='SELECT w.wohn_id, w.name, nname, vname FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id';
$msr=$msdb->query($sql); echo $msdb->error;

while ($row=$msr->fetch_assoc()) {
	
	echo '<tr><td><td>'.$row['name'].'</td><td>'.$row['nname'].', '.$row['vname'].'</td><td>'.$row['cnt'].'</td><td><a href="?edit='.$row[$pkey].'">Bearbeiten</a>'."</td></tr>\n";
	
}

echo '</table>';

GUI::printInsert('<a href="?edit=new">Erstelle neue Wohnung</a>');

}

?></table>



</form>

</body></html>
