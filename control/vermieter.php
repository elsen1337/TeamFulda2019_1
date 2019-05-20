<?php

//require('core-auth.php');
require('../core-mysqla.php');

require('../kernel/class-formhfv.php');
require('../kernel/class-ui.php');


header('Content-Type: text/html; charset=UTF-8');



?><!DOCTYPE HTML><html><head><title>HSF-MScAI-TP - StudyHome</title></head>

<body><h1>Controlpanel</h1><form action="<?=$_SERVER['SCRIPT_NAME']?>" method='post'><?php

GUI::printNotice('Übersicht aller Vermieter');

$tselect=array('M'=>'Mr.','F'=>'Mrs.'); 
$type=array('anrede'=>'selection','vname'=>'text','nname'=>'text','email'=>'mail','tel_nr'=>'text','mob_nr'=>'text','pwort'=>'password');
$pkey='vm_id';

$urs=FormFV::updateDB($_POST,$type,'new','vermieter',$pkey);


#var_dump($urs);
#var_dump($msdb);


if (strlen($_GET['edit']) > 0) {


$sql='SELECT v.* FROM vermieter AS v WHERE vm_id="'.$_GET['edit'].'"';
$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

echo '<table>';
echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('anrede'=>$tselect),true,false),array('Anrede','Vorname','Nachname','eMail','Phone','Mobile','Passwort'));
echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';


// Profilbild; Geringere Priorität
// Passwort: Kein Hash


} else {

echo '<table cellpadding="2" style="empty-cells:show"><tr><th class="trash">&#x232B;</th><th>Bezeichnung</th><th>#Wohnungen</th><th>Optionen</th></tr>';

$sql='SELECT v.vm_id, anrede, nname, vname, COUNT(w.wohn_id) AS cnt FROM vermieter AS v LEFT JOIN wohnung AS w ON v.vm_id = w.vm_id GROUP BY v.vm_id';
$msr=$msdb->query($sql); echo $msdb->error;

while ($row=$msr->fetch_assoc()) {
	
	echo '<tr><td><td>'.$row['nname'].', '.$row['vname'].'</td><td>'.$row['cnt'].'</td><td><a href="?edit='.$row['vm_id'].'">Bearbeiten</a>'."</td></tr>\n";
	
}

echo '</table>';

GUI::printInsert('<a href="?edit=new">Erstelle neuen Vermieter</a>');

}

?></form>

</body></html>
