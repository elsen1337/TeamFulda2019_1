<?php


function image_get_thumb_memory ($bild, $width=100, $height=75, $quality=65, $useoutbuffering=true) {


	ini_set('output_buffering','on');
	if (is_string($bild)) {


		// PHP 5.4 - > Patch 5.3
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
		list($imgwidth, $imgheight, $type) = @getimagesizefromstring($bild);

		} else {
		$type=get_image_type_by_magicbytes(array_pop(unpack("H*",substr($bild,0,2))));

		}


		if ($type==IMAGETYPE_BMP) {
		$mih=fopen('php://memory','wb'); fwrite($mih,$bild['bin']);
		$img_org = imagecreatefrombmp($mih); fclose($mih);

		} elseif ($type==IMAGETYPE_PNG || $type==IMAGETYPE_GIF || $type==IMAGETYPE_JPEG) {
		$img_org = imagecreatefromstring($bild['bin']);

		}

	} else {

		# +++ HE_FIX/MemorySaver +++ #
		$img_org=($bild);


		//ob_start();
		//imagegd2($bild);
		//$img_org = imagecreatefromstring(ob_get_contents());
		//ob_end_clean();

	}


	$imgwidth=imagesx($img_org);
	$imgheight=imagesy($img_org);


	if (!empty($width) && !empty($height)) {
	
	$percent=min(array($height/$imgheight,$width/$imgwidth));
	$newwidth = $imgwidth * $percent;
	$newheight = $imgheight * $percent;

	} elseif (!empty($width)) {
	$newwidth = $width;
	$newheight = $width/$imgwidth * $imgheight;

	} elseif (!empty($height)) {
	$newwidth = $height/$imgheight * $imgwidth;
	$newheight = $height;
	
	} else {
	return false;
	
	} # Scaling


$img_thu=imagecreatetruecolor($newwidth, $newheight);

imagecopyresampled($img_thu, $img_org, 0, 0, 0, 0, (int)$newwidth, (int)$newheight, $imgwidth, $imgheight);


if ($useoutbuffering && ob_start()) {

imagejpeg($img_thu, null, $quality);
$thumb = ob_get_contents();
ob_end_clean();


} else {

$filename=tempnam(get_he_tmp_dir(), 'img-handler-abi');
imagejpeg($img_thu, $filename, $quality);
$thumb = file_get_contents($filename);
unlink($filename);

}


# +++ HE-FIX/MemorySaver +++ #
# imagedestroy($img_org);

imagedestroy($img_thu);
return ($thumb);

}



function image_get_thumb_file ($bild, $width=100, $height=75, $quality=65, $useoutbuffering=true) {

// Eingespeicherter Thumb
list($imgwidth, $imgheight, $type) = @getimagesize($bild); #EXIF_IMAGETYPE;
if ($type==IMAGETYPE_JPEG || $type==IMAGETYPE_TIFF_II || $type==IMAGETYPE_TIFF_MM) {if (($vorschau=exif_thumbnail($bild))!==false) {return ($vorschau);}}


if ($type ==  IMAGETYPE_JPEG) {$img_org = imagecreatefromjpeg($bild);
} elseif ($type ==  IMAGETYPE_BMP) {$img_org = imagecreatefrombmp($bild);
} elseif ($type ==  IMAGETYPE_PNG) {$img_org = imagecreatefrompng($bild);
} elseif ($type ==  IMAGETYPE_GIF) {$img_org = imagecreatefromgif($bild);
} else {return false;}



	if (!empty($width) && !empty($height)) {
	
	$percent=min(array($height/$imgheight,$width/$imgwidth));
	$newwidth = $imgwidth * $percent;
	$newheight = $imgheight * $percent;

	} elseif (!empty($height)) {
	$newwidth = $height/$imgheight * $imgwidth;
	$newheight = $height;

	} elseif (!empty($width)) {
	$newwidth = $width;
	$newheight = $width/$imgwidth * $imgheight;
	
	} else {
	return false;
	
	}


$img_thu=imagecreatetruecolor($newwidth, $newheight);

imagecopyresampled($img_thu, $img_org, 0, 0, 0, 0, $newwidth, $newheight, $imgwidth, $imgheight);
ini_set('output_buffering','on');


if ($useoutbuffering && ob_start()) {

imagejpeg($img_thu, null, $quality);
$thumb = ob_get_contents();
ob_end_clean();


} else {

$filename=tempnam(get_he_tmp_dir(), 'img-handler-abi');
imagejpeg($img_thu, $filename, $quality);
$thumb = file_get_contents($filename);
unlink($filename);

}


// Beide
imagedestroy($img_org);
imagedestroy($img_thu);

return ($thumb);

}


# +++ HE-FIX [SYS_GET_TEMP_DIR()] +++ #
# /is/htdocs/user_tmp/wp10599438_SYSDRE4LOW/

function get_he_tmp_dir() {

$t=explode('/',substr(getcwd(),1));
array_splice($t, 2, 0, 'user_tmp');

return '/'.implode('/',array_slice($t,0,4));

}


?>