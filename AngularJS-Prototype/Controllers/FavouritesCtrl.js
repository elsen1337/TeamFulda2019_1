studyHomeApp.controller('FavouritesCtrl', ['$http', '$scope', '$location', function($http, $scope, $location){


    console.log(sessionStorage.getItem('vm_id'));
    console.log(sessionStorage.getItem('vname'));
    let url = `../restapi/handler.php?objAction=estateimages&objKey=${sessionStorage.getItem('vm_id')}`;

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
                        img: "images/thumb/" + $scope.searchData[i].imgpath,
                        name : $scope.searchData[i].name,
                        price : $scope.searchData[i].preis + " €",
                        entf_meter: $scope.searchData[i].entf_meter + " m",
                        entf_min: $scope.searchData[i].entf_min + " min"
                    };
                }

            },
            (err) => {
                console.log(err);
            });

    $scope.goToDetails = function(id, evt){
        $location.path("details?id=" + id);
    }
}]);
