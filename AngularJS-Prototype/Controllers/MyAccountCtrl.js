studyHomeApp.controller('MyAccountCtrl', ['$scope', '$http', function($scope, $http){

    $scope.rolle = sessionStorage.getItem("role");
    console.log($scope.rolle);
    // Test which navbar should be displayed according to the logged in person's role.


    if ($scope.rolle === "Tenant")
    {
        console.log(sessionStorage.getItem("role"));
        document.getElementById("lessorSidenavCont").remove();
        console.log($scope.rolle);
    }
    else
    {

        document.getElementById("tenantSidenavCont").remove();

    }

}]);