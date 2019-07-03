<?php
require_once ('vendor/autoload.php');

if($_GET['auth']){
    // hier muss code hin, der abhängig vom in 'auth' gesetzten username prüft, ob der jeweilige user eingeloggt ist
    // dementprechend muss der Username wieder zurückkommen bei success
    // bei fail muss "user_not_logged_in" kommen
    // das is die antwort, die auf der clientseite einen Aufruf der Loginseite triggert
    echo $_GET['auth'];
} else {

    $chatkit = new Chatkit\Chatkit([
        'instance_locator' => 'v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df',
        'key' => '1ca63281-b9fa-4ba1-bc37-c5ac419859a6:z8GBGQwZcAOEFl35Kgd0iV6WtIW8f8K8J3tN3FPgJDg='
    ]);
//$chatkit->createUser([
//    'id' => 'ham' ,
//    'name' => 'Hamilton Chapman',
////        'avatar_url' => 'https://placekitten.com/200/300',
//    'custom_data' => [
//        'my_custom_key' => 'some data'
//    ]
//]);
//
//
    $auth = $chatkit->authenticate(['user_id' => 'ham']);

    if($auth['status'] == 200){
        echo "hurra";
    }
//
//$chatkit->createRoom([
//    'creator_id' => 'ham',
//    'name' => 'new name',
//    'user_ids' => ['bob'],
//    'private' => true,
//    'custom_data' => ['klaff' => 'bar']
//]);

}

