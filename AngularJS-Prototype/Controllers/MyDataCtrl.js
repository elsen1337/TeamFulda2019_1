studyHomeApp.controller('MyDataCtrl', ['$scope', function($scope){

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if (sessionStorage.getItem("role") === "Tenant")
    {
        document.getElementById("lessorSidenavCont").remove();
    }
    else if (sessionStorage.getItem("role") === "Lessor") {
        document.getElementById("tenantSidenavCont").remove();
    }

}]);