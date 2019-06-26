<?php



function parseCommand(&$cmd,$route) {

    if (strpos($cmd , $route) !== false) {

        $cmd=substr($cmd,strlen($route));
        return true;

    }

    return false;

}


// 202 Accepted, 201 Created, 204 'No Content', 400 BadRequest, 401 'Unauthorized' VS 403 'Forbidden', 404, 410
function notAllowed() {sendHeader(405,'Not Allowed');}
function noCredentials() {sendHeader(401,'Unauthorized');}
function forbiddenAccess() {sendHeader(403,'Forbidden');}

function objUpdated() {sendHeader(204,'No Content');}
function objRemoved() {sendHeader(204,'No Content');}

function objCreated() {sendHeader(201,'Created');}
function objProcessing() {sendHeader(202,'Accepted');}

function noContent() {sendHeader(204,'No Content');}


function sendHeader($code, $msg) {
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

	if ($_SERVER['REQUEST_METHOD']=='PUT') {
		
		// $reqBody=getRequestBody();
        // parse_str($reqBody,$_REQUEST);
        
	}

    if (stripos($_SERVER["CONTENT_TYPE"],'application/json')!==false) {
        return getJSONFromRequestBody();
        
    } else {
    
		// parse_str POST + 
    
		if (stripos($_SERVER["CONTENT_TYPE"],'multipart/form-data')!==false) {
			return $_POST; // + $_FILES; # ?
		
		} else {
			// Default: application/x-www-form-urlencoded
			return $_POST;
		}

    }

}




$action=$_GET['objAction'];
$objkey=$_GET['objKey'];


$postParam=getPostParameter();


