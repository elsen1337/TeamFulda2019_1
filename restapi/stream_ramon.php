<?php

		function ping_url($ipaddress)
		{	
			$ci = curl_init();
			curl_setopt($ci, CURLOPT_URL, $ipaddress.":8080");
			// Set Return Transfer 1
			curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
			// Set timeout
			curl_setopt($ci, CURLOPT_TIMEOUT, 1);
			// Execute URL
			$http_res = curl_exec($ci);
			// Trim whitespaces and remove PHP and HTML tags of string
			$http_res = trim(strip_tags($http_res));
			// Get status info, if url is reachable
			$http_status = curl_getinfo($ci, CURLINFO_HTTP_CODE);
			
			if ($http_status == "200" || $http_status == "302")
			{	
				return true;
			}
			else
			{
				return false;
			}
			// Closing connection
			curl_close($ci);
		}
		

		//  If connection works, a Ping object will send back
		if ($_SERVER['REQUEST_METHOD']=='GET')
		{
			//$ping = ping_url($objkey);
			$ping = ping_url($objkey);

			header('Content-type: application/json');
			echo json_encode($ping);

		// Send a certain command to the server and the Raspberry Pi execute the command
		} elseif ($_SERVER['REQUEST_METHOD']=='PUT') {	

			
			// Adding static IP address with Port and Buffersize
			$HOST = $postParam['host'];
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
		
			echo 'Sent: '.$postParam['event'];

			socket_write($tcpSocket, $postParam['event'], strlen($postParam['event'])) or die ("Could not send data to server\n");

			socket_close($tcpSocket);
		

		} else {
        
            notAllowed();

        } 
?>