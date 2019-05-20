<?php


class GUI {



/*

SATE &#x2714; &#x2716; &#x2bbf;
STAR &#x2730; &#9733; &#9734; &#10038;
EDIT &#x270E; &#x270D; &#x2699; &#x267B; &#x229d;
WARN &#x26A0; &#x26A1;
WAIT &#x231B;
BARR &#x2357; &#x21F1; &#x21F2; &#x238b
RARR &#x2936; &#x25c4; &#x21b5; &#x25c0; &#x25c5; &#x21b2; &#x2923;
NARR &#x2794; &#x279C;
UARR &#x25B2;
SDEL &#x232B; &#x2326; &#x2421; &#x2327;
INTC &#x263C; &#x2691;

*/


	private static $symbols=array (

		'help'=>'&#xfffd;', 'return'=>'&#x21f1;', 'setting'=>'&#x2699;',
		'insert'=>'&#x2719;', 'treat'=>'&#x270E;', 'trash'=>'&#x232B;',
		'information'=>'&#x261E;', 'critical'=>'&#x26A0;', 'strict'=>'&#x26A1;',
		'failure'=>'&#x2716;', 'correct'=>'&#x2714;'

	);



	public static function evaluateResult($bool,$success=null,$error=null) {

		if (empty($error)) {$error='Misslungene Verarbeitung';}
		if (empty($success)) {$success='Erfolgreiche Verarbeitung';}


		if ($bool===null) {return null;}

		if (is_array($bool)) {$bool=(count($bool)==array_sum($bool));}
		if ($bool) {self::printSuccess($success);} else {self::printFail($error);}

	}


	public static function getMessage($m,$n,$a=null,$t=null) {

		if (empty($t)) {$t='p';} if (array_key_exists($m,self::$symbols)==false) {return;}
		return '<'.$t."\x20".$a.' class="'.$m.'"><span class="symbol">'.self::$symbols[$m].'</span>'.$n.'</'.$t.'>';

	}



	public static function echoArray($a) {

		echo '<pre>'; print_r($a); echo '</pre>';

	}



	public static function printHelp($m,$a=null,$t=null) {

		echo self::getMessage('help',$m,$a,$t);

	}


	public static function printBack($m,$a=null,$t=null) {

		echo self::getMessage('return',$m,$a,$t);

	}


	public static function printSetting($m,$a=null,$t=null) {

		echo self::getMessage('setting',$m,$a,$t);

	}


	public static function printInsert($m,$a=null,$t=null) {

		echo self::getMessage('insert',$m,$a,$t);

	}



	public static function printEdit($m,$a=null,$t=null) {

		echo self::getMessage('treat',$m,$a,$t);

	}


	public static function printTrash($m,$a=null,$t=null) {

		echo self::getMessage('trash',$m,$a,$t);

	}



	public static function printNotice($m,$a=null,$t=null) {

		echo self::getMessage('information',$m,$a,$t);

	}


	public static function printWarn($m,$a=null,$t=null) {

		echo self::getMessage('critical',$m,$a,$t);

	}


	public static function printError($m,$a=null,$t=null) {

		echo self::getMessage('strict',$m,$a,$t);

	}



	public static function printFail($m,$a=null,$t=null) {

		echo self::getMessage('failure',$m,$a,$t);

	}


	public static function printSuccess($m,$a=null,$t=null) {

		echo self::getMessage('correct',$m,$a,$t);

	}



}


?>