<?php

		$HOST = '46.244.200.160';
		$PORT = 21567;
		$BUFSIZE = 1024;
		set_time_limit(0);
		$tcpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		$tcpSocketCon = socket_connect($tcpSocket, $HOST, $PORT) or die ("Could not connect to server\n");

		$speed = 30;
		$tmp = 'speed';
		$data = $tmp.$speed;
		socket_write($tcpSocket, $data, strlen($data)) or die ("Could not send speed data to server\n");
		socket_close($tcpSocket);
		$tcpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		$tcpSocketCon = socket_connect($tcpSocket, $HOST, $PORT) or die ("Could not connect to server\n");

		//$speed = 30;
		//$tmp = 'speed';
		//$data = $tmp.strval($speed);
		//socket_write($tcpSocket, $data, strlen($tmp.$data)) or die ("Could not send speed data to server\n");

		//socket_write($tcpSocket, $postParam['event'], strlen($postParam['event'])) or die ("Could not send data to server\n");
		
		if ($_SERVER['REQUEST_METHOD']=='GET')
		{
			header('Content-type: application/json');
			echo '{"ping":"true"}';

		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {			
		
			echo 'Sent: '.$postParam['event'];



			socket_write($tcpSocket, $postParam['event'], strlen($postParam['event'])) or die ("Could not send data to server\n");

			//socket_write($tcpSocket, 'stop', strlen('stop')) or die ("Could not send data to server\n");
			socket_close($tcpSocket);

		} else {
        
            notAllowed();

        } 
?>