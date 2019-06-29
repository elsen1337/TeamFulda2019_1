studyHomeApp.controller('MessagesCtrl', ['$scope', '$http' , '$location', function($scope, $http, $location){

    // $scope.currentUser = null;


    // const tokenProvider = new Chatkit.TokenProvider({
    //     url: "https://us1.pusherplatform.io/services/chatkit_token_provider/v1/2dfeb892-34c4-4df4-9eb4-8bb4502e03df/token"
    // });

    // const chatManager = new Chatkit.ChatManager({
    //     instanceLocator: "v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df",
    //     userId: "elsen",
    //     // tokenProvider: tokenProvider
    // });
    //
    // chatManager
    //     .connect()
    //     .then(currentUser => {
    //         // $scope.currentUser = currentUser;
    //
    //         const form = document.getElementById("message-form");
    //         form.addEventListener("submit", e => {
    //             e.preventDefault();
    //             const input = document.getElementById("m_area_id");
    //             currentUser.sendSimpleMessage({
    //                 text: input.value,
    //                 roomId: currentUser.rooms[0].id
    //             });
    //             input.value = "";
    //         });
    //
    //         currentUser.subscribeToRoomMultipart({
    //             roomId: currentUser.rooms[0].id,
    //             hooks: {
    //                 onMessage: message => {
    //                     console.log("Received message:", message)
    //                 }
    //             }
    //         }).catch(error => {
    //             console.error("error:", error);
    //             //API Fallback with Polling;
    //         });
    //     });
    //
    // $scope.changeUser = function() {
    //     chatManager.changeUser(getCurrentUser());
    // }

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if (sessionStorage.getItem("role") === "Tenant")
    {
        document.getElementById("lessorSidenavCont").style.display="none";
    }
    else if (sessionStorage.getItem("role") === "Lessor") {
        document.getElementById("tenantSidenavCont").style.display="none";
    }

    $scope.messageLog = [];
    $scope.contacts = [{ name: "test"}];
    $scope.currentChatLog = "";

    // username abfrage
    $http({
        method : "GET",
        url : "../Pusher/pusher.php?auth=" + getCurrentUser() //replace code in getCurrentUser() with dynamic data i.e. set username in window.username when login conirmed
    }).then(function mySuccess(response) {
        $scope.userName = response.data;
        console.log(response.data);
        console.log("status: " + response.status);
        console.log("statusText: " + response.statusText);

        if(!$scope.userName
            || $scope.userName === undefined
            || $scope.userName === 'undefined'
            || $scope.userName === "user_not_logged_in") {
            console.log("User could not be authenticated!");
            // $location.path("login"); // go to login
        }

    }, function myError(response) {
        console.error(response);
    });



    //statt  dieser function am ende einfach im callback von der username abfrage arbeiten
    $scope.connectToChatkit = function(){


        if($scope.chatManager){
            $scope.chatManager.disconnect();
            // $scope.chatManager.user_id = getCurrentUser();
        }


        const tokenProvider = new Chatkit.TokenProvider({
            url: "https://us1.pusherplatform.io/services/chatkit_token_provider/v1/2dfeb892-34c4-4df4-9eb4-8bb4502e03df/token"
        });


        $scope.chatManager = new Chatkit.ChatManager({
            instanceLocator: "v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df",
            userId: getCurrentUser(),
            tokenProvider: tokenProvider
        });

        $scope.chatManager
            .connect()
            .then(currentUser => {

                // console.log(currentUser.rooms.length);
                console.log(currentUser.rooms);
                for(let i = 0; i < currentUser.rooms.length; i++) {
                    // alert(currentUser.rooms[i].userIds[0]);
                    console.log("room")
                    console.log(currentUser.rooms[0]);
                    // if($scope.contacts[0]){
                    //     if(!$scope.contacts[0].name){
                    //         $scope.contacts = [];
                    //     }
                    // }
                    $scope.contacts[i] = { name: 'test2'}//getContact(currentUser.rooms[i], getCurrentUser());  // replace getCurrentUser() later with dynamic data like window.username
                    console.log("contacts");
                    console.log($scope.contacts);
                }
                $scope.currentChatLog = "Test";


                const form = document.getElementById("message-form");
                form.addEventListener("submit", e => {
                    e.preventDefault();
                    const input = document.getElementById("m_area_id");
                    currentUser.sendSimpleMessage({
                        text: input.value,
                        roomId: currentUser.rooms[0].id //maybe replace 0 by getCurrentRoom()?
                    });
                    input.value = "";
                });


                currentUser.subscribeToRoomMultipart({
                    roomId: currentUser.rooms[0].id,
                    hooks: {
                        onMessage: message => {
                            console.log("Received message:", message)
                            if(message.senderId){
                                $scope.messageLog.push({
                                    senderID : message.senderId,
                                    receiverID : getReceiver(),
                                    text : message.parts[0].payload.content
                                });
                                console.log($scope.messageLog);
                            }
                        }
                    }
                }).catch(error => {
                    console.error("error:", error);
                    //API Fallback with Polling;
                });
            });
    };

    $scope.invokeHistory = function() {

    }

    // $scope.submitMessage = function() {
    //     // alert();
    //     const input = document.getElementById("m_area_id");
    //     $scope.currentUser.sendSimpleMessage({
    //         text: input.value,
    //         roomId: currentUser.rooms[0].id
    //     });
    //     input.value = "";
    // };

}]);

function getCurrentUser() {
    //replace code in getCurrentUser() with dynamic data i.e. set username in window.username when login conirmed
    if(document.getElementById("currUser").value) {
        return document.getElementById("currUser").value;
    }
}

function getReceiver() {

}

function getContact(room, username) {
    // alert(room.userIds[0]);
    let result = {
        // name : room.userIds[0] === username ? room.userIds[1] : room.userIds[0]
        name : "test"
    };
    console.log("Res");
    console.log(result);
    return result;
}