<?php


/*

Update mittels ddclient oder ganz einfach mittels WGET
------------------------------------------------------


1)

wget --no-check-certificate --http-user="<eMail>" --http-passwd="<Password>" -q https://hsftp.uber.space/nic/update?system=dyndns&hostname=cst-<UserID>.hsftp.uber.space&ip=<IPv6>

Wobei der IP Parameter einfach nur den Substring ip enthalten muss; also ipv6, myipv4 etc. [...] sind valide.
Die IPv6 gibt's mit dem Befehl: ip -o -6 addr show <INTERFACE; z. B enp2s0, wlan0> scope global | tr -s ' ' | cut -d ' ' -f 4
Der Hostname bzw. die Subdomain cst-<UserID>.hsftp.uber.space gibt es nicht wirklich; ich habe mich an das Namensschema gehalten.
Der Zugriff bzw. Weiterleitung erfolgt über den dynamischen URL Namespace hsftp.uber.space/redir/cst-<UserID>

/nic/dyndns?action=edit&started=1&hostname=YES&host_id=user.provider.tld&myip=2003:dd:ebf3:d1fc:956f:9f6f:341f:929d
/nic/update?system=dyndns&hostname=user.provider.tld&myip=2003:dd:ebf3:d1fc:956f:9f6f:341f:929d


2)

protocol=dyndns2
#use=web, web=checkip.dyndns.org
usev6=if, if=enp2s0
use=if, if=enp2s0

server=hsftp.uber.space
# Default: /nic/dyndns - Weiterleitung über .htaccess RewriteRules
# Manuell durch Angabe; Parameter wie DynDNS2 Standard:
# script=/dyndns/ddns-index.php

login=<eMail>
password=<Password>
cst-<USRID>.hsftp.uber.space

ddclient -daemon=0 -debug -verbose -noquiet 
ip -o -6 addr show enp2s0 scope global | sed -e 's/^.*inet6 \([^ ]\+\).*\
/\1/' cut ?



https://github.com/opnsense/plugins/pull/693
https://github.com/nicokaiser/Dyndns
https://development-blog.eu/ein-eigener-dynamischer-dns-dienst/
https://andrwe.org/linux/own-ddns

https://www.duschblog.de/2015/07/24/dyndns-auf-eigenen-server-betreiben/
https://lbader.de/blog/2015/12/07/eigener-ddns-server/
https://linux.robert-scheck.de/netzwerk/eigener-dyndns-dienst/

https://emanuelduss.ch/2013/07/eigener-dynamischer-dns-ddns-service-betreiben-eigenes-dyndns/
https://www.ionos.de/digitalguide/server/konfiguration/so-machen-sie-aus-dem-raspberry-pi-einen-dns-server/


*/


require('ddns-handler.php');
require('ddns-connector.php');

require('../core-mysqla.php');


/*

$sdss=file_exists(DDNSPersist::META_CACHE);
$msdb=new SQLite3(DDNSPersist::META_CACHE);
$dmts=filemtime(DDNSPersist::META_CACHE);
$svar=SQLite3::version();

if ($sdss==false) {DDNSPersist::setupSQLite($msdb);}

*/


#print_r($_GET);


if ($_GET['system']=='dyndns') {


	echo DDNSHandler::updateRecord($_GET['hostname']);




} elseif (strlen($_POST['ddnslabel']) > 0) {

	#DDNSPersist::insertDNSEntry($msdb,'p2ptv','WebRTC DVB-S Live TV');

	if ($_GET['action'] == 'create') {
		DDNSPersist::insertDNSEntry($msdb,$_POST['ddnslabel'],$_POST['ddnsabout']);
	} elseif ($_GET['action'] == 'erease') {
		DDNSPersist::ereaseEntry($msdb,$_POST['ddnslabel']);
	}
	
	exit('abuse');


} elseif (strlen($_GET['redir']) > 0) {

	DDNSHandler::formRedirect($_GET['redir']);


} else {
	
?><!DOCTYPE HTML><html>
<head><meta charset="utf-8">
<title>DynDNS</title><style type="text/css">
*.cmlcps {font-variant:small-caps}
li {margin: 10px 0}
</style><body>

<h1>URL-Namespace DynDNS Light Service</h1><?php

#<script type="text/javascript" src="action.js"></script>


#$sql='SELECT *, datetime(mts,"localtime") AS tzmts, strftime("%s",mts) AS tzuts FROM dyndnshostlabel ORDER BY mts DESC';
#$mrs=$msdb->query($sql);while ($dnsRecord=$mrs->fetchArray(SQLITE3_ASSOC)) {


echo '<ul>'."\n"; echo $msdb->error;
$mrs=DDNSPersist::getEntries(); $curTime=time();

while ($dnsRecord=$mrs->fetch_assoc()) {


	$ipString=DDNSHandler::formatIPAddr($dnsRecord);

	echo '<li><strong>',$dnsRecord['abt'],'</strong> &middot; (<em>Aktualisierung: <span style="color:'.(count($ipString) == 0 || $dnsRecord['tzuts'] + 86400 < $curTime ? 'red' : 'green').'">',date('d.m.Y - H:i',$dnsRecord['tzuts']), '</span> | ', $dnsRecord['cnt'],'</em>)';
	
	if (count($ipString) > 0) {
		echo '<br><em>SubDomain</em>: <span class="cmlcps">',$dnsRecord['sym'], '</span> @ '; $ipShow=array();
		if (array_key_exists('6',$ipString)) {$ipShow[]='IPv6 <a href="http://['.$ipString['6'].']">'.$ipString['6'].'</a>';}
		if (array_key_exists('4',$ipString)) {$ipShow[]='IPv4 <a href="http://'.$ipString['4'].'">'.$ipString['4'].'</a>';}
		echo implode(' &middot; ',$ipShow);
	}
	
	echo '<br>Bookmarkbar als Redir-Link: <a class="cmlcps" href="redir/'.$dnsRecord['sym'].'">'.$dnsRecord['abt'].' @ '.$dnsRecord['sym'].'</a>'."\n";

}

echo '<li><form style="" method="post" onsubmit="this.action=\'?action=create\'"><input placeholder="Domain-Identifier" name="ddnslabel" type="text"><input placeholder="Beschreibung" name="ddnsabout" type="text"><input value="Anlegen" type="submit"></form></li>';
echo '</ul></body><p>Individual-Leistung im TeamProjekt - Modul &middot; SS19 &middot; Simon Leister</p></html>';

}


?>
