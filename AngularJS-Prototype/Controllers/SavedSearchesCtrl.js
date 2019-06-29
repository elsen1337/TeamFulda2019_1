studyHomeApp.controller('SavedSearchesCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if (sessionStorage.getItem("role") === "Tenant")
    {
        document.getElementById("lessorSidenavCont").style.display="none";
    }
    else if (sessionStorage.getItem("role") === "Lessor") {
        document.getElementById("tenantSidenavCont").style.display="none";
    }

}]);