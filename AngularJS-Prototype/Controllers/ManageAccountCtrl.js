studyHomeApp.controller('ManageAccountCtrl', ['$scope', '$http', function($scope, $http) {

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