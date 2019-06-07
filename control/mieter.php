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

GUI::printNotice('Übersicht aller Mieter');


// Profilbild; Geringere Priorität

$tselect=array('M'=>'Mr.','F'=>'Mrs.'); 
$type=array('anrede'=>'selection','vname'=>'text','nname'=>'text','email'=>'mail');
$ptbl='mieter';
$pkey='m_id';

FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'vdel');

$tokenKey='pwort';
if (array_key_exists($tokenKey,$_POST) && is_array($_POST[$tokenKey])) {
	foreach ($_POST[$tokenKey] as $uid => $plainpw) {
		if (strlen($plainpw) == 0) {continue;}
		$sql='UPDATE '.$ptbl.' SET '.$tokenKey.'=0x'.md5($plainpw).' WHERE '.$pkey.'='.$uid;
		$msdb->query($sql);
	}
}

#var_dump($msdb);


if (strlen($_GET['edit']) > 0) {


	$sql='SELECT v.* FROM '.$ptbl.' AS v WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="post"><table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type+array('pwort'=>'password=placeholder>Optional'),$row[$pkey],array('anrede'=>$tselect),true,false),array('Anrede','Vorname','Nachname','eMail','Passwort'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {

	echo '<table cellpadding="2"><tr><th>Vermieter-Name</th><th>#Bookmark</th><th title="Mieter / Gesamt">#Chat</th><th>Optionen</th></tr>';

	$sql='SELECT m.m_id, anrede, nname, vname, COUNT(DISTINCT f.wohn_id) AS fcnt, COUNT(DISTINCT c.vm_id) AS cdcnt, COUNT(c.mid) AS mcnt FROM '.$ptbl.' AS m LEFT JOIN m_favorit AS f ON m.m_id = m.m_id LEFT JOIN m_chat AS c ON c.m_id=m.m_id  GROUP BY m.m_id';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td>'.$row['nname'].', '.$row['vname'].'</td><td><a href="favorit.php?m_id='.$row[$pkey].'">'.$row['fcnt'].'</a></td><td><a href="chat.php?m_id='.$row[$pkey].'">'.$row['cdcnt'].' / '.$row['mcnt'].'</a></td><td><a href="?edit='.$row[$pkey].'">Bearbeiten</a>'.($row['cnt'] > 0 ? '' : ' &middot; <a href="?vdel[]='.$row[$pkey].'">Löschen</a>')."</td></tr>\n";
		
	}

	echo '</table>';

GUI::printInsert('<a href="?edit=new">Erstelle neuen Mieter</a>');

}

?></form>

</body></html>
