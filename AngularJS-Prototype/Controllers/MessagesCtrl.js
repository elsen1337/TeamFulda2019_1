studyHomeApp.controller('MessagesCtrl', ['$scope', '$http' , '$location', function($scope, $http, $location){

    $scope.currentChat = null;
    $scope.currentUser = null;
    $scope.currentRooms = null;
    $scope.contacts = [];

    // $scope.test = function() {
    //     // $scope.currentUser.sendSimpleMessage({
    //     //         text: "schnellsn",
    //     //         roomId: $scope.currentUser.rooms[0].id //maybe replace 0 by getCurrentRoom()?
    //     // });
    //     // console.log($scope.currentRooms[0]);
    //     // console.log("RoomsTest");
    //     // console.log($scope.currentRooms);
    // };

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
                //Add room to contact list
                $scope.contacts.push({
                    name : room.name.split("-")[$scope.rolle === "Lessor" ? 1 : 3],
                    id : $scope.contacts.length,
                    roomID : room.id
                });
                //wenn noch keine kontakte vorhanden, dann setze currentchat
                if($scope.currentRooms && $scope.currentRooms.length === 0) {
                    $scope.currentChat = room.id;
                }
                if(!$scope.currentRooms) {
                    $scope.currentRooms = [];
                }
                $scope.currentRooms.push(room);
                $scope.updateContactList();
                //subscribe to room
                $scope.currentUser.subscribeToRoomMultipart({
                    roomId: room.id,
                    hooks: {
                        onMessage: message => {
                            let roomID = room.id;
                            let roomName = room.name;
                            let senderID = message.senderId;
                            let role = sessionStorage.getItem("role");
                            let personalID = role === "Lessor" ? sessionStorage.getItem("vm_id") : sessionStorage.getItem("m_id");
                            let myID = role + "_" + personalID;
                            let receiverID = getReceiverID(senderID, myID, roomName);
                            let receiverName = getReceiverName(senderID, myID, roomName);
                            let senderName = getSenderName(senderID, myID, roomName);

                            let messageToAdd = {
                                message : message.parts[0].payload.content,
                                senderID : senderID,
                                receiverID : receiverID,
                                senderName : senderName,
                                receiverName : receiverName,
                            };

                            // append in room
                            let found = null;
                            if($scope.currentRooms) {
                                for(let i = 0; i < $scope.currentRooms.length; i++) {
                                    if($scope.currentRooms[i].id === roomID) {
                                        found = $scope.currentRooms[i];
                                    }
                                }
                                if(found) {
                                    if(!found.messageLog) {
                                        found.messageLog = [];
                                    }
                                    found.messageLog.push(messageToAdd);
                                }
                            }

                            //wenn das aktuelle chatfenster der jetzige raum ist, dann updaten!
                            if($scope.currentChat && found && $scope.currentChat === found.id) {
                                $scope.updateChatLog(found.id);
                            }

                            // hier noch logik für neue message meldung machen
                            // hierfür am besten ein flag in message.customData oder so setzen
                            document.getElementById("newMessageIndicator").innerHTML = "Messages (New!)";
                        }
                    }
                }).catch(error => {
                    console.error("error:", error);
                });
            }
        })
        .then(currentUser => {
            $scope.currentUser = currentUser;

            //add contacts
            if(currentUser.rooms) {
                for(let i = 0; i < currentUser.rooms.length; i++) {
                    $scope.contacts.push({
                        name : currentUser.rooms[i].name.split("-")[sessionStorage.getItem("role") === "Lessor" ? 1 : 3],
                        id : i,
                        roomID : currentUser.rooms[i].id
                    });
                    currentUser.subscribeToRoomMultipart({
                        roomId: currentUser.rooms[i].id,
                        hooks: {
                            onMessage: message => {
                                // console.log("Received message:", message)


                                let roomID = currentUser.rooms[i].id;
                                let roomName = currentUser.rooms[i].name;
                                let senderID = message.senderId;
                                let role = sessionStorage.getItem("role");
                                let personalID = role === "Lessor" ? sessionStorage.getItem("vm_id") : sessionStorage.getItem("m_id");
                                let myID = role + "_" + personalID;
                                let receiverID = getReceiverID(senderID, myID, roomName);
                                let receiverName = getReceiverName(senderID, myID, roomName);
                                let senderName = getSenderName(senderID, myID, roomName);

                                // console.log(message);
                                // console.log(roomID);
                                // console.log(message.parts[0].payload.content);
                                // console.log(message.senderId);
                                // console.log(receiverID);
                                // console.log(receiverName);
                                // console.log(senderID);
                                // console.log(senderName);

                                let messageToAdd = {
                                    message : message.parts[0].payload.content,
                                    senderID : senderID,
                                    receiverID : receiverID,
                                    senderName : senderName,
                                    receiverName : receiverName,
                                };

                                // append in room
                                let found = null;
                                if($scope.currentRooms) {
                                    for(let i = 0; i < $scope.currentRooms.length; i++) {
                                        if($scope.currentRooms[i].id === roomID) {
                                            found = $scope.currentRooms[i];
                                        }
                                    }
                                    if(found) {
                                        if(!found.messageLog) {
                                            found.messageLog = [];
                                        }
                                        found.messageLog.push(messageToAdd);
                                    }
                                }

                                //wenn das aktuelle chatfenster der jetzige raum ist, dann updaten!
                                if($scope.currentChat && found && $scope.currentChat === found.id) {
                                    $scope.updateChatLog(found.id);
                                }

                                // hier noch logik für neue message meldung machen
                                // hierfür am besten ein flag in message.customData oder so setzen
                                document.getElementById("newMessageIndicator").innerHTML = "Messages (New!)";
                            }
                        }
                    }).catch(error => {
                        console.error("error:", error);
                    });
                }
                $scope.currentRooms = currentUser.rooms;
            }
            // alert("Length: " + currentUser.rooms.length);
            console.log("Rewms:");
            console.log($scope.contacts);
            $scope.updateContactList();
            if($scope.currentRooms && $scope.currentRooms.length > 0) {
                //set default chat
                $scope.currentChat = $scope.currentRooms[0].id;
                //invoke default chat here
            }


            switch($scope.rolle) {
                case "Tenant":
                    //check if contact lessor was clicked
                    // alert($location.$$path.includes("?id="));
                    if($location.$$path.includes("?id=")){
                        //check if room exists
                        if(currentUser.rooms) {
                            let exists = false;
                            let roomNameCheck = "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + getLessorName($location.$$path);
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
                                    name : "Tenant_" + sessionStorage.getItem("m_id") + "-" + sessionStorage.getItem("nname") + "-Lessor_" + getLessorID($location.$$path) + "-" + getLessorName($location.$$path),
                                    private : true,
                                    addUserIds : ["Lessor_" + getLessorID($location.$$path)]
                                }).then(room => {
                                    console.log(`Created room called ${room.name}`);
                                    // code zum room adden in scope.currentrooms
                                    // danach updatecontacts machen
                                    if(!$scope.currentRooms) {
                                        $scope.currentRooms = [];
                                    }
                                    // push as well into contacts
                                    // this is not needed because of the onAddedToRoomHook!
                                    // $scope.contacts.push({
                                    //     name : room.name.split("-")[$scope.rolle === "Lessor" ? 1 : 3],
                                    //     id : $scope.contacts.length,
                                    //     roomID : room.id
                                    // });
                                    $scope.currentRooms.push(room);
                                    // this is as well not needed for it will be updated in the hook
                                    // $scope.updateContactList();
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

            // add send callback on sendbutton
            const form = document.getElementById("message-form");
            form.addEventListener("submit", e => {
                e.preventDefault();
                const input = document.getElementById("m_area_id");
                $scope.currentUser.sendSimpleMessage({
                    text: input.value,
                    roomId: $scope.currentChat //maybe replace 0 by getCurrentRoom()?
                });
                input.value = "";
            });
        }).catch(error => {
            alert("err");
            console.error(error);
            //API Fallback with Polling
    });

    $scope.updateContactList = function() {
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
        for(let i = 0; i < $scope.contacts.length; i++) {
            let contact = document.createElement("md-list-item");
            contact.setAttribute("class", "noright");
            let p = document.createElement("p");
            contact.appendChild(p);
            let mdb = document.createElement("md-button");
            mdb.setAttribute("class", "contactButton");
            mdb.innerHTML = $scope.contacts[i].name;
            mdb.addEventListener("click", (function () {
                let roomID = $scope.contacts[i].roomID;
                return function(){
                    $scope.updateChatLog(roomID);
                }
            })());
            p.appendChild(mdb);

            list.appendChild(contact);
        }
    }

    $scope.updateChatLog = function(roomID) {
        // if($scope.currentChat === null || ($scope.currentChat && !(roomID === $scope.currentChat))) {
            if($scope.currentRooms && $scope.currentRooms.length > 0) {
                let list = document.getElementById("chatmessagesList");
                let deleteList = [];
                // items to delete
                for (let i = 0; i < list.childNodes.length; i++) {
                    deleteList.push(list.childNodes[i]);
                }
                // delete items
                for (let i = 0; i < deleteList.length; i++) {
                    list.removeChild(deleteList[i]);
                }
                // get room
                let room = null;
                for (let i = 0; i < $scope.currentRooms.length; i++) {
                    if ($scope.currentRooms[i].id === roomID) {
                        room = $scope.currentRooms[i];
                    }
                }
                // append messages
                if(room) {
                    if(room.messageLog && room.messageLog.length > 0) {
                        for(let i = 0; i < room.messageLog.length; i++) {

                            let messageLI = document.createElement("md-list-item");
                            messageLI.setAttribute("class", "noright");
                            let messageDIV = document.createElement("div");
                            let role = sessionStorage.getItem("role");
                            let personalID = role === "Tenant" ? sessionStorage.getItem("m_id") : sessionStorage.getItem("vm_id");
                            let myID = role + "_" + personalID;
                            if(myID === room.messageLog[i].senderID) {
                                //I am sender
                                messageDIV.setAttribute("class", "chatMessageContainer");
                            } else {
                                //I am receiver
                                messageDIV.setAttribute("class", "chatMessageContainer darkContainer");
                            }
                            let pre = document.createElement("pre");
                            pre.innerHTML = room.messageLog[i].senderName + ":\n" + room.messageLog[i].message;

                            list.appendChild(messageLI);
                            messageLI.appendChild(messageDIV);
                            messageDIV.appendChild(pre);
                        }
                    }
                }
                $scope.currentChat = roomID;
            }
        // }
    }
}]);

function getCurrentUser() {
    let role = sessionStorage.getItem("role");
    let id = role === "Lessor" ? sessionStorage.getItem("vm_id") : sessionStorage.getItem("m_id");
    return role + "_" + id;
}

function getReceiverID(senderID, myID, roomName) {
    if(senderID === myID) {
        if(myID.includes("Tenant")) {
            return roomName.split("-")[2];
        }
    }
    return roomName.split("-")[0];
}

function getReceiverName(senderID, myID, roomName) {
    if(senderID === myID) {
        if(myID.includes("Tenant")) {
            return roomName.split("-")[3];
        }
    }
    return roomName.split("-")[1];
}

function getSenderID(senderID, myID, roomName) {
    if(senderID === myID) {
        if(myID.includes("Tenant")) {
            return roomName.split("-")[0];
        }
    }
    return roomName.split('-')[2];
}

function getSenderName(senderID, myID, roomName) {
    if(senderID === myID) {
        if(myID.includes("Tenant")) {
            return roomName.split("-")[1];
        }
    }
    return roomName.split("-")[3];
}

function getLessorID(path) {
    return path.match("[0-9]+");
}

function getLessorName(path) {
    return path.split("=")[2];
}