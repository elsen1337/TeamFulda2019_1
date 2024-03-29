<?php


class SearchForm {


    private static $dbvar='msdb';


    public static $searchKeyGlobal='appsearch';
    public static $searchKeyDistMtr='distmeter';
    public static $searchKeyDistLPT='distopnv';
    public static $searchKeyPrice='price';
    public static $searchKeyText='fulltext';

    
    public static $rangeOperatorMapping=array('Min'=>'>=','Max'=>'<=' );

    
    // public static $searchParameters=array('val'=>self::$searchKeyText,'entf_meter'=>self::$searchKeyDistMtr,'entf_min'=>self::$searchKeyDistLPT,'preis'=>self::$searchKeyPrice);
    public static $searchParameters=array('val'=>'fulltext','entf_meter'=>'distmeter','entf_min'=>'distopnv','preis'=>'price');
    public static $searchLabels=array('val'=>'FullText-Search','entf_meter'=>'Distance to Campus (Meter)','entf_min'=>'Distance to Campus (Minutes by <abbr title="Low-Range Personal Traffic">LPT</abbr>)','preis'=>'Price / Week');

    
    public static function generateForm () {
    
		echo '<div id="search">';
		echo '<div id="filtermenu"><form method="post">';
		
		foreach (self::$searchParameters as $sqlKey => $formKey) {
			
			echo '<fieldset><legend>'.self::$searchLabels[$sqlKey].'</legend>'."\n";
			
			
			if ($sqlKey != 'val') {
				
				foreach (self::$rangeOperatorMapping as $fieldKey => $operSym) {
					
					echo '<input type="number" name="'.self::$searchKeyGlobal.'['.$formKey.']['.$fieldKey.']" value="'.$_SESSION[self::$searchKeyGlobal][$formKey][$fieldKey].'" placeholder="'.$fieldKey.'">';
					
				}
				
			
			} else {
				
				echo '<input type="search" name="'.self::$searchKeyGlobal.'['.$formKey.']" value="'.$_SESSION[self::$searchKeyGlobal][$formKey].'" placeholder="Type any search value... We\'re hoping to match your request...">';
					
			}

			echo '</fieldset>'."\n";
			
		}
		
		echo '<fieldset><legend>Filter...</legend>
<input type="submit" value="Show Me ResultSet">
</fieldset></form></div>';
    
    
    
    }
    
    
    private static function formQuery ($sqlWhere=array(),$sqlOrder=array(),$sqlLimit=array()) {
    
    
        $fullTextSearch=null;
        
        $isSessionSearch=array_key_exists(self::$searchKeyGlobal,$_SESSION);
        if ($isSessionSearch===false) {$_SESSION[self::$searchKeyGlobal]=array();}        

        $sessionRef=&$_SESSION[self::$searchKeyGlobal];

        
        foreach (self::$searchParameters as $sqlKey => $formKey) {

            if (array_key_exists($formKey,$sessionRef)) {
                
                if ($sqlKey != 'val') {
                    self::addSQLWhereOrder($sqlWhere, $sqlOrder, self::$rangeOperatorMapping, $sqlKey, $sessionRef[$formKey]);
                } else {
                
                    $fullTextSearch=&$sessionRef[$formKey];
                    if (strlen($fullTextSearch) > 0) {
                        $sqlWhere[]='( m.name LIKE "%'.$fullTextSearch.'%" OR MATCH(a.val) AGAINST("'.$fullTextSearch.'") OR w.name LIKE "%'.$fullTextSearch.'%" OR MATCH(w.beschr) AGAINST ("'.$fullTextSearch.'") )';
                    }
                }
            }

            
        }
        
        
        if ($fullTextSearch != null) {
            // sqlOrder Unset on Fulltext... Order By Match Score AUTOMATICALLY from MATCH AGAINST Clause / FullTextSearch 
            $sqlOrder=[];		
        }
    

        $sql='SELECT SQL_CALC_FOUND_ROWS w.*, v.anrede, v.nname, GROUP_CONCAT(i.alt ORDER BY i.rdr SEPARATOR " // ") AS imgalt, SUBSTRING_INDEX(GROUP_CONCAT(i.bild ORDER BY i.rdr), ",", 1) AS imgpath, AVG(f.score) AS favscore, COUNT(DISTINCT f.m_id) AS favcnt FROM wohnung AS w JOIN vermieter AS v ON v.vm_id=w.vm_id LEFT JOIN w_image AS i ON w.wohn_id=i.wohn_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id LEFT JOIN w_attrvals AS a ON a.wohn_id=w.wohn_id LEFT JOIN w_attrmeta AS m ON m.aid=a.aid WHERE w.visible > 0 '.(count($sqlWhere) > 0 ? 'AND '.implode(' AND ',$sqlWhere) : '').' GROUP BY w.wohn_id ORDER BY '.(count($sqlOrder) > 0 ? implode(',',$sqlOrder) : 'favcnt DESC').(count($sqlLimit)==2 ? ' LIMIT '.$sqlLimit[0].','.$sqlLimit[1] : '');
        
        $mrs=$GLOBALS[self::$dbvar]->query($sql);
        echo $GLOBALS[self::$dbvar]->error;
        return $mrs;
        
    }

    
    public static function performSearch ($sqlWhere=array(),$sqlOrder=array(),$sqlLimit=array()) {

        $mrs=self::formQuery($sqlWhere,$sqlOrder,$sqlLimit);
        list($amnt)=$GLOBALS[self::$dbvar]->query('SELECT FOUND_ROWS()')->fetch_array();
        
        $jsn=array();
        
        while ($row=$mrs->fetch_object()) {
        
            $row->paththumb=AppartImage::formThumbFilePath($row->imgpath);
            $row->pathnormal=AppartImage::formNormalFilePath($row->imgpath);
			
			$jsn[]=$row;
			
		}

		echo json_encode($jsn);
		return array($amnt,$mrs->num_rows);
    
    
    }
    
    
    public static function updateSearchSession () {
    
    
        $isSessionSearch=array_key_exists(self::$searchKeyGlobal,$_SESSION);
        $isRequestSearch=array_key_exists(self::$searchKeyGlobal,$_REQUEST);

        if ($isRequestSearch || $isSessionSearch) {


            $sqlWhere=[];
            $sqlOrder=[];


            $searchRef=&$_REQUEST[self::$searchKeyGlobal];
            $sessionRef=&$_SESSION[self::$searchKeyGlobal];
            
            if ($isSessionSearch===false) {$_SESSION[self::$searchKeyGlobal]=array();}

            
            
            $fullTextSearch=null;
            foreach (self::$searchParameters as $sqlKey => $formKey) {
                if ($isRequestSearch && array_key_exists($formKey,$searchRef)) {
                
					$valRef=&$searchRef[$formKey];
					
					if (is_array($valRef)==false && ($valRef=='null' || $valRef=='undefined' || $valRef=='')) {
						unset($sessionRef[$formKey]);
					} else {
						$sessionRef[$formKey]=$searchRef[$formKey];
						if (is_array($valRef)) {
							$sessionRef[$formKey] = array_filter($sessionRef[$formKey],function ($valRef) {return !($valRef=='null' || $valRef=='undefined' || $valRef=='');});
						}
                    }
                }
                
                /*
                if (array_key_exists($formKey,$sessionRef)) {
                    
                    if ($sqlKey != 'val') {
                        self::addSQLWhereOrder($sqlWhere, $sqlOrder, self::$rangeOperatorMapping, $sqlKey, $sessionRef[$formKey]);
                    } else {
                    
                        $fullTextSearch=&$sessionRef[$formKey];
                        if (strlen($fullTextSearch) > 0) {
                            $sqlWhere[]='( m.name LIKE "%'.$fullTextSearch.'%" OR MATCH(a.val) AGAINST("'.$fullTextSearch.'") OR w.name LIKE "%'.$fullTextSearch.'%" OR MATCH(w.beschr) AGAINST ("'.$fullTextSearch.'") )';
                        }
                    }
                }
                */

                
            }
            
            
            /*
            if ($fullTextSearch != null) {
                // sqlOrder Unset on Fulltext... Order By Match Score AUTOMATICALLY from MATCH AGAINST Clause / FullTextSearch 
                $sqlOrder=[];		
            }
            */
            
        }

        
        // self::performSearch($sqlWhere,$sqlOrder);
    
    
    
    }
    
    
    
