studyHomeApp.controller('MessagesCtrl', ['$scope', '$http' , '$location', function($scope, $http, $location){

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if ($scope.rolle === "Tenant")
    {
        let element = document.getElementById("lessorSidenavCont");
        element.parentNode.removeChild(element);
    }
    else
    {

        let element = document.getElementById("tenantSidenavCont");
        element.parentNode.removeChild(element);
    }
    $scope.messageLog = [];
    $scope.contacts = [];
    $scope.currentChatLog = [];

    if($scope.chatManager){
        $scope.chatManager.disconnect();
        // $scope.chatManager.user_id = getCurrentUser();
    }


    const tokenProvider = new Chatkit.TokenProvider({
        url: "https://us1.pusherplatform.io/services/chatkit_token_provider/v1/2dfeb892-34c4-4df4-9eb4-8bb4502e03df/token"
    });

    // alert(getCurrentUser());
    // alert(getLessorID($location.$$path));
    $scope.chatManager = new Chatkit.ChatManager({
        instanceLocator: "v1:us1:2dfeb892-34c4-4df4-9eb4-8bb4502e03df",
        userId: getCurrentUser(),
        tokenProvider: tokenProvider
    });

    $scope.chatManager
        .connect({
            onAddedToRoom: room => {
                console.log(`Added to room ${room.name}`);
                //reinvoke
                $scope.reconnect();
                // $scope.chatManager.disconnect();
                //Add room to contact list
                // $scope.contacts.push({
                //     name : room.name.split("-")[$scope.rolle === "Lessor" ? 1 : 3],
                //     id : $scope.contacts.length
                // });
                // updateContactList($scope.contacts);
            }
        })
        .then(currentUser => {
            switch($scope.rolle) {
                case "Tenant":
                    //check if contact lessor was clicked
                    // alert($location.$$path.includes("?id="));
                    if($location.$$path.includes("?id=")){
                        //check if room exists
                        if(currentUser.rooms) {
                            let exists = false;
                            let roomNameCheck = "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + "replace";
                            // alert(roomNameCheck);
                            for(let i = 0; i < currentUser.rooms.length; i++) {
                                if(roomNameCheck === currentUser.rooms[i].name) {
                                    exists = true;
                                }
                            }
                            if(exists){
                               //dont create a new room
                               //alert("dont");
                            } else {
                                //create a new room
                                // alert("create");
                                currentUser.createRoom({
                                    name : "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + "replace",
                                    private : true,
                                    addUserIds : ["Lessor_" + getLessorID($location.$$path)]
                                }).then(room => {
                                    console.log(`Created room called ${room.name}`);
                                    //reinvoke
                                    $scope.reconnect();
                                }).catch(err => {
                                    console.log(`Error creating room ${err}`);
                                })
                            }
                        }
                    }
                    break;
                case "Lessor":
                    break;
            }
            //add contacts
            if(currentUser.rooms) {
                for(let i = 0; i < currentUser.rooms.length; i++) {
                    $scope.contacts.push({
                        name : currentUser.rooms[i].name.split("-")[sessionStorage.getItem("role") === "Lessor" ? 1 : 3],
                        id : i
                    });
                }
            }
            // alert("Length: " + currentUser.rooms.length);
            console.log("Rewms:");
            console.log($scope.contacts);
            updateContactList($scope.contacts);


            // // console.log(currentUser.rooms.length);
            // console.log(currentUser.rooms);
            // // $scope.demTest = [{text: "hey"}, {text: "jo"}];
            // for(let i = 0; i < currentUser.rooms.length; i++) {
            //     // alert(currentUser.rooms[i].userIds[0]);
            //     console.log("room")
            //     console.log(currentUser.rooms[0].name);
            //     // if($scope.contacts[0]){
            //     //     if(!$scope.contacts[0].name){
            //     //         $scope.contacts = [];
            //     //     }
            //     // }
            //     $scope.contacts[i] = { name: 'test2'}//getContact(currentUser.rooms[i], getCurrentUser());  // replace getCurrentUser() later with dynamic data like window.username
            //     console.log("contacts");
            //     console.log($scope.contacts);
            // }
            // $scope.currentChatLog = "Test";
            //
            //
            // const form = document.getElementById("message-form");
            // form.addEventListener("submit", e => {
            //     e.preventDefault();
            //     const input = document.getElementById("m_area_id");
            //     currentUser.sendSimpleMessage({
            //         text: input.value,
            //         roomId: currentUser.rooms[0].id //maybe replace 0 by getCurrentRoom()?
            //     });
            //     input.value = "";
            // });
            //
            //
            // currentUser.subscribeToRoomMultipart({
            //     roomId: currentUser.rooms[0].id,
            //     hooks: {
            //         onMessage: message => {
            //             console.log("Received message:", message)
            //             if(message.senderId){
            //                 $scope.messageLog.push({
            //                     senderID : message.senderId,
            //                     receiverID : getReceiver(),
            //                     text : message.parts[0].payload.content
            //                 });
            //                 console.log($scope.messageLog);
            //             }
            //         }
            //     }
            // }).catch(error => {
            //     console.error("error:", error);
            // });
        }).catch(error => {
            alert("err");
            console.error(error);
            //API Fallback with Polling
    });

    $scope.reconnect = function(){
        $scope.contacts = [];
        $scope.chatManager.disconnect();
        $scope.chatManager
            .connect({
                onAddedToRoom: room => {
                    console.log(`Added to room ${room.name}`);
                    //reinvoke
                    $scope.reconnect();
                    // $scope.chatManager.disconnect();
                    //Add room to contact list
                    // $scope.contacts.push({
                    //     name : room.name.split("-")[$scope.rolle === "Lessor" ? 1 : 3],
                    //     id : $scope.contacts.length
                    // });
                    // updateContactList($scope.contacts);
                }
            })
            .then(currentUser => {
                switch($scope.rolle) {
                    case "Tenant":
                        //check if contact lessor was clicked
                        // alert($location.$$path.includes("?id="));
                        // if($location.$$path.includes("?id=")){
                        //     //check if room exists
                        //     if(currentUser.rooms) {
                        //         let exists = false;
                        //         let roomNameCheck = "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + "replace";
                        //         // alert(roomNameCheck);
                        //         for(let i = 0; i < currentUser.rooms.length; i++) {
                        //             if(roomNameCheck === currentUser.rooms[i].name) {
                        //                 exists = true;
                        //             }
                        //         }
                        //         if(exists){
                        //             //dont create a new room
                        //             //alert("dont");
                        //         } else {
                        //             //create a new room
                        //             // alert("create");
                        //             currentUser.createRoom({
                        //                 name : "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + "replace",
                        //                 private : true,
                        //                 addUserIds : ["Lessor_" + getLessorID($location.$$path)]
                        //             }).then(room => {
                        //                 console.log(`Created room called ${room.name}`);
                        //                 //reinvoke
                        //                 $scope.reconnect()
                        //             }).catch(err => {
                        //                 console.log(`Error creating room ${err}`);
                        //             })
                        //         }
                        //     }
                        // }
                        break;
                    case "Lessor":
                        break;
                }
                //add contacts
                if(currentUser.rooms) {
                    for(let i = 0; i < currentUser.rooms.length; i++) {
                        $scope.contacts.push({
                            name : currentUser.rooms[i].name.split("-")[sessionStorage.getItem("role") === "Lessor" ? 1 : 3],
                            id : i
                        });
                    }
                }

                // alert("Length: " + currentUser.rooms.length);
                console.log("Rewms:");
                console.log($scope.contacts);
                updateContactList($scope.contacts);
            }).catch(error => {
                alert("err");
                console.error(error);
                //API Fallback with Polling
            });
    }

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
    let role = sessionStorage.getItem("role");
    let id = role === "Lessor" ? sessionStorage.getItem("vm_id") : sessionStorage.getItem("m_id");
    return role + "_" + id;
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

function getLessorID(path) {
    return path.match("[0-9]+");
}

function updateContactList(contacts) {
    let list = document.getElementById("contactList");
    let deleteList = [];
    //get items to delete
    for(let i = 0; i < list.childNodes.length; i++) {
        deleteList.push(list.childNodes[i]);
    }
    //delete items
    for(let i = 0; i < deleteList.length; i++) {
        list.removeChild(deleteList[i]);
    }
    for(let i = 0; i < contacts.length; i++) {
        let contact = document.createElement("md-list-item");
        contact.setAttribute("class", "noright");
        let p = document.createElement("p");
        contact.appendChild(p);
        let mdb = document.createElement("md-button");
        mdb.setAttribute("class", "contactButton");
        mdb.innerHTML = contacts[i].name;
        mdb.addEventListener("click", (function () {
            let functionID = i;
            // alert(functionID);
            return function(){
                updateChatLog(functionID);
            }
        })());
        p.appendChild(mdb);

        list.appendChild(contact);
    }
}

function updateChatLog(id) {
    alert(id);
}