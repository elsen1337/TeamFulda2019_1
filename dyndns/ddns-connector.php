<?php




class DDNSPersist {



	private static $dataBaseLink='msdb';



	public static function authorizeUser($dyndnsAuthUser,$dyndnsAuthToken,$useAuth,&$userProp) {

		if ($useAuth==false) {
			
			#$sql='SELECT vm_id FROM vermieter WHERE email="'.$dyndnsAuthUser.'" AND pwort=0x'.md5($dyndnsAuthToken).'';
			
			$sql='SELECT vm_id FROM vermieter WHERE email="'.$dyndnsAuthUser.'" AND pwort=UNHEX(MD5("'.$dyndnsAuthToken.'"))';
			$mrs=$GLOBALS[self::$dataBaseLink]->query($sql);
						
			if ($mrs->num_rows == 1) {
				$userProp=$mrs->fetch_object();
				return true;
			} else {
				return false;
			}

		} else {
		
			return true;
		
		}

	}


	public static function authorizeDomain($dyndnsAuthUser,$dyndnsSubdomainLabel,$addUsrInfo=null) {

		list($trsh, $vmid)=explode('-',$dyndnsSubdomainLabel);
		
		$userKey='vm_id';
		if ($addUsrInfo <> null && property_exists($addUsrInfo,$userKey)) {
			$dyndnsAuthUser=$addUsrInfo->{$userKey};
		}

		if (is_numeric($dyndnsAuthUser)) {
			return ($vmid==$dyndnsAuthUser);
			
		} else {

			$sql='SELECT v.vm_id FROM vermieter AS v LEFT JOIN v_dyndns AS d ON v.vm_id=d.vm_id WHERE email="'.$dyndnsAuthUser.'"';
			$mrs=$GLOBALS[self::$dataBaseLink]->query($sql);
 			
 			// echo $GLOBALS[self::$dataBaseLink]->error;

			if ($mrs->num_rows > 0) {
				$usrInfo=$mrs->fetch_object();
				return ($vmid==$usrInfo->vm_id);
			} else {
				return false;
			}
		
		}

	}



	public static function getEntry($dyndnsSubdomainLabel) {

		list($trsh, $vmid)=explode('-',$dyndnsSubdomainLabel);

		$sql='SELECT * FROM v_dyndns WHERE vm_id="'.$vmid.'"';
		$mrs=$GLOBALS[self::$dataBaseLink]->query($sql);

		if ($mrs->num_rows > 0) {
			return $mrs->fetch_assoc();
		} else {
			return array();
		}
		
	}



	public static function ereaseEntry($dyndnsSubdomainLabel) {
	
		list($trsh, $vmid)=explode('-',$dyndnsSubdomainLabel);
		
		$sql='DELETE FROM v_dyndns WHERE vm_id='.$vid;
		$GLOBALS[self::$dataBaseLink]->query($sql);
		
		return $GLOBALS[self::$dataBaseLink]->affected_rows;
	}


	public static function insertDNSEntry($dyndnsSubdomainLabel) {
	
		list($trsh, $vmid)=explode('-',$dyndnsSubdomainLabel);

		$sql='INSERT INTO v_dyndns (vm_id,rfrsh,cnt) VALUES ('.$vmid.',NOW(),0)';
		$GLOBALS[self::$dataBaseLink]->query($sql);
		
		return $GLOBALS[self::$dataBaseLink]->affected_rows;


	}


	public static function updateIPData($dyndnsSubdomainLabel,$ipArr) {
	
		list($trsh, $vmid)=explode('-',$dyndnsSubdomainLabel);

		foreach (array('4','6') as $typ) {
			$uarr[]='bipv'.$typ.' = '.(array_key_exists($typ,$ipArr) ? '0x'.bin2hex(inet_pton($ipArr[$typ])) : 'null');
		}
		
		$sql='UPDATE v_dyndns SET '.implode(',',$uarr).', cnt = cnt + 1, rfrsh=NOW() WHERE vm_id = '.$vmid;
		$GLOBALS[self::$dataBaseLink]->query($sql);

		return $GLOBALS[self::$dataBaseLink]->affected_rows;

	}



	public static function getEntries() {

		$sql='SELECT d.cnt, DATE_FORMAT("%d.%m.%Y %H:%M",d.rfrsh) AS mts, d.bipv4, d.bipv6, CONCAT_WS("-","cst",d.vm_id) AS sym, CONCAT_WS(" // ", v.nname, v.email) AS abt, UNIX_TIMESTAMP(d.rfrsh) AS tzuts FROM vermieter AS v JOIN v_dyndns AS d ON v.vm_id=d.vm_id ORDER BY rfrsh DESC, cnt DESC';

		return $GLOBALS[self::$dataBaseLink]->query($sql);

	}



	public static function setupSQL() {

		$sql='CREATE TABLE IF NOT EXISTS v_dyndns (`vm_id` INT NOT NULL, `bipv4` BINARY(4) NULL, `bipv6` BINARY(16) NULL, `rfrsh` DATETIME NOT NULL, `cnt` INT NOT NULL, PRIMARY KEY (`vm_id`))';
		$GLOBALS[self::$dataBaseLink]->query($sql);

	}
}



?>
