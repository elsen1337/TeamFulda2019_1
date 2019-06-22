<?php


class Estate {


    private static $dbvar='msdb';
    
    
    public static $formFieldsAttr = array('aid'=>'selection','wid'=>'number','val'=>'text');
    
    public static $formFieldsDefault = array('visible'=>'selection', 'name'=>'text','beschr'=>'area', 'vm_id'=>'selection', 'str'=>'text','plz'=>'number','ort'=>'text', 'preis'=>'number=step>0.01','qm_groesse'=>'number', 'entf_meter'=>'number','entf_min'=>'number');


    public static $entPrimKey='wohn_id';
    public static $entSQLTable='wohnung';

    

    public static function getDefaultProperties ($wid) {
    
        $sql='SELECT w.*, v.nname, COUNT(f.m_id) AS cnt, AVG(f.score) AS score FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id WHERE w.wohn_id='.$wid.' GROUP BY w.wohn_id';

        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;

    }

    public static function getDynamicProperties ($wid) {
    
        $sql='SELECT m.name,a.val FROM w_attrvals AS a JOIN w_attrmeta AS m ON a.aid=m.aid WHERE a.wohn_id='.$wid.' AND m.vsb > 0 ORDER BY m.rdr';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        $attrarr=[];
        while (list($key,$val)=$mrs->fetch_array()) {
        
            $attrarr[]=array($key=>$val);
                            
        }
        
        return $attrarr;
        
    }

        
    // -> AppartImage
    public static function getImagesMetaData ($wid) {
    
       	$sql='SELECT alt,bild,bild_id FROM w_image WHERE wohn_id='.$wid.' ORDER BY rdr';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        $attrarr=[];
        while ($imgobj=$mrs->fetch_object()) {
        
            $imgobj->paththumb=AppartImage::formThumbFilePath($imgobj->bild);
            $imgobj->pathnormal=AppartImage::formNormalFilePath($imgobj->bild);

            $attrarr[]=$imgobj;
                            
        }
        
        return $attrarr;

    }

    
        
    public static function create ($prp) {
    

        $prp=array_intersect_key($prp,self::$formFieldsDefault); // Escape
                
        $sql='INSERT INTO '.self::$entSQLTable.' ('.implode(',',array_keys($prp)).') VALUES ("'.implode('","',($prp)).'")';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->insert_id;

    }
   
   
    public static function update($prp,$pkey) {
    
        $prp=array_intersect_key($prp,self::$formFieldsDefault);

        $ufarr=array();
        foreach ($prp as $key => $val) {
            $ufarr[]=$key.'="'.$GLOBALS[self::$dbvar]->escape_string($val).'"';
        }
        
        $sql='UPDATE '.self::$entSQLTable.' SET '.implode(',',$ufarr).' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }
    
        
    public static function delete($pkey) {
    
		// DELETE Images
		set_include_path(__DIR__);
		require_once('class-appartimg.php');
		
		//foreach () {}
		//CHDIR Strategie ?!
		//AppartImage::delete();
		
		// Properties, Termine, [Stream, Video]
		$sql='DELETE v,w FROM '.self::$entSQLTable.' AS w LEFT JOIN w_attrvals AS v ON v.wid=w.wohn_id LEFT JOIN w_meet AS m ON m.wohn_id=w.wohn_id  WHERE w.'.self::$entPrimKey.'='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows > 0;

    
    }
    
    
}


?>
