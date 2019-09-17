<?php



function parseCommand(&$cmd,$route) {

    if (strpos($cmd , $route) === 0) {

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


/*
Behandlung bei den wenigen Spezialfällen; Suche und Bilder
	if ($_SERVER['REQUEST_METHOD']=='PUT') {
		
		$reqBody=getRequestBody();
        parse_str($reqBody,$_REQUEST);
        
	}
*/

	if (stripos($_SERVER["CONTENT_TYPE"],'application/json')!==false) {
		return getJSONFromRequestBody();
		
	} else {
		
		if (stripos($_SERVER["CONTENT_TYPE"],'multipart/form-data')!==false) {
			return $_POST; // + $_FILES durch MultipartFormDataParser bei Requests <> POST
		
		} else {
			// Default: application/x-www-form-urlencoded
			return $_POST;
		}

	}

}




function requestHeaders() {
	$headers = array();
	foreach($_SERVER as $key => $value) {
		if(substr($key, 0, 5) == 'HTTP_') {
			$key=ucwords(str_replace('_', ' ',  strtolower(substr($key, 5)) ));
			$headers[str_replace(' ', '-',  $key)] = $value;
		}
	}
	return $headers;
}



function sendDefaultActionRequestBody($actState,$sqlCon,$otherProps=array()) {

	header('Content-type: application/json');
	$restResponse=$otherProps+array('actSuccess'=>var_export($actState, true),'sqlError'=>$sqlCon->error);
	echo json_encode($restResponse);
	
	# echo '{"actSuccess":'.var_export($actState, true).',"sqlError":"'.$sqlCon->error.'"}';

}



$action=$_GET['objAction'];
$objkey=$_GET['objKey'];


$postParam=getPostParameter();
$reqHeaders=requestHeaders();



// (Chat), Meeting, BilddatenPATCH:OK, UpdateEstateData:OK, [Metadaten:OK]



require('../core-mysqla.php');


if (parseCommand($action,'estate')) {


	require('../kernel/class-estate.php');
    

	if (parseCommand($action,'search')) {
	
	
		session_start();
		require('../kernel/class-search.php');
		
		#print_r($_SESSION);
		
		if (parseCommand($action,'session')) {
		
			$sid=$reqHeaders['X-Additional-Key'];
			
			#print_r($reqHeaders);
			#var_dump($sid);

			if ($_SERVER['REQUEST_METHOD']=='GET') {
			
				$retJson=SearchForm::getSearchSessionsList($objkey);
				header('Content-type: application/json');
				echo json_encode($retJson);


			} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

				$loadedSessionID=SearchForm::loadStoredSession($objkey,$sid); // MID; 2Do: Optionale SID
				sendDefaultActionRequestBody(($loadedSessionID>0), $msdb, array('loadedSessionID'=>$loadedSessionID));

				#print_r($_SESSION[SearchForm::$searchKeyGlobal]);
				#noContent();


			} elseif ($_SERVER['REQUEST_METHOD']=='POST') {

				$resArr=SearchForm::storeSession($objkey,$sid); // MID; 2Do: Optionale SID
				if ($resArr['newSearchSessionID'] > 0) {objCreated();}
				
				sendDefaultActionRequestBody(null,$msdb,$resArr);


			} elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

				$actResult=SearchForm::deleteStoredSession($objkey);
				sendDefaultActionRequestBody($actResult,$msdb);


			} else {
			
				notAllowed();

			}
		
		// Besser: searchform; jetzt jedoch keine Änderungen mehr.
		} else {

    
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


			} else {
			
				notAllowed();

			}
        
		}
        

    } elseif (parseCommand($action,'default')) {
    
		if ($_SERVER['REQUEST_METHOD']=='GET') {
		
			header('Content-type: application/json');
			echo json_encode(Estate::getDefaultProperties($objkey));
		
		
		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
		
			$actResult=Estate::update($postParam,$objkey);
			sendDefaultActionRequestBody($actResult,$msdb);


		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
		
			// DEBUG: Visible=1 (!!!)
			$newObjID=Estate::create($postParam+array('visible'=>'1'));
						
			header('Content-type: application/json');
			if ($newObjID > 0) {objCreated();}
			
			echo '{"newEstateID":'.$newObjID.',"sqlError":"'.$msdb->error.'"}';


		} elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
		
			// Internal / Partial 2Do
			$actResult=Estate::delete($objkey);
			sendDefaultActionRequestBody($actResult,$msdb);


		} else {
		
			notAllowed();

		}


    } elseif (parseCommand($action,'attribute')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            header('Content-type: application/json');
            echo json_encode(Estate::getDynamicProperties($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
			$actResult=Estate::updateAttrib($postParam);
			sendDefaultActionRequestBody($actResult,$msdb);
 
      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$actResult=Estate::deleteAttrib($objkey,$postParam['akeys']);
			sendDefaultActionRequestBody($actResult,$msdb);

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
			#sendDefaultActionRequestBody($actResult,$msdb,array('newImgID'=>$newImgID));
	
	
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
		
			// Update
				
			require('../kernel/class-string.php');
			require('../kernel/image-support-thumb.php');
			
			chdir('../'.AppartImage::$uploadBaseDir);
			
			$bildUpload=&$_FILES['bild'];
			$actResult=AppartImage::uploadImage($bildUpload);
            
			sendDefaultActionRequestBody($actResult,$msdb);


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

        
    } elseif (parseCommand($action,'meeting')) {

      
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
            header('Content-type: application/json');
            echo json_encode(Estate::getProposedMeetingSlots($objkey));
        
        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {
        
   
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
			$newObjID=Estate::addMeetingSlotProposal($postParam['wohn_id'],$postParam['slot']);
			if ($newObjID > 0) {objCreated();}

			header('Content-type: application/json');
			echo '{"newMeetID":'.var_export($newObjID,true).',"sqlError":"'.$msdb->error.'"}';

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			$actResult=Estate::removePredefinedMeetingSlot($objkey);

			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult,true).',"sqlError":"'.$msdb->error.'"}';
       
        } else {
        
            notAllowed();

        } 
    } elseif (parseCommand($action,'stream')) {

		$HOST = '46.244.200.160';
		$PORT = 21567;
		$BUFSIZE = 1024;
		set_time_limit(0);
		$tcpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		$tcpSocketCon = socket_connect($tcpSocket, $HOST, $PORT) or die ("Could not connect to server\n");
		
		
		if ($_SERVER['REQUEST_METHOD']=='GET')
		{				
			$speed = 30;
			$tmp = 'speed';
			$data = $tmp.strval($speed);
			socket_write($tcpSocket, $data, strlen($tmp.$data)) or die ("Could not send speed data to server\n");
			header('Content-type: application/json');
			echo '{"ping":"true"}';

		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {			
		
			echo 'Sent: '.$postParam['event'];
			$speed = 30;
			$tmp = 'speed';
			$data = $tmp.strval($speed);
			socket_write($tcpSocket, $data, strlen($tmp.$data)) or die ("Could not send speed data to server\n");

			socket_write($tcpSocket, $postParam['event'], strlen($postParam['event'])) or die ("Could not send data to server\n");
			//socket_write($tcpSocket, 'stop', strlen('stop')) or die ("Could not send data to server\n");
			socket_close($tcpSocket);

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


				#var_dump($_SESSION[Lessor::$sKey]['id']);
				#var_dump($objkey);
				

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
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';


      
        } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
            $newObjID=Lessor::register($postParam);

            header('Content-type: application/json');
            if ($newObjID > 0) {objCreated();}

            echo '{"newLessorID":'.$newObjID.',"sqlError":"'.$msdb->error.'"}';


        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {


			// Internal 2Do
            $actResult=Lessor::delete($objkey);

			header('Content-type: application/json');
			echo '{"actSuccess":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';


        } else {
        
            notAllowed();
            
        }

        
	} elseif (parseCommand($action,'estate')) {
    
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			header('Content-type: application/json');
			$usr=Lessor::getEstates($objkey);
			echo json_encode($usr);

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
			sendDefaultActionRequestBody($actResult,$msdb);

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        
			// Not NotImplemented

		} elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
        
			list($postParam['m_id'],$postParam['wohn_id'])=explode('-',$objkey);
			$actResult=Tenant::favouriteRemove($postParam['m_id'],$postParam['wohn_id']);

			sendDefaultActionRequestBody($actResult,$msdb);


		} else {
		
			notAllowed();
			
		}        

	} elseif (parseCommand($action,'meeting')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Tenant::getMyMeetingSlots($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

			$actResult=Tenant::addMeetingSlot($postParam['m_id'],$postParam['tid']);
			sendDefaultActionRequestBody($actResult,$msdb);

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        

		} elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

			$actResult=Tenant::removeMeetingSlot($postParam['m_id'],$postParam['tid']);
			sendDefaultActionRequestBody($actResult,$msdb);


        } else {
        
            notAllowed();
            
        }

        
	} elseif (parseCommand($action,'rating')) {
	
		require('mod_rating_michael.php');

    }

} elseif (parseCommand($action,'chat')) {


    require('../kernel/class-chat.php');

    if (parseCommand($action,'lessors4tenant')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Chat::getChatPartner4Tenant($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {

			$actResult=Chat::insertMessageFromLessor2Tenant($postParam['vm_id'],$postParam['m_id'],$postParam['msg']);

			header('Content-type: application/json');
			echo '{"success":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';
        
        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        } else {
        
            notAllowed();
            
        }
       
       
	}  elseif (parseCommand($action,'tenants4lessor')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Chat::getChatPartner4Lessor($objkey);
			header('Content-type: application/json');
			echo json_encode($lst);
   

        } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

      
		} elseif ($_SERVER['REQUEST_METHOD']=='POST') {

			$actResult=Chat::insertMessageFromTenant2Lessor($postParam['m_id'],$postParam['vm_id'],$postParam['msg']);

			header('Content-type: application/json');
			echo '{"success":'.var_export($actResult, true).',"sqlError":"'.$msdb->error.'"}';

        } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

        } else {
        
            notAllowed();
            
        }
        
	}  elseif (parseCommand($action,'lastmsg')) {
    
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        
			$lst=Chat::getLastMessages4LessorTenant($_GET['vm_id'],$_GET['m_id']);
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
