studyHomeApp.controller('MyDataCtrl', ['$scope', function($scope){

    // Test which navbar should be displayed according to the logged in person's role.
    if (sessionStorage.getItem("role") === "Tenant")
    {
        document.getElementById("lessorSidenavCont").innerHTML = '';
    }
    else if (sessionStorage.getItem("role") === "Lessor") {
        document.getElementById("tenantSidenavCont").innerHTML = '';
    }

}]);