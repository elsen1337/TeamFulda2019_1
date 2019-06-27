studyHomeApp.controller('RegisterCtrl', ['$scope', '$http', function($scope, $http){

    $scope.rollen = [
        "Lessor",
        "Tenant"
    ];

    $scope.anreden = [
        "Ms.",
        "Mr."
    ];

    $scope.user = [{}];

    $scope.register = function(){

        $scope.user = {

            "account" : "",
            "anrede" : "",
            "vname" : "",
            "nname" : "",
            "pwort" : "",
            "email" : "",
            "profil" : ""
        };


        $scope.user.account = $scope.rolle;
        $scope.user.vname = document.getElementById("prename").value;
        $scope.user.nname = document.getElementById("lastname").value;
        $scope.user.pwort = document.getElementById("password").value;
        $scope.user.email = document.getElementById("email").value;

        console.log($scope.user);

        urlVar = "";

        if ($scope.anrede == "Mr.")
        {
            $scope.user.anrede = "M";

        }
        else if ($scope.anrede == "Ms.")
        {
            $scope.user.anrede = "F";
        }

        if ($scope.user.account == "Lessor")
        {
            urlVar = "../restapi/handler.php?objAction=lessoraccount"
        }
        else if ($scope.user.account == "Tenant")
        {
            urlVar = "../restapi/handler.php?objAction=tenantaccount"
        }

        console.log($scope.user.account);

        $http({
            url : urlVar,
            method: "POST",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data : $scope.user
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);
        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
    };
}]);

