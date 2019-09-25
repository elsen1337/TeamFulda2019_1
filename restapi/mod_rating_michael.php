<?php

    function ratingList($vmid) {
        $sql='SELECT * FROM v_rating WHERE vm_id='.$vmid.'';
		$mrs=$GLOBALS['msdb']->query($sql);
		$attrarr=[];
		while ($obj=$mrs->fetch_object()) {
			$attrarr[]=$obj;
		}
		return $attrarr;
    }

    function ratingAddUpdate($vm_id,$m_id,$stars,$cmt) {
        $sql='INSERT INTO v_rating (vm_id, m_id, stars, cmt) VALUES ('.$vm_id.', '.$m_id.', '.$stars.', "'.$cmt.'")';
        $mrs=$GLOBALS['msdb']->query($sql);
        return $GLOBALS['msdb']->affected_rows > 0; // !!! Spezialfall 0,1(Insert),2(Update)
    }

    if ($_SERVER['REQUEST_METHOD']=='GET') {
        //Ausgeben aller Kommentare
        $lst=ratingList($objkey);
        header('Content-type: application/json');
        echo json_encode($lst);

    } elseif ($_SERVER['REQUEST_METHOD']=='PUT') {

    } elseif ($_SERVER['REQUEST_METHOD']=='POST') {
        //hinzufügen eines neuen Kommentars
        $actResult=ratingAddUpdate($postParam['vm_id'],$postParam['m_id'],$postParam['stars'],$postParam['cmt']);
        sendDefaultActionRequestBody($actResult,$msdb);

    } elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {

    } else {

        notAllowed();

    }
?>