// (Chat), Meeting, BilddatenPATCH:OK, UpdateEstateData:OK, [Metadaten:OK]



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
        
            $reqBody=getRequestBody();
            
            require('../kernel/class-formdata.php');
            
            $mPartFDataParser=new MultipartFormData($reqBody);
            $_REQUEST=$mPartFDataParser->getFormData();
            
            #X-WWW-FORM
            #parse_str($reqBody,$_REQUEST);
            #print_r($_REQUEST);
            
            #var_dump($reqBody);
            #var_dump($_SERVER["CONTENT_TYPE"]);
            
 			#print_r($_REQUEST);
 			#print_r($postParam);
 			
			#print_r($_SERVER);
			
 
            SearchForm::updateSearchSession();
            objProcessing();
            
            header('Content-type: application/json');
            echo json_encode($_SESSION[SearchForm::$searchKeyGlobal]);
	
	
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
        
            require('../kernel/class-appartimg.php');

            header('Content-type: application/json');
            list($amnt,$view)=SearchForm::performSearch([],[],[]);
            header('X-SearchResultSize-Overall: '.$amnt);
            header('X-SearchResultSize-Limit: '.$view);
            

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
        
            SearchForm::resetSession();
            noContent();
            
            #print_r($_SESSION[SearchForm::$searchKeyGlobal]);
        

        } else {
        
            notAllowed();

        }
        
    
    } elseif (parseCommand($action,'default')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            header('Content-type: application/json');
            echo json_encode(Estate::getDefaultProperties($objkey));
        
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $newObjID=Estate::update($postParam,$objkey);
			
			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult,true).',"sqlError":"'.$msdb->error.'"}';


		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
			// DEBUG: Visible=1 (!!!)
            $newObjID=Estate::create($postParam+array('visible'=>'1'));
                        
            header('Content-type: application/json');
            if ($newObjID > 0) {objCreated();}
            
            echo '{"newEstateID":'.$newObjID.',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			// Internal / Partial 2Do
			$actResult=Estate::delete($objkey);
			
			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';


        } else {
        
            notAllowed();

        }


    } elseif (parseCommand($action,'attribute')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            header('Content-type: application/json');
            echo json_encode(Estate::getDynamicProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
			$actResult=Estate::updateAttrib($postParam);
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$actResult=Estate::deleteAttrib($objkey,$postParam['akeys']);
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';

        } else {
        
            notAllowed();

        }
	
	
    } elseif (parseCommand($action,'attribmeta')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            header('Content-type: application/json');
            echo json_encode(Estate::getAttributeList());
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
   
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
        
        } else {
        
            notAllowed();

        } 
    
    } elseif (parseCommand($action,'images')) {
    
        require('../kernel/class-appartimg.php');
		
         
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            echo json_encode(AppartImage::getImagesMetaData($objkey));

            
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
 
            $newImgID=AppartImage::addImage($postParam);

            header('Content-type: application/json');
            echo '{"newImgID":'.$newImgID.',"sqlError":"'.$msdb->error.'"}';
      
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            // Update
            	
            require('../kernel/class-string.php');
            require('../kernel/image-support-thumb.php');
            
            chdir('../'.AppartImage::$uploadBaseDir);
            
            $bildUpload=&$_FILES['bild'];
            $actResult=AppartImage::uploadImage($bildUpload);
            
			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult,true).',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='PATCH') {
        
			$actResult=AppartImage::updateMetaData($postParam,$objkey);

			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult,true).',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
            
            chdir('../'.AppartImage::$uploadBaseDir);
            
            $actResult=AppartImage::removeImage($objkey);
            
			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult,true).',"sqlError":"'.$msdb->error.'"}';



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
        
        
            $usr=Lessor::login($postParam['email'],$postParam['pwort']);
            $_SESSION[Lessor::$sKey]['id']=$usr->{Lessor::$entPrimKey}; // Wahlweise auch mehrer Daten in Session kopieren; z.B Nutzernamenanzeige: Hallo <Name>....

            header('Content-type: application/json');
            echo json_encode($usr);
            

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$_SESSION[Lessor::$sKey]=array();
			noContent();

        } else {
        
            notAllowed();
            
        }  


    } elseif (parseCommand($action,'account')) {
    
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			session_start();
        
			if (is_array($_SESSION[Lessor::$sKey]) && array_key_exists('id',$_SESSION[Lessor::$sKey])) {
			
				if ($_SESSION[Lessor::$sKey]['id']==$objkey) {
            
					header('Content-type: application/json');
					$usr=Lessor::about($objkey);
					echo json_encode($usr);
					
				} else {
				
					noCredentials();
				
				}
					
            } else {

				forbiddenAccess();
				
            }
            
            
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $actResult=Lessor::update($postParam,$objkey);

            header('Content-type: application/json');
			echo '{"actSuccess":'.$actResult.',"sqlError":"'.$msdb->error.'"}';


      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $newObjID=Lessor::register($postParam);

            header('Content-type: application/json');
            if ($newObjID > 0) {objCreated();}

            echo '{"newLessorID":'.$newObjID.',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {



			// Internal 2Do
            $actResult=Lessor::delete($objkey);

			header('Content-type: application/json');
			echo '{"actSuccess":'.$actResult.',"sqlError":"'.$msdb->error.'"}';


        } else {
        
            notAllowed();
            
        }

    }
    

} elseif (parseCommand($action,'tenant')) {


    session_start();
    require('../kernel/class-tenant.php');
   

    if (parseCommand($action,'login')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            $aKey=Tenant::$sKey;
            if (array_key_exists($aKey,$_SESSION)===false) {$_SESSION[$aKey]=array();}

            header('Content-type: application/json');
            echo json_encode($_SESSION[$aKey]);
        
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            // NotImplemented
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
        
            $usr=Tenant::login($postParam['email'],$postParam['pwort']);
            $_SESSION[Tenant::$sKey]['id']=$usr->{Tenant::$entPrimKey}; // Wahlweise auch mehrer Daten in Session kopieren; z.B Nutzernamenanzeige: Hallo <Name>....

            header('Content-type: application/json');
            echo json_encode($usr);
            

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$_SESSION[Tenant::$sKey]=array();
			noContent();

        } else {
        
            notAllowed();
            
        }      
        
    
    } elseif (parseCommand($action,'account')) {

    
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			session_start();
        
			if (is_array($_SESSION[Tenant::$sKey]) && array_key_exists('id',$_SESSION[Tenant::$sKey])) {
			
				if ($_SESSION[Tenant::$sKey]['id']==$objkey) {
            
					header('Content-type: application/json');
					$usr=Tenant::about($objkey);
					echo json_encode($usr);
					
				} else {
				
					noCredentials();
				
				}
					
            } else {

				forbiddenAccess();
				
            }
            
            
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
            $actResult=Tenant::update($postParam,$objkey);
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';

      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $newObjID=Tenant::register($postParam);

            header('Content-type: application/json');
            if ($newObjID > 0) {objCreated();}

            echo '{"newTenantID":'.$newObjID.',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

			// Internal 2Do
            $actResult=Tenant::delete($objkey);
			
			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';


        } else {
        
            notAllowed();
            
        }
        
        
    
    } elseif (parseCommand($action,'favorit')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Tenant::favouriteList($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
		
			$actResult=Tenant::favouriteAddUpdate($postParam['m_id'],$postParam['wohn_id'],$postParam['score']);

			header('Content-type: application/json');
			echo '{"success":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
			// Not NotImplemented

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$actResult=Tenant::favouriteRemove($postParam['m_id'],$postParam['wohn_id']);

			header('Content-type: application/json');
			echo '{"success":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';


        } else {
        
            notAllowed();
            
        }
        

    }

} elseif (parseCommand($action,'chat')) {


    require('../kernel/class-chat.php');
   

    if (parseCommand($action,'lessors4tenant')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Chat::getChatPartner($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        } else {
        
            notAllowed();
            
        }
       
       
	}  elseif (parseCommand($action,'tenants4lessor')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Chat::getChatPartner($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        } else {
        
            notAllowed();
            
        }
        
	}
        
} else {

	notAllowed();

}




if (array_key_exists('debug',$_GET)) {


	echo "Session-Daten (Sofern SESSION_START() in enstsprechender Sektion):\n";
	print_r($_SESSION);

	echo "Body-Parameter (Except PUT Requests; 2 Be Fixed):\n";
	print_r($postParam);

	#echo "Session-Daten:\n";
	#print_r($_SESSION);

}



?>
