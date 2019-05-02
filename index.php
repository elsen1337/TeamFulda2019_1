<html>
<head>
	<title>Home</title>
	<meta charset="8-utf"/>
</head>
<body>
<?php 

//$directory = "C:/Users/fdai3744/Desktop/testing/";
$directory = "./";
$phpfiles = glob($directory . "*.html");

foreach ($phpfiles as $phpfile)
{
	//echo file_get_contents($phpfile);
	echo '<p><a href="'.basename($phpfile).'">'.$phpfile.'Blubb</a></p>';
	
}

 //echo file_get_contents("C:/Users/fdai3744/Desktop/testing/ramon.profile.html");


?>
</body>

</html>
