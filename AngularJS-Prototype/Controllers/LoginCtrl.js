studyHomeApp.controller('LoginCtrl', ['$scope', '$http','$location', function($scope, $http, $location){

    var urlVar = "";

    $scope.routes = ROUTES;

    $scope.user = [{}];



    $scope.login = function (Auth) {
        //var email = document.getElementById("email").value;
        //var pwort = document.getElementById("passwort").value;

        console.log('E-Mail = ' + email + ', Passwort = ' + passwort);

        //console.log($scope.email.toLowerCase());

        if ($scope.loginForm.$valid) {
                let user = {
                    "email": $scope.email.toLowerCase(),
                    "pwort": $scope.pwort
                }

                if ($scope.loginrolle == "Lessor") {
                    urlVar = "../restapi/handler.php?objAction=lessorlogin";
                } else if ($scope.loginrolle == "Tenant") {
                    urlVar = "../restapi/handler.php?objAction=tenantlogin";
                }

                $http({

                    url: urlVar,
                    method: "POST",
                    // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                    data: user
                }).then(function mySuccess(response) {
                    console.log($scope.loginrolle);
                    $scope.putSucc = response.data;

                    if (response.data === null) {
                        $scope.log_mytext = "The password is wrong or the user doesn't exist. Try again and watch of your selected role.";
                    }

                    if (typeof (Storage) !== "undefined" && $scope.putSucc !== null) {
                        // Store
                        sessionStorage.setItem("isLoggedIn", "yes")
                        sessionStorage.setItem("vm_id", $scope.putSucc["vm_id"]);
                        sessionStorage.setItem("vname", $scope.putSucc["vname"]);
                        sessionStorage.setItem("nname", $scope.putSucc["nname"]);
                        sessionStorage.setItem("role", $scope.loginrolle);
                        // Retrieve

                        $scope.log_mytext = "";


                        console.log(response.data);
                        //console.log($scope.putSucc["vname"]);
                        console.log("status: " + response.status);
                        console.log("statusText: " + response.statusText);
                        window.location.href = "./index.html#!/homeStart";
                        window.location.reload();
                    }
                    // $location.path("index.html#!/homeStart");
                }, function myError(response) {
                    $scope.error = response.statusText;
                    console.error($scope.error);
                });
        }
        else
        {
            $scope.log_mytext = "Please, fill out the formular correktly.";
        }
    };

    $scope.goto = function ( path ) {
        $location.path( path );
    };

    function reloadpage() {
        window.location.reload();
    }
}]);

