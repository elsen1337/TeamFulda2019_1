studyHomeApp.controller('SavedSearchesCtrl', ['$scope', '$http', function($scope, $http) {

    let url = '';

    $http.get(url,
        {
            transformRequest: angular.identity,
            headers: {
                'Content-Type': undefined
            }
        })
        .then((response) =>
            {
                $scope.userData = response.data;
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                $scope.item.searchfield = response.data.searchfield;
                $scope.item.min_meter = response.data.min_meter;
                $scope.item.max_meter = response.data.max_meter;
                $scope.item.min_min = response.data.min_min;
                $scope.item.max_min = response.data.max_meter;
                $scope.item.price = response.data.price;
            },
            (err) => {
                console.log(err);
            });

    window.setSearchKey = () =>
    {
        window.startSearch = $scope.item.searchfield;
        window.min_meter = $scope.item.min_meter;
        window.max_meter = $scope.item.max_meter;
        window.min_min = $scope.item.min_min;
        window.max_min = $scope.item.max_min;
        window.price = $scope.item.price;

        return true;
    };

}]);