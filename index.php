<html>
<head>
    <title>Home</title>
    <meta charset="8-utf"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="siteHead">
    <a href="https://github.com/sl05/TeamFulda2019_1" target="_blank"><img id="logo" src="Study-Home.png" alt="Logo"/></a>
    <h2>Team Project</h2>
    <h2>Summer 2019 in Fulda</h2>
    <h2>TeamFulda2019_1</h2>
</div>
<h3>Team Members:</h3>

<?php

//$directory = "C:/Users/fdai3744/Desktop/testing/";
$directory = "./";
$phpfiles = glob($directory . "*.profile.html");
$names = ["Manuel Schmitt", "Michael Iglhaut", "Moritz Mrosek", "Ramon Wilhelm", "Simon Leister"];
$arrayLength = count($names);
$i = 0;

foreach ($phpfiles as $phpfile)
{
    //echo file_get_contents($phpfile);
    if($i < $arrayLength){
        echo '<p class="nameButton"><a class="hoverFX" href="' . basename($phpfile) . '">' . $names[$i] . '</a></p>';
        $i++;
    }
}

//echo file_get_contents("C:/Users/fdai3744/Desktop/testing/ramon.profile.html");


?>
</body>

</html>
