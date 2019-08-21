studyHomeApp.controller('ViewingAppointmentsCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.slots = [{}];

    $scope.viewAppointments = function () {
        id = sessionStorage.getItem("vm_id");
            $http({
                url: `../restapi/handler.php?objAction=estatemeeting&objKey=${id}`,
                //url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                method: "GET",
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            }).then(function mySuccess(response) {
                $scope.putSucc = response.data;
                slots = $scope.putSucc;
                console.log(slots);
                $scope.slots = slots;
            }, function myError(response) {
                console.log(response);
            });
    };
}]);