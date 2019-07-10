//
// var IMAGES = [{
//     src: 'img/estateDummy.png',
//     title: 'Pic 1'
// }, {
//     src: 'img/estateDummy.png',
//     title: 'Pic 2'
// }, {
//     src: 'img/estateDummy.png',
//     title: 'Pic 3'
// }, {
//     src: 'img/estateDummy.png',
//     title: 'Pic 4'
// }, {
//     src: 'img/estateDummy.png',
//     title: 'Pic 5'
// }];

studyHomeApp.controller('SliderCtrl', ['$scope', '$http', function ($scope, $http) {
    $scope.images = [{}];
    $http({
        method : "GET",
        url : "../restapi/handler.php" + getDetailsQueryString("images", $scope.detailsID)
    }).then(function mySuccess(response) {
        $scope.pix = response.data;
        // $scope.images = IMAGES;

        // console.log($scope.pix[0].pathnormal);

        // let pix = [
        //     {
        //         src: $scope.pix[0].pathnormal,
        //         title: 'Pic 1'
        //     }, {
        //         src: $scope.pix[1].pathnormal,
        //         title: 'Pic 2'
        //     }
        //
        // ];

        pix = getImageData($scope.pix);

        $scope.images = pix;
        $scope.currentIndex = 0;
        $scope.images[$scope.currentIndex].visible = true; // make the current image visible

        console.log(response);

    }, function myError(response) {
        $scope.error = response.statusText;
        console.error($scope.error);
    });
}])
    .directive('slider', function($timeout){
        return {
            restrict: 'AE',
            replace: true,
            scope: {
                images: '='
            },
            link: function($scope, elem, attrs){
                $scope.currentIndex = 0; // Initially the index is at the first image

                $scope.next = function() {
                    // scope.images = IMAGES;
                    // console.log($scope.images);
                    $scope.currentIndex < $scope.images.length - 1 ? $scope.currentIndex++ : $scope.currentIndex = 0;
                };

                $scope.prev = function() {
                    $scope.currentIndex > 0 ? $scope.currentIndex-- : $scope.currentIndex = $scope.images.length - 1;
                };
                $scope.$watch('currentIndex', function() {
                    $scope.images.forEach(function(image) {
                        image.visible = false; // make every image invisible
                    });
                    if($scope.images.length > 0) {
                        $scope.images[$scope.currentIndex].visible = true; // make the current image visible
                    }
                });
            },
            templateUrl: "Templates/Slider.html"
        };
    });

function getImageData(pix){
    let result = [{}];
    for(let i = 0; i < pix.length; i++) {
        result[i] = {};
        result[i].src = pix[i].pathnormal;
        result[i].title = "Pic" + i;
    }
    return result;
}
