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
                $scope.searchData = response.data;
                $scope.searchItems = [{}];
                if($scope.searchData.length === 0) {
                    document.getElementById("searchList").style.display="none";
                }
                for(let i = 0; i < $scope.searchData.length; i++) {
                    $scope.searchItems[i] = {
                        id : $scope.searchData[i].sid,
                        label : $scope.searchData[i].label
                    };
                }
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                /*$scope.item.searchfield = response.data.searchfield;
                $scope.item.min_meter = response.data.min_meter;
                $scope.item.max_meter = response.data.max_meter;
                $scope.item.min_min = response.data.min_min;
                $scope.item.max_min = response.data.max_meter;
                $scope.item.price = response.data.price;*/
            },
            (err) => {
                console.log(err);
            });

    window.setSearchKey = (id, event) =>
    {
        console.log("Ich wurde aufgerufen!");
        let url = `../restapi/handler.php?objAction=estatesearchsession&objKey=${id}`;
        $http.put(url,
            {
                transformRequest: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            })
            .then((response) =>
                {
                    console.log(response.data);
                    console.log("status: " + response.status);
                    console.log("statusText: " + response.statusText);
                    window.startSearch = response.data.searchfield;
                    window.min_meter = response.data.min_meter;
                    window.max_meter = response.data.max_meter;
                    window.min_min = response.data.min_min;
                    window.max_min = response.data.max_min;
                    window.price = response.data.price;
                    $location.path("home");
                },
                (err) => {
                    console.log(err);
                });
        return true;
    };
}]);