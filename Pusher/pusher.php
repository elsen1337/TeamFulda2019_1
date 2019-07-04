<?php
require_once ('vendor/autoload.php');

if($_GET['name'] && $_GET['role'] && $_GET['id']) {

    $chatkit = new Chatkit\Chatkit([
        'instance_locator' => 'v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df',
        'key' => '1ca63281-b9fa-4ba1-bc37-c5ac419859a6:z8GBGQwZcAOEFl35Kgd0iV6WtIW8f8K8J3tN3FPgJDg='
    ]);

    $uid = $_GET['role']."_".$_GET['id'];

    //check if user exists
    $users = $chatkit->getUsers();

//    echo $users;
//    echo sizeof($users['body']);
//    echo $users['body'][0]['id'];
//    echo $users['body'][1]['id'];
//    echo $users['body'][2]['id'];
//    echo $users['body'][3]['id'];
//    echo $users['body'][4]['id'];
//    echo $users['body'][5]['id'];

    $exists = 0;

    foreach ($users['body'] as $u){
        if($u['id'] == $uid) {
            $exists = 1;
//            break;
        }
    }
    if($exists) {
        echo $uid." was successfully authenticated for chat";
    } else {
        //create and authenticate user
        $chatkit->createUser([
            "id" => $uid,
            "name" => $_GET['name']
        ]);

        $auth = $chatkit->authenticate(['user_id' => $uid]);
        if($auth['status'] == 200){
            echo $uid." was successfully created and authenticated for chat";
        }
    }

//
//
//    if($exists == 0){
////        $chatkit->createUser([
////            "id" => $uid,
////            "name" => $_GET['name']
////        ]);
//        echo $uid." was successfully created for chat";
//    } else {
//        echo "yow!";
//    }

//    $auth = $chatkit->authenticate(['user_id' => "lel"]);
//    if($auth['status'] == 200) {
////        echo "user ".$_GET['name']."_".$_GET['role']."_".$_GET['id']." was successfully authenticated for chat";
//        echo $auth['body']['access_token']."___".$auth['body']['token_type']."___".$auth['body']['expires_in'];
//    } else {
////        $chatkit->createUser([
////            'id' => "manu"
//////            'name' => $_GET['name']
//////            'avatar_url' => 'https://placekitten.com/200/300',
//////            'custom_data' => [
//////                'my_custom_key' => 'some data'
//////            ]
////        ]);
//        echo "user ".$_GET['name']."_".$_GET['role']."_".$_GET['id']." was successfully created for chat";
//    }
}


//if($_GET['auth']){
//    // hier muss code hin, der abhängig vom in 'auth' gesetzten username prüft, ob der jeweilige user eingeloggt ist
//    // dementprechend muss der Username wieder zurückkommen bei success
//    // bei fail muss "user_not_logged_in" kommen
//    // das is die antwort, die auf der clientseite einen Aufruf der Loginseite triggert
//    echo $_GET['auth'];
//} else {
//
//    $chatkit = new Chatkit\Chatkit([
//        'instance_locator' => 'v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df',
//        'key' => '1ca63281-b9fa-4ba1-bc37-c5ac419859a6:z8GBGQwZcAOEFl35Kgd0iV6WtIW8f8K8J3tN3FPgJDg='
//    ]);
////$chatkit->createUser([
////    'id' => 'ham' ,
////    'name' => 'Hamilton Chapman',
//////        'avatar_url' => 'https://placekitten.com/200/300',
////    'custom_data' => [
////        'my_custom_key' => 'some data'
////    ]
////]);
////
////
//    $auth = $chatkit->authenticate(['user_id' => 'ham']);
//
//    if($auth['status'] == 200){
//        echo "hurra";
//    }
////
////$chatkit->createRoom([
////    'creator_id' => 'ham',
////    'name' => 'new name',
////    'user_ids' => ['bob'],
////    'private' => true,
////    'custom_data' => ['klaff' => 'bar']
////]);
//
//}

