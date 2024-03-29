<?php


class Estate {


    private static $dbvar='msdb';
    
    
    public static $formFieldsAttr = array('aid'=>'selection','wohn_id'=>'number','val'=>'text');
    
    public static $formFieldsDefault = array('visible'=>'selection', 'name'=>'text','beschr'=>'area', 'vm_id'=>'selection', 'str'=>'text','plz'=>'number','ort'=>'text', 'preis'=>'number=step>0.01','qm_groesse'=>'number', 'entf_meter'=>'number','entf_min'=>'number',
    'kaution'=>'number','garage'=>'text','frei_ab'=>'date','zimmer'=>'number','tiere'=>'text');


    public static $entPrimKey='wohn_id';
    public static $entSQLTable='wohnung';

    

    public static function getDefaultProperties ($wid) {
    
        $sql='SELECT w.*, v.nname, s.vid_url, COUNT(f.m_id) AS cnt, AVG(f.score) AS score FROM wohnung AS w JOIN vermieter AS v ON w.vm_id=v.vm_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id LEFT JOIN w_stream AS s ON s.wohn_id=w.wohn_id WHERE w.wohn_id='.$wid.' GROUP BY w.wohn_id';

        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        return ($mrs->num_rows == 1) ? $mrs->fetch_object() : null;

    }
	
	public static function getVideoStream($wid) 
	{
		$sql='SELECT * from w_stream WHERE wohn_id = '.$wid;
	}


	// Object JSON Format
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
		
		//CHDIR Strategie ?!
		$tmpimg=AppartImage::getImagesMetaData($pkey);
		chdir(__DIR__.'/../'.AppartImage::$uploadBaseDir);

		
		foreach ($tmpimg as $imgobj) {
		
			AppartImage::removeImage($imgobj->bild_id);
			
		}
		
		
		// Properties, Termine, [Stream, Video]
		$sql='DELETE v,w FROM '.self::$entSQLTable.' AS w LEFT JOIN w_attrvals AS v ON v.wohn_id=w.wohn_id LEFT JOIN w_meet AS wm ON wm.wohn_id=w.wohn_id LEFT JOIN m_meet AS mm ON mm.tid=wm.tid LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id WHERE w.'.self::$entPrimKey.'='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows > 0;

    
	}

    
	
	public static function updateAttrib($prp) {
    
		$prp=array_intersect_key($prp,self::$formFieldsAttr);

		$ufarr=array();
		foreach ($prp as $key => $val) {
			$ufarr[$key]=$GLOBALS[self::$dbvar]->escape_string($val);
		}
		
		$sql='INSERT INTO w_attrvals ('.implode(',',array_keys($ufarr)).') VALUES ("'.implode('","',($ufarr)).'") ON DUPLICATE KEY UPDATE val=VALUES(val)';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows >= 0;

    
    }
    
        
    public static function deleteAttrib($pkey,$akeys) {
    
		// Subkeys als GET ? implode(',',$akeys)
		#  echo $sql='DELETE FROM w_attrvals WHERE '.self::$entPrimKey.'='.$pkey.' AND aid IN ('..')';
		$sql='DELETE FROM w_attrvals WHERE '.self::$entPrimKey.'='.$pkey.' AND aid IN ('.$akeys.')';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows > 0;

    
    }

    
	// Object JSON Format
    public static function getAttributeList() {
    
		$sql='SELECT aid, name FROM w_attrmeta WHERE vsb > 0 ORDER BY rdr';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
        $attrarr=[];
        while (list($key,$val)=$mrs->fetch_array()) {
        
            $attrarr[]=array($key=>$val);
                            
        }
        
        return $attrarr;
    
    }    

    
    
    public static function getProposedMeetingSlots($pkey) {
    
		$sql='SELECT w.tid, GROUP_CONCAT(m.m_id) AS optionalMIDArr, DATE_FORMAT(w.slot, "%d.%m.%Y @ %H:%i") AS slot FROM w_meet AS w LEFT JOIN m_meet AS m ON m.tid=w.tid WHERE w.wohn_id = '.$pkey.' GROUP BY w.tid ORDER BY slot';
		
		//$sql='SELECT t.m_id, w.tid, t.vname, t.nname, t.email, DATE_FORMAT(w.slot, "%d.%m.%Y @ %H:%i") AS slot FROM w_meet AS w LEFT JOIN m_meet AS m ON m.tid=w.tid JOIN mieter AS t ON t.m_id=m.m_id WHERE w.wohn_id = '.$pkey.' GROUP BY w.tid ORDER BY slot';

		//$sql='SELECT tid, DATE_FORMAT(slot, "%d.%m.%Y @ %H:%i") AS slot FROM w_meet WHERE wohn_id = ANY (SELECT wohn_id FROM wohnung WHERE vm_id = '.$pkey.') ORDER by slot';
		        
		$attrarr=[];
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		while ($mobj=$mrs->fetch_object()) {
        
			$sql='SELECT m.m_id, m.vname, m.nname, m.email FROM m_meet AS a LEFT JOIN mieter AS m ON a.m_id=m.m_id WHERE a.tid='.$mobj->tid;
			$srs=$GLOBALS[self::$dbvar]->query($sql);
			
			$mobj->bookedBy=array();
			while ($sobj=$srs->fetch_assoc()) {
				$mobj->bookedBy[]=$sobj;
			}
			
			$attrarr[]=$mobj;

		}
		
		return $attrarr;
	
	}   
    
	public static function addMeetingSlotProposal($pkey,$date) {
    
		$sql='INSERT INTO w_meet (wohn_id, slot) VALUES ('.$pkey.',"'.$date.'")';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
		return $GLOBALS[self::$dbvar]->insert_id;

    }
    
    public static function removePredefinedMeetingSlot($pkey) {
    
		$sql='DELETE h,m FROM w_meet AS m LEFT JOIN m_meet AS h ON h.tid=m.tid WHERE m.tid ='.$pkey;
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
		return $GLOBALS[self::$dbvar]->affected_rows;

    }
    
    
}


?>
