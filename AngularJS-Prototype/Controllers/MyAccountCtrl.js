studyHomeApp.controller('MyAccountCtrl', ['$scope', '$http', function($scope, $http){


    console.log(sessionStorage.getItem("role"));
    if (sessionStorage.getItem("role") === "Tenant")
    {
        console.log(1);
        document.getElementById("lessorSidenav").innerHTML = '';
    }
    else if (sessionStorage.getItem("role") === "Lessor")
    {
        console.log(2);
        document.getElementById("tenantSidenav").innerHTML = '';
    }

    $scope.rolle = sessionStorage.getItem("role").toLocaleLowerCase();
    $scope.sidenavurl = `Views/${$scope.rolle}Sidenav`;
    console.log($scope.rolle);

}]);