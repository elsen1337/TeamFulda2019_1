<?php



function parseCommand(&$cmd,$route) {

    if (strpos($cmd , $route) !== false) {

        $cmd=substr($cmd,strlen($route));
        return true;

    }

    return false;

}


function getRequestBody() {

return file_get_contents('php://input');

}


function getJSONFromRequestBody($str=null,$ascarr=true) {

if (str === null) {$str=getRequestBody();} 
return json_decode($str,$ascarr);

}


function getPostParameter() {

    if (stripos($_SERVER["CONTENT_TYPE"],'application/json')!==false) {
        return getJSONFromRequestBody();
    } elseif (stripos($_SERVER["CONTENT_TYPE"],'multipart/form-data')!==false) {
        return $_POST;
    } else {
        return $_POST;
    }

}




$action=$_GET['objAction'];
$objkey=$_GET['objKey'];


/*
Manu: Bilder Eigenschaften Attribute
Bilder: Insert
Vermieter: Login
*/




require('../core-mysqla.php');


if (parseCommand($action,'estate')) {


    require('../kernel/class-search.php');
    

    if (parseCommand($action,'search')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            SearchForm::generateForm();
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            // Create
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            SearchForm::updateSeachSession();


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
            // Delete

        }  
        
    
    } elseif (parseCommand($action,'default')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getDefaultProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            // Create
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            // Update

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
            // Delete

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
 
            // Create
            $postParam=getPostParameter();
            $newImgID=AppartImage::addImage( array_intersect_key($postParam,AppartImage::$formFields) );
            echo '{"newImgID":'.$newImgID.'}';
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            // Update
            	
            require('../kernel/class-string.php');
            require('../kernel/image-support-thumb.php');
            
            chdir('../'.AppartImage::$uploadBaseDir);
            
            $bildUpload=&$_FILES['bild'];
            AppartImage::uploadImage($bildUpload);
            
            


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
            // Delete

        }   


    }

} elseif (parseCommand($action,'lessor')) {


    session_start();
    require('../kernel/class-lessor.php');
    
    
    // Loginstatus; nicht VermieterKonto !
    if (parseCommand($action,'login')) {

         
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            $aKey='LAUTH';
            if (array_key_exists($aKey,$_SESSION)) {$_SESSION[$aKey]=array();}
            echo json_encode($_SESSION[$aKey]);
        
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $postParam=getPostParameter();
        
            $usr=Lessor::login($postParam['user'],$postParam['auth']);
            $_SESSION['LAUTH']['id']=$usr->vm_id; // Wahlweise auch mehrer Daten in Session kopieren; z.B Nutzernamenanzeige: Hallo <Name>....
            echo json_encode($usr);
            

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
             $_SESSION['LAUTH']=array();

        }   


    } elseif (parseCommand($action,'account')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            // 2Do
        
            
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $postParam=getPostParameter();
            $newObjID=Lessor::register($postParam);
            echo '{"newLessorID":'.$newObjID.'}';

 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $postParam=getPostParameter();
            // 2Do
        


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
             $_SESSION['LAUTH']=array();
             // 2Do

        }

    }
    

} elseif (parseCommand($action,'tenant')) {


}




?>
