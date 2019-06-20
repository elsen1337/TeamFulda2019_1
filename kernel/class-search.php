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
                    $sqlWhere[]='( m.name LIKE "%'.$fullTextSearch.'%" OR MATCH(a.val) AGAINST("'.$fullTextSearch.'") OR w.name LIKE "%'.$fullTextSearch.'%" OR MATCH(w.beschr) AGAINST ("'.$fullTextSearch.'") )';
                }
            }

            
        }
        
        
        if ($fullTextSearch != null) {
            // sqlOrder Unset on Fulltext... Order By Match Score AUTOMATICALLY from MATCH AGAINST Clause / FullTextSearch 
            $sqlOrder=[];		
        }
    
        // SUBSTRING_INDEX(GROUP_CONCAT(ColName ORDER BY ColName DESC), ',', 5)
        $sql='SELECT w.*, v.anrede, v.nname, GROUP_CONCAT(i.alt ORDER BY i.rdr SEPARATOR " // ") AS imgalt, SUBSTRING_INDEX(GROUP_CONCAT(i.bild ORDER BY i.rdr), ",", 1) AS imgpath, AVG(f.score) AS score, COUNT(DISTINCT f.m_id) AS cnt FROM wohnung AS w JOIN vermieter AS v ON v.vm_id=w.vm_id LEFT JOIN w_image AS i ON w.wohn_id=i.wohn_id LEFT JOIN m_favorit AS f ON f.wohn_id=w.wohn_id LEFT JOIN w_attrvals AS a ON a.wohn_id=w.wohn_id LEFT JOIN w_attrmeta AS m ON m.aid=a.aid WHERE w.visible > 0 '.(count($sqlWhere) > 0 ? 'AND '.implode(' AND ',$sqlWhere) : '').' GROUP BY w.wohn_id ORDER BY '.(count($sqlOrder) > 0 ? implode(',',$sqlOrder) : 'cnt DESC').(count($sqlLimit)==2 ? ' LIMIT '.$sqlLimit[0].','.$sqlLimit[1] : '');
        
        $mrs=$GLOBALS[self::$dbvar]->query($sql); echo $msdb->error;
        return $mrs;
        
    }

    
    public static function performSearch ($sqlWhere=array(),$sqlOrder=array(),$sqlLimit=array()) {

        $mrs=self::formQuery($sqlWhere,$sqlOrder,$sqlLimit);
        $jsn=array();
        
        while ($row=$mrs->fetch_object()) {
			
			 $jsn[]=$row;
			
		}

		echo json_encode($jsn);
    
    
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
                    $sessionRef[$formKey]=$searchRef[$formKey];
                }
                if (array_key_exists($formKey,$sessionRef)) {
                    
                    if ($sqlKey != 'val') {
                        self::addSQLWhereOrder($sqlWhere, $sqlOrder, self::$rangeOperatorMapping, $sqlKey, $sessionRef[$formKey]);
                    } else {
                    
                        $fullTextSearch=&$sessionRef[$formKey];
                        $sqlWhere[]='( m.name LIKE "%'.$fullTextSearch.'%" OR MATCH(a.val) AGAINST("'.$fullTextSearch.'") OR w.name LIKE "%'.$fullTextSearch.'%" OR MATCH(w.beschr) AGAINST ("'.$fullTextSearch.'") )';
                    }
                }

                
            }
            
            
            if ($fullTextSearch != null) {
                // sqlOrder Unset on Fulltext... Order By Match Score AUTOMATICALLY from MATCH AGAINST Clause / FullTextSearch 
                $sqlOrder=[];		
            }
            
        }

        
        // self::performSearch($sqlWhere,$sqlOrder);
    
    
    
    }
   
   
   
   
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