	public static function resetSession() {
	
		$_SESSION[self::$searchKeyGlobal]=array();
	
	}



	public static function deleteStoredSession($sid) {
	
		$sql='DELETE FROM m_search WHERE sid='.$sid;
		$GLOBALS[self::$dbvar]->query($sql);
		
		return $GLOBALS[self::$dbvar]->affected_rows > 0;
	
	}    
    
    
    public static function storeSession($mid,$sid=-1) {
    
    
		$jsn=json_encode($_SESSION[self::$searchKeyGlobal]);
		$sss=$GLOBALS[self::$dbvar]->escape_string($jsn);

		# var_dump($sss);
		
		if ($sid > 0) {
		
			$sql='UPDATE m_search SET sss="'.$sss.'", shot=NOW() WHERE mid = '.$mid.' AND sid = '.$sid;
			$GLOBALS[self::$dbvar]->query($sql);
			
			return array('actSuccess'=> ( $GLOBALS[self::$dbvar]->affected_rows >= 0 ) );
		
		} else {
		
			$sql='INSERT INTO m_search (mid,shot,sss) VALUES ('.$mid.',NOW(),"'.$sss.'")';
			$GLOBALS[self::$dbvar]->query($sql);
			
			$newid=$GLOBALS[self::$dbvar]->insert_id;
			return array('actSuccess'=> ($newid > 0), 'newSearchSessionID'=>$newid);
			
		}
    

    }
    
