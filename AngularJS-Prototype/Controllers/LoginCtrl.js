studyHomeApp.controller('LoginCtrl', ['$scope', '$http','$location', function($scope, $http, $location){

    var urlVar = "";

    $scope.routes = ROUTES;

    $scope.user = [{}];

    // roleId can either be vm_id for lessor or m_id for tenant
    var roleId;

	// Executing function to Login
    $scope.login = function (Auth) {
        //var email = document.getElementById("email").value;
        //var pwort = document.getElementById("passwort").value;

        console.log('E-Mail = ' + email + ', Passwort = ' + passwort);

		// Check if login entries are valid
        if ($scope.loginForm.$valid) {
			
				// Creating user object
                let user = {
                    "email": $scope.email.toLowerCase(),
                    "pwort": $scope.pwort
                }

				// Check, if "Lessor" or "Tentant" had been chosen. Appropriate variable for URL and ID will be created.
                if ($scope.loginrolle === "Lessor") {
                    urlVar = "../restapi/handler.php?objAction=lessorlogin";
                    roleId = "vm_id"
                } else if ($scope.loginrolle === "Tenant") {
                    urlVar = "../restapi/handler.php?objAction=tenantlogin";
                    roleId = "m_id"
                }

				// Sending data via AJAX
                $http({
                    url: urlVar,
                    method: "POST",
                    // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                    data: user
                }).then(function mySuccess(response) {
                    console.log($scope.loginrolle);
                    $scope.putSucc = response.data;

					// If receiving JSON object is empty, print this error message
                    if (response.data === null) {
                        $scope.log_mytext = "The password is wrong or the user doesn't exist. Try again and watch of your selected role.";
                    }

					// If no session exists, creating new session
                    if (typeof (Storage) !== "undefined" && $scope.putSucc !== null) {
                        // Store
                        sessionStorage.setItem("isLoggedIn", "yes");
                        sessionStorage.setItem(roleId, $scope.putSucc[roleId]);
                        sessionStorage.setItem("vname", $scope.putSucc["vname"]);
                        sessionStorage.setItem("nname", $scope.putSucc["nname"]);
                        sessionStorage.setItem("email", $scope.putSucc["email"]);
                        sessionStorage.setItem("role", $scope.loginrolle);

						// If you are lessor or tenant, an appropriate session object will be created.
                        if ($scope.loginrolle === "Lessor") {
                            sessionStorage.setItem("vm_id", $scope.putSucc["vm_id"]);
                        }
                        else {
                            sessionStorage.setItem("m_id", $scope.putSucc["m_id"]);
                        }
                        // Retrieve

                        $scope.log_mytext = "";


                        console.log(response.data);
                        //console.log($scope.putSucc["vname"]);
                        console.log("status: " + response.status);
                        console.log("statusText: " + response.statusText);

                        // alert(getChatKitAuthQS());

                        $http({
                            url: "../Pusher/pusher.php" + getChatKitAuthQS(),
                            method: "GET"
                        }).then(function mySuccess(response) {
                            console.log(response.data);
                            // alert(response.data);

                            window.location.href = "./index.html#!/homeStart";
                            window.location.reload();

                        }, function myError(response) {
                            console.log(response);

                            window.location.href = "./index.html#!/homeStart";
                            window.location.reload();
                        });

                        // window.location.href = "./index.html#!/homeStart";
                        // window.location.reload();
                    }



                    // $location.path("index.html#!/homeStart");
                }, function myError(response) {
                    console.log(response);
                });
        }
        else
        {
            $scope.log_mytext = "Please, fill out the formular correctly.";
        }
    };

    $scope.goto = function ( path ) {
        $location.path( path );
    };

    function reloadpage() {
        window.location.reload();
    }
}]);

function getChatKitAuthQS() {
    let result = "?";

    result += "name=" + sessionStorage.getItem("nname");
    result += "&role=" + sessionStorage.getItem("role");
    result += "&id=";
    result += sessionStorage.getItem("role") === "Lessor" ? sessionStorage.getItem("vm_id") : sessionStorage.getItem("m_id");

    return result;
}