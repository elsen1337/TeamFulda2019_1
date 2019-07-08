<?php



class Chat {


	private static $dbvar = 'msdb';

	/*
	WICHTIG: Senderichtung der Nachricht duch Vorzeichen, dabei ist entweder VM_ID ode M_ID negativ !
	*/

	public static function getChatPartner4Lessor ($vid) {
	
		$sql='SELECT c.m_id, nname FROM m_chat AS c LEFT JOIN mieter AS m ON m.m_id=ABS(c.m_id) WHERE ABS(c.vm_id)='.$vid.' GROUP BY c.vm_id';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
        $attrarr=[];
        while (list($key,$val)=$mrs->fetch_array()) {
        
            $attrarr[]=array($key=>$val);
                            
        }
        
        return $attrarr;
	}
	
	
	public static function getChatPartner4Tenant ($mid) {
	
		$sql='SELECT c.vm_id, nname FROM m_chat AS c LEFT JOIN vermieter AS v ON v.vm_id=ABS(c.vm_id) WHERE ABS(c.m_id)='.$mid.' GROUP BY c.m_id';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		        
        $attrarr=[];
        while (list($key,$val)=$mrs->fetch_array()) {
        
            $attrarr[]=array($key=>$val);
                            
        }
        
        return $attrarr;
	
	}
	
	
	
	public static function getLastMessages4LessorTenant($vid,$mid) {
		
		$sql='SELECT c.mid, c.msg, c.date, IF(c.vm_id < 0, v.nname, m.nname) AS sendername,  IF(c.m_id > 0, m.nname, v.nname) AS recievername FROM m_chat AS c LEFT JOIN vermieter AS v ON v.vm_id=ABS(c.vm_id) LEFT JOIN mieter AS m ON m.m_id=ABS(c.m_id) WHERE ABS(c.m_id)='.$mid.' AND ABS(c.vm_id)='.$vid.' GROUP BY c.mid ORDER BY date ASC';
		$mrs=$GLOBALS[self::$dbvar]->query($sql); echo $GLOBALS[self::$dbvar]->error;
		        
        $attrarr=[];
        while ($msg=$mrs->fetch_assoc()) {
        
            $attrarr[]=$msg;
                            
        }
        
        return $attrarr;
	
	}
	
	
	private static function insertMessage($vid,$mid,$msg,$msgid) {
	
	
		$emsg=$GLOBALS[self::$dbvar]->escape_string($msg);
	
		if ($msgid > 0) {
		
			$sql='UPDATE m_chat SET m_id='.$mid.', vm_id='.$vid.', msg="'.$emsg.'" WHERE mid='.$msgid;
			$mrs=$GLOBALS[self::$dbvar]->query($sql);
			
			return $GLOBALS[self::$dbvar]->affected_rows;

		} else {
	
			$sql='INSERT INTO m_chat (vm_id, m_id, date, msg) VALUES ('.$vid.', '.$mid.', NOW(), "'.$emsg.'")';
			$mrs=$GLOBALS[self::$dbvar]->query($sql);
			
			return $GLOBALS[self::$dbvar]->insert_id;
			
		}
		
	}
	
	
	public static function insertMessageFromLessor2Tenant($vid,$mid,$msg,$updtmid=false) {
	
		return self::insertMessage($vid*-1,$mid,$msg,$updtmid);

	}
	
	public static function insertMessageFromTenant2Lessor($mid,$vid,$msg,$updtmid=false) {
	
		return self::insertMessage($vid,$mid*-1,$msg,$updtmid);

	}


}


?>


