 
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

<body class="appmeta"><h1>Wohnungseigenschaften (Controlpanel)</h1><form action="<?=$_SERVER['SCRIPT_NAME']?>" method='post'><?php

GUI::printNotice('Dynmaische Eigenschaften der Wohungen');

$type=array('vsb'=>'checkbox','rdr'=>'number','name'=>'text');
$ptbl='w_attrmeta';
$pkey='aid';

FormFV::updateDB($_POST,$type,'new',$ptbl,$pkey,'a_del');

#var_dump($msdb);


if (strlen($_GET['edit']) > 0) {


	$sql='SELECT * FROM '.$ptbl.' WHERE '.$pkey.'="'.$_GET['edit'].'"';
	$mrs=$msdb->query($sql); if ($mrs->num_rows > 0) {$row=$mrs->fetch_assoc();} else {$row[$pkey]='new';}

	echo '<table>';
	echo FormFV::printVertical(FormFV::makeHTML($row,$type,$row[$pkey],array('vsb'=>'1'),true,false),array('Aktiv','Reihenfolge','Name'));
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';



} else {

	echo '<table cellpadding="2" style="empty-cells:show"><tr><th>Aktiv</th><th>#Reihenfolge</th><th>Name</th><th>Verwendete Atttribute / Optionen</th></tr>';

	$sql='SELECT m.*, COUNT(v.wohn_id) AS cnt FROM '.$ptbl.' AS m LEFT JOIN w_attrvals AS v ON m.aid = v.aid GROUP BY m.aid';
	$msr=$msdb->query($sql); echo $msdb->error;

	while ($row=$msr->fetch_assoc()) {
		
		echo '<tr><td>'.FormFV::makeHTML($row,$type,$row[$pkey],array('vsb'=>'1'),false,false).'<td><a href="?edit='.$row[$pkey].'">Bearbeiten</a> &middot; '.($row['cnt'] > 0 ? 'Keine Löschung ('.$row['cnt'].' Verwendungen)' : '<a href="?a_del[]='.$row[$pkey].'">Löschen</a>').'</td></tr>'."\n";
		
	}

	echo '<tr><td>'.FormFV::makeHTML(array(),$type,'new',array('vsb'=>'1'),false,false)."</tr>\n";
	echo '</table><p><input type="submit" value="Erstellen &middot; Aktualisieren"></p>';

	GUI::printInsert('<a href="?edit=new">Erstelle neues Attribut</a>');

}

?></form>

</body></html>
