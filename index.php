<html>
<head>
	<title>Home</title>
	<meta charset="8-utf"/>
</head>
<body>

	<h2>Team Project</h2>
	<h2>Summer 2019</h2>
	<h2>TeamFulda2019_1</h2>

<?php 

//$directory = "C:/Users/fdai3744/Desktop/testing/";
$directory = "./";
$phpfiles = glob($directory . "*.html");
$names = [
	0 => "Manuel",
	1 => "Michael",
	2 => "Moritz",
	3 => "Ramon",
	4 => "Simon"
];
$i = 0;

foreach ($phpfiles as $phpfile)
{
	//echo file_get_contents($phpfile);
	echo '<p><a href="'.basename($phpfile).'">'.$names[i].'</a></p>';
	$i++;
}

 //echo file_get_contents("C:/Users/fdai3744/Desktop/testing/ramon.profile.html");


?>
</body>

</html>
