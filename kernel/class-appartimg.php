<?phpclass AppartImage {    public static $dbvar='msdb';    public static $formFields=array('wohn_id'=>'selection','rdr'=>'number','alt'=>'text');        public static $uploadBaseDir='images';    public static $dirThumb='thumb';    public static $dirOrg='normal';        const MAX_IMAGE_NUM=10;    const MAX_IMAGE_SIZE_KB=1024 * 4;    const MAX_IMAGE_WIDTH=150;    const MAX_IMAGE_HEIGHT=150;    public static $entPrimKey='bild_id';    public static $entSQLTable='w_image';        public static function addImage($prp) {            $prp=array_intersect_key($prp, self::$formFields);        $sql='INSERT INTO '.self::$entSQLTable.' ('.implode(',',array_keys($prp)).') VALUES ("'.implode('","',($prp)).'")';        $mrs=$GLOBALS[self::$dbvar]->query($sql);                return $GLOBALS[self::$dbvar]->insert_id;            }            public static function uploadImage($bildUpload,$postParam=array()) {             // 2Do: Status            foreach ($bildUpload['error'] as $bid => $val) {                        if ($val > 0) {continue;}            if ($bildUpload['size'][$bid] > self::MAX_IMAGE_SIZE_KB * 1024 || $bildUpload['size'][$bid] == 0) {continue;}            if (strpos($bildUpload['type'][$bid],'image') === false) {continue;}                        # stringNormalize::normalizeURL File            $oldFileName = $bildUpload['name'][$bid];            $pathInfoOld=pathinfo($oldFileName);                      // $r=trim(preg_replace('/(\d+)/i', '-$1-', $r),'-');            // $r=str_replace('.', '', $r); $r=preg_replace('/\-+/i', '-', $r);            $tmpUploadFileName=$bildUpload['tmp_name'][$bid];                                    $bsqlid=is_int($bid)===false ? self::addImage($postParam) : $bid;            $newFileName = str_pad($bsqlid, 5, "0", STR_PAD_LEFT).'-'.stringNormalize::normalizeURL($pathInfoOld['filename']).'.'.$pathInfoOld['extension'];                        $targetNormalFile = implode('/',array(self::$dirOrg, $newFileName));            $targetThumbFile=self::$dirThumb .'/'.substr($newFileName,0,(false==$p=strrpos($newFileName,'.')) ? strlen($newFileName) : $p).'.jpg';            move_uploaded_file($tmpUploadFileName,$targetNormalFile);            file_put_contents($targetThumbFile, image_get_thumb_file ($targetNormalFile, self::MAX_IMAGE_WIDTH, self::MAX_IMAGE_HEIGHT, 65));                    $GLOBALS[self::$dbvar]->query('UPDATE '.self::$entSQLTable.' SET bild = "'.$newFileName.'" WHERE '.self::$entPrimKey.'='.$bsqlid);                    }            }                public static function removeImage($bid) {            $row=$GLOBALS[self::$dbvar]->query('SELECT * FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$bid)->fetch_assoc();		$accRes=0;		$accRes+=(int)@unlink(self::$dirThumb.'/'.AppartImage::formThumbFileName($row['bild']));		$accRes+=(int)@unlink(self::$dirOrg.'/'.$row['bild']);		if ($accRes > 1 || strlen($row['bild'])==0) {            $GLOBALS[self::$dbvar]->query('DELETE FROM '.self::$entSQLTable.' WHERE '.self::$entPrimKey.'='.$bid);		}		return $accRes > 1;    }            public static function updateMetaData($pkey,$prp) { 		$prp=array_intersect_key($prp,self::$formFields);		$ufarr=array();		foreach ($prp as $key => $val) {			$ufarr[]=$key.'="'.$GLOBALS[self::$dbvar]->escape_string($val).'"';		}		$sql='UPDATE '.self::$entSQLTable.' SET '.implode(',',$ufarr).' WHERE '.self::$entPrimKey.'='.$pkey;		$mrs=$GLOBALS[self::$dbvar]->query($sql);		return $GLOBALS[self::$dbvar]->affected_rows===1;    }  	public static function getImagesMetaData ($wid) {			// List VS Single ?		$sql='SELECT alt,bild,bild_id FROM w_image WHERE wohn_id='.$wid.' ORDER BY rdr';		$mrs=$GLOBALS[self::$dbvar]->query($sql);				$attrarr=[];		while ($imgobj=$mrs->fetch_object()) {					$imgobj->paththumb=AppartImage::formThumbFilePath($imgobj->bild);			$imgobj->pathnormal=AppartImage::formNormalFilePath($imgobj->bild);			$attrarr[]=$imgobj;									}				return $attrarr;	}            	public static function formThumbFileName($img) {		$pathInfo=pathinfo($img);		return $pathInfo['filename'].'.jpg';	}		public static function formThumbFilePath($img) {		if (strlen($img) > 0) {			return implode('/',array(self::$uploadBaseDir,self::$dirThumb,self::formThumbFileName($img) ));		} else {			return null;		}	}		public static function formNormalFilePath($img) {		if (strlen($img) > 0) {			return implode('/',array(self::$uploadBaseDir,self::$dirOrg,$img ));		} else {			return null;		}	}		public static function formNormalizedFileName($img,$bid) {		$pathInfoOld=pathinfo($oldFileName);		$newFileName = str_pad($bid, 5, "0", STR_PAD_LEFT).'-'.stringNormalize::normalizeURL($pathInfoOld['filename']).'.'.$pathInfoOld['extension'];		return $newFileName;	}		public static function getMaxPictNum() {		return self::MAX_IMAGE_NUM;	}    }?>