studyHomeApp.controller('ApartmentsCtrl', ['$scope', '$http', function($scope, $http) {

    console.log(sessionStorage.getItem('vm_id'));
    console.log(sessionStorage.getItem('vname'));
    let url = `../restapi/handler.php?objAction=lessorEstate&objKey=${sessionStorage.getItem('vm_id')}`;

    $scope.getApartments = () => {
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
                    if($scope.searchData.length === 0) {
                        document.getElementById("searchList").style.display="none";
                    }
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
    }
    $scope.getApartments();

    $scope.goToDetails2 = (id, evt) => {
        $location.path("details?id=" + id);
        console.log("Hallo:" + id);
    }

}]);