studyHomeApp.controller('RentingCtrl', ['$scope', '$http', function($scope, $http){
    $scope.beschr = 'Schöne kleine 2-Zimmer-Wohnung. Ca. 30 Quadratmeter mit Badezimmer. ' +
        '            Kaltmiete 200 € + Nebenkosten = 360 € Warmmiete.' +
        '            Zur Besichtigung kontaktieren Sie mich bitte über Study Home.';
    $scope.entf_meter = 1000;
    $scope.entf_min = 25;
    $scope.name = 'Horst Schlämmer';
    $scope.ort = 'Fulda';
    $scope.plz = 36039;
    $scope.preis = 360;
    $scope.str = 'Gerloserweg 5';
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


/*
            angular.forEach($scope.bilder, (val, key) =>
            {
                fd.append('bild'+key, key);
            });
*/
            console.log(fd);

            // Convert formdata object to JSON
            //let data = JSON.stringify(Object.fromEntries(fd));

            $http.put('https://hsftp.uber.space/sfsuroombook/restapi/handler.php?objAction=estatedefault', fd,
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
