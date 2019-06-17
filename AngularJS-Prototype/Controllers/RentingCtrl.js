studyHomeApp.controller('RentingCtrl', ['$scope', '$http', function($scope, $http){

    $scope.submit = () =>
    {
        if($scope.entf_meter !== undefined
        && $scope.entf_min !== undefined
            && $scope.name !== undefined
            && $scope.ort !== undefined
            && $scope.plz !== undefined
            && $scope.preis !== undefined
            && $scope.str !== undefined)
        {
            let fd = new FormData();

            fd.append('beschr', $scope.beschr);
            fd.append('entf_meter', $scope.entf_meter);
            fd.append('entf_min', $scope.entf_min);
            fd.append('name', $scope.name);
            fd.append('ort', $scope.ort);
            fd.append('plz', $scope.plz);
            fd.append('preis', $scope.preis);
            fd.append('str', $scope.str);



            angular.forEach($scope.bilder, (val, key) =>
            {
                fd.append('bild'+key, key);
            });

            console.log(fd);

            // Convert formdata object to JSON
            //let data = JSON.stringify(Object.fromEntries(fd));

            $http.post('https://hsftp.uber.space/sfsuroombook/wohnung', fd,
                {
                    transformRequest: angular.identity
                })
                .then((serviceResponse) =>
                    {
                        console.log(serviceResponse);
                    },
                    (err) => {
                        console.log(err);
                    });
        };
        }

    }])

    // Adds the selected files to the files array
    .directive('fileModel', ['$parse', function ($parse) {
        return {
            restrict: 'A'
            , link: function (scope, element, attrs) {
                let model = $parse(attrs.fileModel);
                let modelSetter = model.assign;
                element.bind('change', function () {
                    let files = [];
                    angular.forEach(element[0].files,function(file){
                        files.push(file);
                    });
                    scope.$apply(function () {
                        modelSetter(scope, files);
                    });
                });
            }
        };
    }]);
