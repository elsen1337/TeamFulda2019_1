<?php


class MultipartFormData {

	private $bnd=null;
	private $prm=array();
	
	
	function __construct(&$body=null,$cntype=null) {
	
		if ($cntype==null) {$cntype=$_SERVER['CONTENT_TYPE'];}
		if ($body==null) {$body=file_get_contents('php://input');}
		
		
		if (stripos($cntype,'content-type')!==false || stripos($cntype,'multipart/form-data')!==false) {
			$this->bnd=self::getBoundary($cntype,'boundary=');

		} else {
			$this->bnd=$cntype;
		}
		

		if (strlen($this->bnd)==0) {
			$bep=min( strpos($body,"\r"), strpos($body,"\n") );
			$this->bnd=substr($body,0,$bep);
		}

		self::parseMultipartFormData($body,$this->bnd,$this->prm);

	}
	
	
	function getFormData() {
	
		return $this->prm;
	
	}
	

	public static function getBoundary($about,$stoken) {

		$sp=stripos($about,$stoken);
		if ($sp===false) {return null;}
		$ep=stripos($about,";",$sp);
		if ($ep===false) {$ep=strlen($about);}
		
		return trim(substr($about,$sp+strlen($stoken),$ep));
		
	}

	public static function getFieldNameSpace($field) {

		$osb=substr_count($field,'[');
		$oeb=substr_count($field,']');
		
		if ($osb<>$oeb) {
			return array($field);
				
		} else {
		
			if ($osb <> 0) {
			
				preg_match_all('/\[([^\[\]]*)\]/',$field,$fmr);
				
				$nsdims=array_reverse($fmr[1]);
				array_push($nsdims, substr($field,0,strpos($field,'[')) );
				
				# $nsdims=array_merge( array( substr($field,0,strpos($field,'[')) ), $fmr[1]);

				return $nsdims;
			
			} else {
			
				return array($field);
			
			}

		}

	}


	public static function insert2Array(&$arr, $val, $key="") {

		if ($key == null || strlen($key) == 0) {
		
			array_push($arr,$val);
		
		} else {
		
			$arr[$key]=$val;
			
		}

	}

	public static function insert2ArrayRecur (&$arr, $val, $keys) {

		
		$ckey=array_pop($keys);
		
		if (count($keys) > 0) {
		
			if (array_key_exists($ckey,$arr)===false) {
			
				$arr[$ckey]=array();
				
			} else {
			
				if (is_array($arr[$ckey])==false) {
				
					$arr[$ckey]=array($arr[$ckey]);
				
				}
				
			}
			
			self::insert2ArrayRecur($arr[$ckey],$val,$keys);
		
		} else {

			self::insert2Array($arr,$val,$ckey);

		}


	}

	public static function parseMultipartFormData($body,$del,&$prm) {

		$qdel=preg_quote($del);
		// Reihenfolge am Ende und -- Zusätzlich (1) - Unabhängig vom Boundary mit -  
		$cdel='/[\r|\n]{0,2}[\-]{0,2}'.$qdel.'(?:\-\-[\s]{0,4}|[\r|\n]{0,4})/'; #/U
		$parts=preg_split($cdel,$body,null,PREG_SPLIT_NO_EMPTY);
		
		#var_dump($del);
		#print_r($parts);
				
		foreach($parts as &$part) {
		
			#var_dump($part);
			
			#if (strlen(trim($part))==0) {continue;}
			
			list($key,$val)=preg_split("/\s{2,4}/",$part,2,PREG_SPLIT_NO_EMPTY);
			
			#echo "$key,$val\n";
			
			$fieldname=trim( self::getBoundary($key,'name='), '"');
			$fieldarr=self::getFieldNameSpace($fieldname);
			
			self::insert2ArrayRecur($prm,$val,$fieldarr);


		}

	}
	
}


?>
