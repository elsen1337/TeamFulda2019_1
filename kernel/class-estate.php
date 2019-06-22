<?php


class Estate {


    private static $dbvar='msdb';
    
    
    public static $formFields=array('wohn_id'=>'selection','rdr'=>'number','alt'=>'text');
    public static $formFieldsDefault=array('visible'=>'selection','name'=>'text','beschr'=>'area','vm_id'=>'selection','str'=>'text','plz'=>'number','ort'=>'text','preis'=>'number=step>0.01','qm_groesse'=>'number','entf_meter'=>'number','entf_min'=>'number');


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
        
            $attrarr[$key]=$val;
                            
        }
        
        return $attrarr;
        
    }

        
    
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
        $prp['visible']='1'; // Debug
                
        $sql='INSERT INTO '.self::$entSQLTable.' ('.implode(',',array_keys($prp)).') VALUES ("'.implode('","',($prp)).'")';
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->insert_id;

    }
    
    public static function update($prp,$pkey) {
    
        $prp=array_intersect_key($prp,self::$formFieldsDefault); // Escape

        $ufarr=array();
        foreach ($prp as $key => $val) {
            $ufarr[]=$key.'="'.$GLOBALS[self::$dbvar]->escape_string($val).'"';
        }
        
        $sql='UPDATE '.self::$entSQLTable.' SET '.implode(',',$ufarr).' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }
    
        
    public static function delete($pkey) {
    
		// DELETE Properties & Images
    
        $sql='DELETE FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$pkey;
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        
        return $GLOBALS[self::$dbvar]->affected_rows===1;
    
    
    }
}

?>
