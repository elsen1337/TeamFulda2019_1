studyHomeApp.controller('HomeStartCtrl', ['$scope', '$http', '$location', function($scope, $http, $location){
    window.setSearchKey = () =>
    {
        window.startSearch = $scope.startSearch;
        return true;
    };
}]);

