studyHomeApp.controller('LoginCtrl', ['$scope', '$location', function($scope, $location){
    $scope.routes = ROUTES;

    $scope.login = function () {
        var email = document.getElementById("email").value;
        var passwort = document.getElementById("passwort").value;
        console.log('E-Mail = ' + email + ', Passwort = ' + passwort);
    };

    $scope.goto = function ( path ) {
        $location.path( path );
    };
}]);

