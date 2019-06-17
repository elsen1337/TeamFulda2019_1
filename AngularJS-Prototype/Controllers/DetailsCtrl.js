studyHomeApp.controller('DetailsCtrl', ['$scope', '$http', function($scope, $http){

    $http({
        method : "GET",
        url : "https://hsftp.uber.space/sfsuroombook/restapi/handler.php?objAction=estatedefault&objKey=1"
    }).then(function mySuccess(response) {
        $scope.test = response.data;
        alert("success" + response.statusText);
    }, function myError(response) {
        $scope.testErr = response.statusText;
        alert("error" + response.statusText);
    });

    $scope.rightContent = [{
            title: "Rent",
            description: "300€"
        },
        {
            title: "Rooms",
            description: "3"
        },
        {
            title: "Surface",
            description: "27m²"
        }];
    $scope.bottomContent = [{

    }];
    $scope.bottomText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut aliquam purus sit amet luctus venenatis lectus magna. Amet luctus venenatis lectus magna fringilla urna porttitor rhoncus dolor. Ut tristique et egestas quis ipsum suspendisse ultrices. Dignissim convallis aenean et tortor at risus viverra. Id neque aliquam vestibulum morbi blandit. Lectus proin nibh nisl condimentum id."
}]);