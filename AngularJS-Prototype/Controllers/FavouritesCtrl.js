studyHomeApp.controller('FavouritesCtrl', ['$http', '$scope', '$location', function($http, $scope, $location){


    console.log(sessionStorage.getItem('m_id'));
    console.log(sessionStorage.getItem('vname'));
    let url = `../restapi/handler.php?objAction=tenantfavorit&objKey=${sessionStorage.getItem('m_id')}`;

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
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                //reset search items because sth could be left over

                $scope.searchItems = [{}];
                for(let i = 0; i < $scope.searchData.length; i++) {
                    $scope.searchItems[i] = {
                        id : $scope.searchData[i].wohn_id,
                        alt : $scope.searchData[i].imgalt,
                        name : $scope.searchData[i].name,
                    };
                }
            },
            (err) => {
                console.log(err);
            });

    $scope.goToDetails2 = function(id, evt){
        $location.path("details?id=" + id);
        console.log("Hallo:" + id);
    }

    let mid = sessionStorage.getItem('m_id');

    let url2 = `../restapi/handler.php?objAction=tenantfavorit&objKey=${mid}-${id}`;
    $scope.delete = (id, evt) => {
        $http.delete(url2)
            .then((response) =>
                {
                    $scope.searchData = response.data;
                    console.log(response.data);
                    console.log("status: " + response.status);
                    console.log("statusText: " + response.statusText);
                    //reset search items because sth could be left over
                },
                (err) => {
                    console.log(err);
                });
    }
}]);

