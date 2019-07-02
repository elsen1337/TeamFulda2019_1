studyHomeApp.controller('MyAccountCtrl', ['$scope', '$http', function($scope, $http){

    $scope.rolle = sessionStorage.getItem("role");
    console.log($scope.rolle);
    // Test which navbar should be displayed according to the logged in person's role.


    if ($scope.rolle === "Tenant")
    {
        let element = document.getElementById("lessorSidenavCont");
        element.parentNode.removeChild(element);
    }
    else
    {

        let element = document.getElementById("tenantSidenavCont");
        element.parentNode.removeChild(element);
    }

}]);