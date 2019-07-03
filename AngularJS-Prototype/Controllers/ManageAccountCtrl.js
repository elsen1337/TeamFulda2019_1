studyHomeApp.controller('ManageAccountCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if ($scope.rolle === "Tenant")
    {
        console.log('hallo');
        let element = document.getElementById("lessorSidenavCont");
        element.parentNode.removeChild(element);
    }
    else
    {
        let element = document.getElementById("tenantSidenavCont");
        element.parentNode.removeChild(element);
    }

}]);