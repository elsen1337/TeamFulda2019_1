studyHomeApp.controller('LoginCtrl', ['$scope', '$http','$location', function($scope, $http, $location){
    $scope.routes = ROUTES;

    $scope.user = [{}];

    $scope.login = function (Auth) {
        var email = document.getElementById("email").value;
        var passwort = document.getElementById("passwort").value;
        console.log('E-Mail = ' + email + ', Passwort = ' + passwort);

        $scope.user.email = email;
        $scope.user.passwort = passwort;

        console.log

        $http({
            url : "../restapi/handler.php?objAction=lessorlogin",
            method: "POST",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data : $scope.user
        }).then(function mySuccess(response) {
            console.log("Fischkopf");
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);
        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });

        $location.path('/renting');
    };

    $scope.goto = function ( path ) {
        $location.path( path );
    };
}]);