    public static function loadStoredSession($mid,$sid=-1) {
    
		$sql='SELECT sid,sss FROM m_search WHERE mid = '.$mid.($sid > 0 ? ' AND sid = '.$sid : '') .' ORDER BY shot DESC LIMIT 1';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);
		
		if ($mrs->num_rows > 0) {
		
			list($sid,$sss)=$mrs->fetch_row();
			$decSearchSession=json_decode($sss,true);
			
			if (json_last_error() == JSON_ERROR_NONE) {
			
				$_SESSION[self::$searchKeyGlobal]=$decSearchSession;
				
				return $sid;
			
			}
    		
		}
		
		return -1;
		

    }
   
   
	public static function getSearchSessionsList($mid) {
   
		$translateList=array_flip(self::$searchParameters);
   
		$sql='SELECT sid, sss, DATE_FORMAT(shot, "%d.%m.%Y @ %H:%i") AS shot FROM m_search WHERE mid = '.$mid.' ORDER BY shot DESC';
		$mrs=$GLOBALS[self::$dbvar]->query($sql);

		$attrarr=[];
		while ($obj=$mrs->fetch_object()) {

			$strRepr=self::transformSearchHumanReadable($translateList,json_decode($obj->sss,true));
			$attrarr[]=array('sid'=>$obj->sid, 'label'=>$obj->shot, 'detail'=>strip_tags($strRepr));
			
		}
		
		return $attrarr;

	}


	// Private
	public static function transformSearchHumanReadable($trl,$arr) {

		if (is_array($arr)===false) {return 'No Search Parameters Set';}

		$rar=array();
		foreach ($arr as $key => $val) {
			$rar[]=self::$searchLabels[$trl[$key]].': '.(is_array($val) ? implode(',',$val) : $val);
		}
		
		return implode('; ',$rar);

	}
   
   
   
	// Private
	public static function addSQLWhereOrder(&$sqlWhere, &$sqlOrder, &$rangeOperatorMapping, $sqlField, &$inputArr) {
		
		foreach ($rangeOperatorMapping as $field => $operator) {
			if (array_key_exists($field,$inputArr) && strlen($inputArr[$field]) > 0) {
				$sqlWhere[]=$sqlField.' '.$operator.' '.$inputArr[$field];
				$sqlOrder[$sqlField]=$sqlField.' ASC';
			}
		}
		
	}

    
    public static function printInitialSearchForm($searchKeyGlobal,$searchKeyText) {

        echo '<form method="post" id="startform"><input type="search" value="'.$_SESSION[self::$searchKeyGlobal][self::$searchKeyText].'" name="'.self::$searchKeyGlobal.'['.self::$searchKeyText.']" placeholder="Type any search value... We\'re hoping to match your request..."><input type="submit" value="Go..."></form>';
        
    }
    


}



?>
