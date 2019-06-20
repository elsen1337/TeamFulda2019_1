<?php



function parseCommand(&$cmd,$route) {

    if (strpos($cmd , $route) !== false) {

        $cmd=substr($cmd,strlen($route));
        return true;

    }

    return false;

}


// 202 Accepted, 201 Created, 204 'No Content', 400 BadRequest, 401 'Unauthorized' VS 403 'Forbidden', 404, 410
function notAllowed() {sentHeader(405,'NotAllowed');}
function objUpdated() {sentHeader(204,'No Content');}
function objRemoved() {sentHeader(204,'No Content');}
function objCreated() {sentHeader(201,'Created');}
function objProcessing() {sentHeader(202,'Accepted');}


function sentHeader($code, $msg) {
    header( implode("\x20", array($_SERVER['SERVER_PROTOCOL'], $code, $msg) ) );
}




function getRequestBody() {

return file_get_contents('php://input');

}


function getJSONFromRequestBody($str=null,$ascarr=true) {

if ($str === null) {$str=getRequestBody();} 
return json_decode($str,$ascarr);

}


function getPostParameter() {

    if (stripos($_SERVER["CONTENT_TYPE"],'application/json')!==false) {
        return getJSONFromRequestBody();
        
    } elseif (stripos($_SERVER["CONTENT_TYPE"],'multipart/form-data')!==false) {
        return $_POST + $_FILES; # ?
    
    } else {
        // Default: application/x-www-form-urlencoded
        return $_POST;
    }

}




$action=$_GET['objAction'];
$objkey=$_GET['objKey'];


$postParam=getPostParameter();





require('../core-mysqla.php');


if (parseCommand($action,'estate')) {


    require('../kernel/class-estate.php');
    

    if (parseCommand($action,'search')) {
    
    
        session_start();
        require('../kernel/class-search.php');
        
        #print_r($_SESSION);

    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            SearchForm::generateForm();
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            SearchForm::updateSearchSession();
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            SearchForm::performSearch();

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        

        } else {
        
            notAllowed();

        }
        
    
    } elseif (parseCommand($action,'default')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getDefaultProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
        
            $newObjID=Estate::createEstate($postParam);
                        
            header('Content-type: application/json');
            echo '{"newEstateID":'.$newObjID.'}';

       
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $newObjID=Estate::createEstate($postParam);
                        
            header('Content-type: application/json');
            echo '{"newEstateID":'.$newObjID.'}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
            
        
        } else {
        
            notAllowed();

        }


    } elseif (parseCommand($action,'attribute')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getDynamicProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        } else {
        
            notAllowed();

        } 
    
    } elseif (parseCommand($action,'images')) {
    
        require('../kernel/class-appartimg.php');

         
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(Estate::getImagesMetaData($objkey));

            
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            $newImgID=AppartImage::addImage($postParam);

            header('Content-type: application/json');
            echo '{"newImgID":'.$newImgID.'}';
      
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            // Update
            	
            require('../kernel/class-string.php');
            require('../kernel/image-support-thumb.php');
            
            chdir('../'.AppartImage::$uploadBaseDir);
            
            $bildUpload=&$_FILES['bild'];
            AppartImage::uploadImage($bildUpload);
            
            


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
            
            chdir('../'.AppartImage::$uploadBaseDir);
            
            AppartImage::removeImage($objkey);


        } else {
        
            notAllowed();
        
        }   


    }

} elseif (parseCommand($action,'lessor')) {


    session_start();
    require('../kernel/class-lessor.php');
    
    
    // Loginstatus; nicht VermieterKonto !
    if (parseCommand($action,'login')) {

         
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            $aKey=Lessor::$sKey;
            if (array_key_exists($aKey,$_SESSION)===false) {$_SESSION[$aKey]=array();}

            header('Content-type: application/json');
            echo json_encode($_SESSION[$aKey]);
        
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            // NotImplemented
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
        
            $usr=Lessor::login($postParam['user'],$postParam['auth']);
            $_SESSION['LAUTH']['id']=$usr->{Lessor::$entPrimKey}; // Wahlweise auch mehrer Daten in Session kopieren; z.B Nutzernamenanzeige: Hallo <Name>....

            header('Content-type: application/json');
            echo json_encode($usr);
            

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
             $_SESSION[Lessor::$sKey]=array();

        } else {
            notAllowed();
        }  


    } elseif (parseCommand($action,'account')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            // 2Do
            
            
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $newObjID=Lessor::update($postParam);

            header('Content-type: application/json');
            echo '{"newLessorID":'.$newObjID.'}';

      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $newObjID=Lessor::register($postParam);

            header('Content-type: application/json');
            echo '{"newLessorID":'.$newObjID.'}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {



            $newObjID=Lessor::delete($objkey);

            header('Content-type: application/json');
            echo '{"newLessorID":'.$newObjID.'}';

        } else {
            notAllowed();
        }

    }
    

} elseif (parseCommand($action,'tenant')) {




    if (parseCommand($action,'login')) {
    
    
        
    
    } elseif (parseCommand($action,'account')) {
    } elseif (parseCommand($action,'account')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            // 2Do
        
            
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $newObjID=Tenant::update($postParam);

            header('Content-type: application/json');
            echo '{"state":'.$newObjID.'}';

 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            // 2Do
        


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
             $_SESSION['LAUTH']=array();
             // 2Do

        }

    }



}




?>
