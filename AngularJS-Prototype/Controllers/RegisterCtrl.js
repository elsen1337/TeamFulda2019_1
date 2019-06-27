studyHomeApp.controller('RegisterCtrl', ['$scope', '$http', function($scope, $http){

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


        $scope.user.account = document.getElementById("acc-type").value;
        $scope.user.anrede = document.getElementById("p-title").value;
        $scope.user.vname = document.getElementById("prename").value;
        $scope.user.nname = document.getElementById("lastname").value;
        $scope.user.pwort = document.getElementById("password").value;
        $scope.user.email = document.getElementById("email").value;

        console.log($scope.user);

        urlVar = "";

        if ($scope.user.account == "lessor")
        {
            urlVar = "../restapi/handler.php?objAction=lessoraccount"
        }
        else if ($scope.user.account == "tenant")
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

    $scope.submitSearchForm = function() {
        console.log($scope.user);
        let submitData = convertRegisterFormData($scope.user);
        console.log(submitData);
        $http({
            url : "../restapi/handler.php?objAction=lessoraccount",
            method: "POST",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data : submitData
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

