studyHomeApp.controller('HomeStartCtrl', ['$scope', '$http', '$location', function($scope, $http, $location){
    window.setSearchKey = () =>
    {
        let formData = new FormData();
        formData.append("appsearch[fulltext]", $scope.startSearch);

        $http({
            url : "../restapi/handler.php?objAction=estatesearch",
            method: "PUT",
            headers : {'Content-Type': undefined},
            data : result
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);

        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
        return true;
    };
}]);

