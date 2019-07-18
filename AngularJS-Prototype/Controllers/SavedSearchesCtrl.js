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
                        Fulltext: $scope.searchData[i].label.FullText-Search,
                        label : $scope.searchData[i].label
                    };
                }
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
            },
            (err) => {
                console.log(err);
            });

    $scope.setSearchKey = (id, event) =>
    {
        console.log("Ich wurde aufgerufen!");
        let data = JSON.stringify({sid: id});
        let url = `../restapi/handler.php?objAction=estatesearchsession&objKey=${M_ID}`;
        $http.put(url, data,
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
                    $location.path("home");
                },
                (err) => {
                    console.log(err);
                });
        return true;
    };
}]);