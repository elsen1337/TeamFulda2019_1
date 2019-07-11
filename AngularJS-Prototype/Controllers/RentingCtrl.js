studyHomeApp.controller('RentingCtrl', ['$scope', '$http', function($scope, $http){
    /*
    $scope.beschr = 'Schöne kleine 2-Zimmer-Wohnung. Ca. 30 Quadratmeter mit Badezimmer. ' +
        'Kaltmiete 200 € + Nebenkosten = 360 € Warmmiete. ' +
        'Zur Besichtigung kontaktieren Sie mich bitte über Study Home.';
    $scope.entf_meter = 1000;
    $scope.entf_min = 25;
    $scope.name = 'Wohnung in Fulda';
    $scope.ort = 'Fulda';
    $scope.plz = 36039;
    $scope.preis = 360;
    $scope.str = 'Gerloserweg 5';
    */

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


            // Assign values of the input fields to a formdata Object to send them to the server.
            fd.append("beschr", $scope.beschr);
            fd.append("entf_meter", $scope.entf_meter);
            fd.append("entf_min", $scope.entf_min);
            fd.append("name", $scope.name);
            fd.append("ort", $scope.ort);
            fd.append("plz", $scope.plz);
            fd.append("preis", $scope.preis);
            fd.append("str", $scope.str);
            fd.append("hausnummer", $scope.hausnummer);
            fd.append("zimmer", $scope.zimmer);
            fd.append("vm_id", sessionStorage.getItem("vm_id"));
            for (let value of fd.values()) {
                console.log(value);
            }

/*
            var jsonData = `{ "beschr": ${$scope.beschr},' +
            '"entf_meter": ${$scope.entf_meter},' +
            '"entf_min": ${$scope.entf},' +
            '"name": ${$scope.entf},' +
            '"ort": ${$scope.name},' +
            '"plz": ${$scope.ort},' +
            '"preis": ${$scope.plz},' +
            '"str": ${$scope.preis},' +
            '"vm_id": ${$scope.str}}`
*/

/*
            angular.forEach($scope.bilder, (val, key) =>
            {
                fd.append('bild' + '[' + key + ']', val);
            });
*/
            console.log(fd.get('preis'));

            // Convert formdata object to JSON
            let fdjson = JSON.stringify(Object.fromEntries(fd));

            // Send a put request with the inputs from the text and number fields to create a new appartment object.
            $http.post('../restapi/handler.php?objAction=estatedefault', fdjson,
                {
                    transformRequest: angular.identity,
                    headers: {
                        'Content-Type': 'application/json',
                        'charset': 'utf-8'
                    }
                })
                .then((serviceResponse) =>
                    {
                        $scope.newEstateID = serviceResponse.data.newEstateID;
                        /* For every picture attached to the file input send a put request
                        with the newly created appartment's id an alternate text and the order they should be safed in.
                        After that attach the current picture to a formdata object and send it to the server with it's newly assigned id.
                         */

                        dynamicAttribs = new FormData();

                        dynamicAttribs.append("wohn_id", $scope.newEstateID);
                        dynamicAttribs.append("qm_groesse", $scope.qm_groesse);
                        dynamicAttribs.append("garage", $scope.garage);
                        dynamicAttribs.append("frei_ab", $scope.frei_ab);
                        dynamicAttribs.append("tiere", $scope.tiere);
                        dynamicAttribs.append("kaution", $scope.kaution);

                        let attribjson = JSON.stringify(Object.fromEntries(dynamicAttribs));

                        $http.put('../restapi/handler.php?objAction=estateattribute', attribjson,
                            {
                                transformRequest: angular.identity,
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then((serviceResponse) =>
                                {
                                    console.log('Dynamic attribsResponse: ' + serviceResponse);
                                },
                                (err) => {
                                    console.log(err);
                                });


                        angular.forEach($scope.bilder, (val, key) =>
                        {

                            let fdi = new FormData();
                            fdi.append("wohn_id", $scope.newEstateID);
                            fdi.append("alt", "Bild der Wohnung");
                            fdi.append("rdr", 1);

                            let fdijson = JSON.stringify(Object.fromEntries(fdi));



                            $http.put('../restapi/handler.php?objAction=estateimages', fdijson,
                                {
                                    transformRequest: angular.identity,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'charset': 'utf-8'
                                    }
                                })
                                .then((serviceResponse) =>
                                    {
                                        let image = new FormData();
                                        console.log(serviceResponse.data.newImgID);
                                        image.append('bild' + '[' + serviceResponse.data.newImgID + ']', val);



                                        $http.post('../restapi/handler.php?objAction=estateimages', image,
                                            {
                                                transformRequest: angular.identity,
                                                headers: {
                                                    'Content-Type': undefined
                                                }
                                            })
                                            .then((serviceResponse) =>
                                                {
                                                    console.log(serviceResponse);
                                                },
                                                (err) => {
                                                    console.log(err);
                                                });

                                        console.log(serviceResponse);
                                    },
                                    (err) => {
                                        console.log(err);
                                    });


                        });

                        console.log(serviceResponse.data.newEstateID);
                    },
                    (err) => {
                        console.log(err);
                        document.getElementById('renting_output').innerText = `There seems to have been an error. We'll try to fix that soon.`;
                    });


            let tempName = $scope.name;
            document.getElementById('renting_output').innerText = `Your ad '${tempName}' has been succesfully added.`;

            $scope.beschr = '';
            $scope.entf_meter = '';
            $scope.entf_min = '';
            $scope.name = '';
            $scope.ort = '';
            $scope.plz = '';
            $scope.preis = '';
            $scope.str = '';
            $scope.hausnummer = '';
            $scope.zimmer = '';
            $scope.qm_groesse = '';
            $scope.garage = '';
            $scope.frei_ab = '';
            $scope.tiere = '';
            $scope.kaution = '';
            document.getElementById('fileinput').value = '';
        }
        else
        {
            document.getElementById('renting_output').innerText = 'Please fill out the required fields first.';
        }
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
