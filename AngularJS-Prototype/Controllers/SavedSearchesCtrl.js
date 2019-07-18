studyHomeApp.controller('SavedSearchesCtrl', ['$scope', '$http', '$location', function($scope, $http, $location) {

    let M_ID = sessionStorage.getItem('m_id');
    let url = `../restapi/handler.php?objAction=estatesearchsession&objKey=${M_ID}`;

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

    window.setSearchKey = (item) =>
    {
        window.startSearch = item.searchfield;
        window.min_meter = item.min_meter;
        window.max_meter = item.max_meter;
        window.min_min = item.min_min;
        window.max_min = item.max_min;
        window.price = item.price;
        $location.path("home");
        return true;
    };

}]);