<?php


class Estate {


    private static $dbvar='msdb';
    
    
    public static $formFields=array('wohn_id'=>'selection','rdr'=>'number','alt'=>'text');


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

}

?>
