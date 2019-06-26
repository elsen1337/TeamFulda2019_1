<?php

//require('core-auth.php');

require('../core-mysqla.php');

require('../kernel/class-formhfv.php');
require('../kernel/class-ui.php');


header('Content-Type: text/html; charset=UTF-8');


?><!DOCTYPE HTML><html><head>
<title>HSF-MScAI-TP - StudyHome</title>
<link rel="stylesheet" href="../client/layout/base-messages.css">
<link rel="stylesheet" href="control.css">
</head>

<body class="vmt"><h1>Controlpanel</h1><?php

GUI::printNotice('Übersicht aller Vermieter');


// Profilbild; Geringere Priorität


require('../kernel/class-lessor.php');


$tselect=array('M'=>'Mr.','F'=>'Mrs.'); 
$type=array('anrede'=>'selection','vname'=>'text','nname'=>'text','email'=>'mail','tel_nr'=>'text','mob_nr'=>'text');  // Lessor::$formFields
$ptbl='vermieter'; // Lessor::$entSQLTable
$pkey='vm_id'; // Lessor::$entPrimKey

FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'vdel');

$tokenKey='pwort';
if (array_key_exists($tokenKey,$_POST) && is_array($_POST[$tokenKey])) {
	foreach ($_POST[$tokenKey] as $uid => $plainpw) {
		if (strlen($plainpw) == 0) {continue;}
		$sql='UPDATE '.$ptbl.' SET '.$tokenKey.'=0x'.Lessor::cryptPasswort($plainpw).' WHERE '.$pkey.'='.$uid;
		$msdb->query($sql);
	}
}

#var_dump($msdb);


if (strlen($_GET['edit']) > 0) {


	$sql='SELECT v.* FROM '.$ptbl.' AS v WHERE vm_id="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="post"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type+array('pwort'=>'password=placeholder>Optional'),$row[$pkey],array('anrede'=>$tselect),true,false),array('Anrede','Vorname','Nachname','eMail','Phone','Mobile','Passwort'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {

	echo '<table cellpadding="2" style="empty-cells:show"><tr><th>Vermieter-Name</th><th>#Wohnungen</th><th title="Mieter / Gesamt">#Chat</th><th>Optionen</th></tr>';

	$sql='SELECT v.vm_id, anrede, nname, vname, COUNT(w.wohn_id) AS cnt, COUNT(DISTINCT ABS(c.m_id)) AS cdcnt, COUNT(DISTINCT c.mid) AS mcnt FROM '.$ptbl.' AS v LEFT JOIN wohnung AS w ON v.vm_id = w.vm_id LEFT JOIN m_chat AS c ON c.vm_id=v.vm_id GROUP BY v.vm_id';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td>'.$row['nname'].', '.$row['vname'].'</td><td>'.$row['cnt'].'</td><td><a href="chat.php?vm_id='.$row[$pkey].'">'.$row['cdcnt'].' / '.$row['mcnt'].'</a></td><td><a href="?edit='.$row['vm_id'].'">Bearbeiten</a>'.($row['cnt'] > 0 ? '' : ' &middot; <a href="?vdel[]='.$row[$pkey].'">Löschen</a>')."</td></tr>\n";
		
	}

	echo '</table>';

GUI::printInsert('<a href="?edit=new">Erstelle neuen Vermieter</a>');

}

?></form>

</body></html>
