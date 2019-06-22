<?php



class Tenant {


    private static $dbvar='msdb';
    

    public static $formFields=array('anrede'=>'selection','vname'=>'text','nname'=>'text','email'=>'mail','tel_nr'=>'text','mob_nr'=>'text');
    public static $formFieldPasswort='pwort';
    
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
    
		$sql='SELECT m_id, anrede, vname, nname, email, profil FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;
   
    }

    public static function update($prp,$pkey) {


        if (array_key_exists(self::$formFieldPasswort,$prp)) {
			// $prp[self::$formFieldPasswort]=self::cryptPasswort($prp[self::$formFieldPasswort]);
			$ufarr[]=self::$formFieldPasswort.'=0x'.self::cryptPasswort($prp[self::$formFieldPasswort]);
		}

        $prp=array_intersect_key($prp,self::$formFields);
        
        $ufarr=array();
        foreach ($prp as $key => $val) {
			if ($key == 'pwort') {continue;}
            $ufarr[]=$key.'="'.$GLOBALS[self::$dbvar]->escape_string($val).'"';
        }
        
        $sql='UPDATE '.self::$entSQLTable.' SET '.implode(',',$ufarr).' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }
    
        
    public static function delete($pkey) {
    
        $sql='DELETE FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }

}


?>
 
