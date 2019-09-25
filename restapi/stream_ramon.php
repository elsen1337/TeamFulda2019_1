<?php

		// Adding static IP address with Port and Buffersize
		$HOST = '46.244.200.160';
		$PORT = 21567;
		$BUFSIZE = 1024;
		// Setting time for executing code. If code was not executed after certain time of seconds, the code prints an error message
		set_time_limit(0);
		
		// Creating Socket connection 
		$tcpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		$tcpSocketCon = socket_connect($tcpSocket, $HOST, $PORT) or die ("Could not connect to server\n");

		// Sending 'Speed' attribute to socket for driving the Smart Video Car forward and backward
		$speed = 30;
		$tmp = 'speed';
		$data = $tmp.$speed;
		socket_write($tcpSocket, $data, strlen($data)) or die ("Could not send speed data to server\n");
		// Closing Socket and Reconnecting Socket connection
		socket_close($tcpSocket);
		$tcpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		$tcpSocketCon = socket_connect($tcpSocket, $HOST, $PORT) or die ("Could not connect to server\n");

		//  If connection works, a Ping object will send back
		if ($_SERVER['REQUEST_METHOD']=='GET')
		{
			header('Content-type: application/json');
			echo '{"ping":"true"}';

		// Send a certain command to the server and the Raspberry Pi execute the command
		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {			
		
			echo 'Sent: '.$postParam['event'];

			socket_write($tcpSocket, $postParam['event'], strlen($postParam['event'])) or die ("Could not send data to server\n");

			socket_close($tcpSocket);

		} else {
        
            notAllowed();

        } 
?>