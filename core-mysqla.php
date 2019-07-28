<?php


$msdb=mysqli_connect('localhost','root','root','hsftp_booking');
$msdb->set_charset('utf8');

// Hat was mit zulassbaren NULL Werten in Saplten beim Einfügen zu tun; entspricht der Aufhebung der standardmäßig aktivierten strikten Auslegung des Schemas
$msdb->query('SET SQL_MODE = ""');


?>
