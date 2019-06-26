studyHomeApp.controller('HeaderCtrl', ['$scope', function($scope){
    window.setSearchKeyHeader = () =>
    {
        window.startSearch = $scope.startSearchHeader;
        $scope.startSearchHeader = '';
        return true;
    };
}]);