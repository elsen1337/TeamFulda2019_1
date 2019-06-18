<?php



class Lessor {


    private static $dbvar='msdb';
    

    public static $formFields=array('wohn_id'=>'selection','rdr'=>'number','alt'=>'text');
    public static $formFieldPasswort='pwort';



    public static function cryptPasswort($token) {
    
        return md5($token);
    
    }

    public static function login($name, $passwort) {
    
        $sql='SELECT * FROM vermieter WHERE email="'.$name.'" AND pwort="'.self::cryptPasswort($passwort).'"';
        
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;
    
    
    }
    
    
    public static function register($prp) {
    
        $prp=array_intersect_key($prp,self::$formFields+array(self::$formFieldPasswort=>'password')); // Escape
        if (array_key_exists(self::$formFieldPasswort,$prp)) {$prp[self::$formFieldPasswort]=self::cryptPasswort($prp[self::$formFieldPasswort]);}
        $sql='INSERT INTO vermieter ('.implode(',',array_keys($prp)).') VALUES ("'.implode('","',($prp)).'")';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->insert_id;
    
    
    }


}


?>
