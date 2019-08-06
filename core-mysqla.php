<?php


$msdb=mysqli_connect('studyhomedb.cqvrg2kdtx7n.us-east-1.rds.amazonaws.com:3306','root','rootroot','hsftp_booking');
$msdb->set_charset('utf8');

// Hat was mit zulassbaren NULL Werten in Saplten beim Einfügen zu tun; entspricht der Aufhebung der standardmäßig aktivierten strikten Auslegung des Schemas
$msdb->query('SET SQL_MODE = ""');


?>
