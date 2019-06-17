<?php


$action=$_GET['objAction'];
$objkey=$_GET['objKey'];


/*
Manu: Bilder Eigenschaften Attribute
Bilder: Insert
Vermieter: Login
*/


function parseCommand(&$cmd,$route) {

    if (strpos($cmd , $route) !== false) {

        $cmd=substr($cmd,strlen($route));
        return true;

    }

    return false;

}



require('../core-mysqla.php');


if (parseCommand($action,'estate')) {


    require('../kernel/class-estate.php');
    

    if (parseCommand($action,'default')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getDefaultProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        }


    } elseif (parseCommand($action,'attribute')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getDynamicProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        }  
    
    } elseif (parseCommand($action,'images')) {
    
        require('../kernel/class-appartimg.php');

         
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getImagesMetaData($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        }   


    }

} elseif (parseCommand($action,'vermieter')) {

} elseif (parseCommand($action,'mieter')) {


}




?>
