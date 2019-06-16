studyHomeApp.controller('RegisterCtrl', ['$scope', function($scope){
    $scope.register = function(){
        var prename = document.getElementById("prename").value;
        var lastname = document.getElementById("lastname").value;
        var password = document.getElementById("password").value;
        var repeatPassword = document.getElementById("repeatPassword").value;
        var email = document.getElementById("email").value;
        var birthDate = document.getElementById("birthDate").value;
        console.log('Prename = ' + prename
            + ', Lastname = ' + lastname
            + ', Passwort = ' + password
            + ', Repeat Passwort = ' + repeatPassword
            + ', E-Mail = ' + email
            + ', Birth Date = ' + birthDate);
    };

}]);
