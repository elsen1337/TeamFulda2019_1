<?php


class DDNSHandler {


	// Standardeinstellungen
	
	const AUTO_INSERT=false; # Kein Hinzufügen nicht existierender Einträge
	const NO_AUTH=false; # Kein Authentifizierung 
	
	const USE_REQ_IP=false; # IP aus Remote IP in Pool ?
	const USE_IP4=false; # Verwendung von IPv4 ?
	
	
	public static function sendHeader($code, $msg) {
	
		header( implode("\x20", array($_SERVER['SERVER_PROTOCOL'], $code, $msg) ) );
		
	}
	
	
	
	public static function getConsideredIPAddrSources() {
		
		$ipAddr2Check=array();
		$ipAddr2Use=array('4'=>array(),'6'=>array());
		if (self::USE_REQ_IP) {$ipAddr2Check[]=$_SERVER['REMOTE_ADDR'];}
		
		// RegEx IP
		foreach ($_GET as $key => &$ipAddr) {
			if (strpos($key,'ip') > -1) {
				$ipAddr2Check[]=$ipAddr;
			}
		}
		
		/*
		print_r($ipAddr2Check);
		foreach (array('myip','ipv6','ipv4') as $aKey) {
			if (array_key_exists($aKey, $_GET)) {
				$ipAddr2Check[]=$_GET[$aKey];
			}
		}
		*/

		foreach ($ipAddr2Check as &$ipAddr) {

			if(filter_var($ipAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$ipAddr2Use['6'][]=$ipAddr;
			}

			if(filter_var($ipAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				$ipAddr2Use['4'][]=$ipAddr;
			}
		}

		# IP Filter
		if (self::USE_IP4===false) {unset($ipAddr2Use['4']);}
		foreach ($ipAddr2Use as &$arrTmp) {
			$arrTmp=current(array_filter($arrTmp));
		}

		return array_filter($ipAddr2Use);
			
	}


	public static function formatIPAddr($iparr) {

		$ipAddrFrmt=array();
		foreach (array('6','4') as $typ) {
			$key='bipv'.$typ;
			if (array_key_exists($key,$iparr)) {
				if (strlen($iparr[$key]) < 4) {continue;}
				$ipAddrFrmt[$typ]=inet_ntop($iparr[$key]);
			}
		}
		
		return $ipAddrFrmt;
	}
	
		
	public static function formRedirect($domainLabel) {
		
		$curRecord=DDNSPersist::getEntry($domainLabel);
		
		#DynDNS2: dnserr
		if (count($curRecord) == 0) {
		self::sendHeader(404,'Not Found');
			echo 'DNS-Error: Keine bekannte Domain';
			return false;

		}
		
		
		$curIP=self::formatIPAddr($curRecord);
		$ipTyp=key($curIP); $ipStr=$curIP[$ipTyp];
		
		# Keine IPAdresse
		if (count($curIP) > 0) {
			$addParams=array_filter($_GET,function($k) {return $k<>'redir' &&  $k<>'url';},ARRAY_FILTER_USE_KEY);
			header('Location: http'.(strlen($_SERVER['HTTPS']) > 0 ? 's' : '').'://'.($ipTyp=='6' ? '['.$ipStr.']' : $ipStr).$_GET['url'].(count($addParams) > 0 ? '?'.http_build_query($addParams) : ''));
			return true;
		} else {
			self::sendHeader(404,'Not Found');
			echo 'DNS-Error: Keine IP Adresse Domain';
			return false;
		}
		
	}	
	
	
	public static function updateRecord($dyndnsSubdomain,$httpHost=null,$oneDomainOnly=true) {
		
		if (empty($httpHost)) {$httpHost=$_SERVER['HTTP_HOST'];}
		if (empty($dyndnsSubdomain)) {return('badagent');}
		
		$dyndnsSubdomainEnd=strpos($dyndnsSubdomain,$httpHost);
		$dyndnsSubdomainLabel=substr($dyndnsSubdomain,0,$dyndnsSubdomainEnd-1);


		# Sonderfall: Update von nur einer Domain...
		if ($oneDomainOnly && strpos(',',$dyndnsSubdomain) > -1) {self::sendHeader(403,'Forbidden'); return('numhost');}

		# Kein FQDN bzw. keine SubDomain der Domain
		if ($dyndnsSubdomainEnd < 0) {self::sendHeader(400,'Bad Request'); return('notfqdn');}


		# $_SERVER['HTTP_AUTHORIZATION']
		
		$dyndnsAuthUser=$_SERVER['PHP_AUTH_USER'];
		$dyndnsIsUserAuth=DDNSPersist::authorizeUser($dyndnsAuthUser,$_SERVER['PHP_AUTH_PW'],self::NO_AUTH,$usrInfo);
		$dyndnsIsDomainAuth=false;
		
		if ($dyndnsIsUserAuth) {
			$dyndnsIsDomainAuth=DDNSPersist::authorizeDomain($dyndnsAuthUser,$dyndnsSubdomainLabel,$usrInfo);
			
			if ($dyndnsIsDomainAuth===false) {
				self::sendHeader(404,'Not Found');
				return('nohost');
			}
			
		} else {
			self::sendHeader(401,'Unauthorized');
			return('badauth');
			
		}



		$ipAddr2Use=DDNSHandler::getConsideredIPAddrSources();
		$ipAvailable=count($ipAddr2Use) > 0;
		#print_r($ipAddr2Use);


		if ($ipAvailable) {

			$curRecord=DDNSPersist::getEntry($dyndnsSubdomainLabel); # Default []
			#print_r($curRecord);


			if (count($curRecord) == 0) {
				# Array NOT NULL / NOT Empty => AutoInsertAllowed $dyndnsIsAuth
				if (DDNSHandler::AUTO_INSERT===false && $dyndnsIsUserAuth===false) {
					self::sendHeader(404,'Not Found');
					return('nohost');
				}
				
				DDNSPersist::insertDNSEntry($dyndnsSubdomainLabel,'AUTO - Insert (!)');
				$curRecord=array(); # Notwendig ?
			}


			if ($ipAddr2Use['4']==inet_ntop($curRecord['bipv4']) && $ipAddr2Use['6']==inet_ntop($curRecord['bipv6'])) {
				#self::sendHeader(304,'Not Modified');
				self::sendHeader(510,'Not Extended');
				return('nochg '.implode("\x20",$ipAddr2Use));
			}


			$chRec=DDNSPersist::updateIPData($dyndnsSubdomainLabel,$ipAddr2Use);
			
			if ($chRec > 0) {
				return('good '.implode("\x20",$ipAddr2Use));
			} else {
				#self::sendHeader(503, 'Service Unavailable');
				self::sendHeader(510,'Not Extended');
				return('fatal'); #911 dnserr
			}

		} else {
			self::sendHeader(304,'Not Modified');
			return('nochg');
		}
	
		
	}
	


	public static function normalizeFileName($str,$asa=false,$ext=null) {

	/*
		$se = array('ä','ö','ü','ß');
		$re = array('ae','oe','ue','ss');
		$r=str_replace($se, $re, $r);
	*/

		$pia=pathinfo($str);
		// Unicode-Modifier und Ausgangszeichensatz (SETLOCALE+ICONV)
		setlocale(LC_COLLATE, array('de_DE.utf8','deu.utf8'));
		setlocale(LC_COLLATE, array('de_DE.iso-8859-1','deu.iso-8859-1'));
		$a=preg_split('#[^\\pL\d]+#',$pia['filename'],null,PREG_SPLIT_NO_EMPTY);

		$str=iconv('iso-8859-1', 'us-ascii//TRANSLIT', implode('-',$a) );
		$str=preg_replace('#\"([oua])#iu', '\1e', $str);
		$str=preg_replace('#[^-\w]+#', '', $str);

		$hyp=empty($str) ? 'no-mark' : strtolower($str);
		$hyp=trim(preg_replace('/(\d+)/i', '-$1-', $hyp),'-');
		$hyp=str_replace('.', '', $hyp); $hyp=preg_replace('/\-+/i', '-', $hyp);

		if (is_string($ext) && strlen($ext) > 0) {$pia['extension']=($ext);}
		return $asa ? array($hyp,$pia['extension']) : $hyp.'.'.$pia['extension'];

	}
	
		
}


?>
