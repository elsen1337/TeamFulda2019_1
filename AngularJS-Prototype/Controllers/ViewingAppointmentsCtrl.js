studyHomeApp.controller('ViewingAppointmentsCtrl', ['$scope', '$http', function($scope, $http) {


    $scope.viewAppointments = function () {
        id = sessionStorage.getItem("vm_id")
        console.log('tetetetetet');

            $http({
                url: `../restapi/handler.php?objAction=estatemeeting&objKey=${1}`,
                method: "GET",
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            }).then(function mySuccess(response) {
                $scope.putSucc = response.data;
                console.log($scope.putSucc)
            }, function myError(response) {
                console.log(response);
            });
    };
}]);