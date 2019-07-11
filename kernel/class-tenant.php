<?php



class Tenant {


    private static $dbvar='msdb';
    

    public static $formFieldPasswort='pwort';
    public static $formFields=array('anrede'=>'selection','vname'=>'text','nname'=>'text', 'email'=>'mail', 'tel_nr'=>'text','mob_nr'=>'text', 'birthdate'=>'date');
    
    public static $entPrimKey='m_id';
    public static $entSQLTable='mieter';

    public static $sKey='TAUTH';



    public static function cryptPasswort($token) {
    
        return md5($token);
    
    }
    
    
    public static function login($name, $passwort) {
    
        $sql='SELECT m_id, anrede, nname, vname, email FROM '.self::$entSQLTable.' WHERE email="'.$name.'" AND pwort=0x'.self::cryptPasswort($passwort).'';
        
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;
    
    
    }
    
    
    public static function register($prp) {
    
		$pwort=self::cryptPasswort($prp[self::$formFieldPasswort]);
        $prp=array_intersect_key($prp,self::$formFields); // Escape
        
        // if (array_key_exists(self::$formFieldPasswort,$prp)) {$prp[self::$formFieldPasswort]=self::cryptPasswort($prp[self::$formFieldPasswort]);}

		$sql='INSERT INTO '.self::$entSQLTable.' (pwort,'.implode(',',array_keys($prp)).') VALUES (0x'.$pwort.' ,"'.implode('","',($prp)).'")';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->insert_id;
    
    
    }
    
    public static function about($pkey) {
    
		$sql='SELECT m_id, anrede, vname, nname, email, profil, birthdate FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;
   
    }

    public static function update($prp,$pkey) {

        $ufarr=array();

        if (array_key_exists(self::$formFieldPasswort,$prp)) {
			// $prp[self::$formFieldPasswort]=self::cryptPasswort($prp[self::$formFieldPasswort]);
			$ufarr[]=self::$formFieldPasswort.'=0x'.self::cryptPasswort($prp[self::$formFieldPasswort]);
		}

        $prp=array_intersect_key($prp,self::$formFields);
        
        foreach ($prp as $key => $val) {
			if ($key == 'pwort') {continue;}
            $ufarr[]=$key.'="'.$GLOBALS[self::$dbvar]->escape_string($val).'"';
        }
        
        $sql='UPDATE '.self::$entSQLTable.' SET '.implode(',',$ufarr).' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }
    
        
    public static function delete($pkey) {
    
		// Zuerst: Favoriten, Chatnachrichten [OK]
		$sql='DELETE f,c,m FROM '.self::$entSQLTable.' AS m LEFT JOIN m_chat AS c ON c.m_id=m.m_id LEFT JOIN m_favorit AS f ON f.m_id=m.m_id LEFT JOIN m_chat AS c ON ABS(c.m_id)=m.m_id WHERE m.'.self::$entPrimKey.'='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows > 0;

    
    }
    
            
    public static function favouriteAddUpdate($mid,$wid,$scr=null) {
    
		if (strlen($scr)==0) {$scr='null';}
        $sql='INSERT INTO m_favorit (m_id,wohn_id,score,cdate) VALUES ('.$mid.', '.$wid.','.$scr.', NOW() ) ON DUPLICATE KEY UPDATE cdate=VALUES(cdate), score=VALUES(score)';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows > 0; // !!! Spezialfall 0,1(Insert),2(Update)
    
    }    
    
    
    public static function favouriteRemove($mid,$wid) {
    
        $sql='DELETE FROM m_favorit WHERE wohn_id='.$wid.' AND m_id='.$mid;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    }    
    
    
	public static function favouriteList($mid) {

		$sql='SELECT f.m_id, f.wohn_id, w.name, f.score, f.cdate FROM m_favorit AS f JOIN wohnung AS w ON w.wohn_id=f.wohn_id WHERE f.m_id='.$mid.' ORDER BY cdate DESC, score DESC';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		$attrarr=[];
		while ($obj=$mrs->fetch_object()) {

			$attrarr[]=$obj;
							
		}
		
		return $attrarr;

    }
    
    public static function getMyMeetingSlots($pkey) {
    
		$sql='SELECT h.tid, m.slot, w.name FROM m_meet AS h JOIN w_meet AS m ON h.tid=m.tid LEFT JOIN wohnung AS w ON w.wohn_id=m.wohn_id WHERE h.m_id = '.$pkey.' ORDER BY slot';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
        $attrarr=[];
        while ($row=$mrs->fetch_assoc()) {
        
            $attrarr[]=$row;
                            
        }
        
        return $attrarr;
    
    }   
    
    public static function addMeetingSlot($pkey,$tid) {
    
		$sql='INSERT IGNORE INTO m_meet (tid, m_id) VALUES ('.$tid.','.$pkey.')';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
		return $GLOBALS[self::$dbvar]->insert_id;

    }
    
    public static function removeMeetingSlot($pkey,$tid) {
    
		$sql='DELETE h FROM m_meet AS h WHERE h.tid = '.$tid.' AND h.m_id = '.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
		return $GLOBALS[self::$dbvar]->affected_rows;

    }


	public static function getPassword($pkey) {
    
		$sql='SELECT pwort FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.' = '.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);

		return $GLOBALS[self::$dbvar]->affected_rows;

    }


}


?>
 
