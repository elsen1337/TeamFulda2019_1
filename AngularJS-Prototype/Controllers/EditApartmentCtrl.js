studyHomeApp.controller('EditApartmentCtrl', ['$scope', '$http', '$location', function($scope, $http, $location) {

    var routeLink=$location.$$path;
    var estateID=routeLink.substr( routeLink.indexOf('=')+1 );
    console.log(estateID);

    $http({
        method : "GET",
        url : `../restapi/handler.php?objAction=estatedefault&objKey=${estateID}`
    }).then(function mySuccess(response) {

        $scope.vm_id = sessionStorage.getItem('vm_id');
        $scope.default = response.data;

        console.log($scope.default);

        $scope.name = $scope.default.name;
        $scope.beschr = $scope.default.beschr;

        $scope.preis = parseInt($scope.default.preis);
        $scope.zimmer = parseInt($scope.default.zimmer);
        $scope.qm_groesse = parseInt($scope.default.qm_groesse);

        $scope.ort = $scope.default.ort;
        $scope.str = $scope.default.str;

        $scope.plz = parseInt($scope.default.plz);
        $scope.entf_meter = parseInt($scope.default.entf_meter);
        $scope.entf_min = parseInt($scope.default.entf_min);

        $scope.kaution = parseInt($scope.default.kaution);
        console.log($scope.default.garage);

        if ($scope.default.garage == 1) {
            $scope.garage = 'Yes';
        } else {
            $scope.garage = 'No';
        }
        $scope.tiere = $scope.default.tiere;


        $scope.vm_id = $scope.default.vm_id;

    },
        (err) => {
            console.log(err);
    });

    $scope.edit = () => {

        if($scope.entf_meter !== undefined
            && $scope.entf_min !== undefined
            && $scope.name !== undefined
            && $scope.ort !== undefined
            && $scope.plz !== undefined
            && $scope.preis !== undefined
            && $scope.str !== undefined) {
            let fd = new FormData();


            // Assign values of the input fields to a formdata Object to send them to the server.
            fd.append("beschr", document.getElementById('beschr').value);
            fd.append("entf_meter", document.getElementById('entf_meter').value);
            fd.append("entf_min", document.getElementById('entf_min').value);
            fd.append("name", document.getElementById('name').value);
            fd.append("ort", document.getElementById('ort').value);
            fd.append("plz", document.getElementById('plz').value);
            fd.append("preis", document.getElementById('preis').value);
            fd.append("str", document.getElementById('str').value);
            fd.append("zimmer", document.getElementById('zimmer').value);
            fd.append("qm_groesse", document.getElementById('qm_groesse').value);
            if (document.getElementById('garage').value.toLowerCase() === 'ja' || document.getElementById('garage').value.toLowerCase() === 'yes') {
                fd.append("garage", 1);
            } else {
                fd.append("garage", 0);
            }
            fd.append("tiere", document.getElementById('tiere').value);
            fd.append("kaution", document.getElementById('kaution').value);
            for (let value of fd.values()) {

            }

            console.log(fd.get('preis'));

            // Convert formdata object to JSON
            let fdjson = JSON.stringify(Object.fromEntries(fd));

            let vmid = sessionStorage.getItem('vm_id');
            let url = `../restapi/handler.php?objAction=estatedefault&objKey=${estateID}`;

            console.log("Now do a put");

            $http.put(url, fdjson,
                {
                    transformRequest: angular.identity,
                    headers: {
                        'Content-Type': 'application/json',
                        'charset': 'utf-8'
                    }
                })
                .then((serviceResponse) =>
                    {
                        console.log("put done");
                        /* For every picture attached to the file input send a put request
                        with the newly created appartment's id an alternate text and the order they should be safed in.
                        After that attach the current picture to a formdata object and send it to the server with it's newly assigned id.
                         */

                        angular.forEach($scope.bilder, (val, key) =>
                        {

                            let fdi = new FormData();
                            fdi.append("wohn_id", estateID);
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
                        $scope.goToApartmentspage();
                    },
                    (err) => {
                        console.log(err);
                        document.getElementById('renting_output').innerText = `There seems to have been an error. We'll try to fix that soon.`;
                    });


            let tempName = $scope.name;
            document.getElementById('renting_output').innerText = `Your ad '${tempName}' has been succesfully edited.`;

            $scope.beschr = '';
            $scope.entf_meter = '';
            $scope.entf_min = '';
            $scope.name = '';
            $scope.ort = '';
            $scope.plz = '';
            $scope.preis = '';
            $scope.str = '';
            $scope.zimmer = '';
            $scope.qm_groesse = '';
            $scope.garage = '';
            $scope.tiere = '';
            $scope.kaution = '';
            document.getElementById('fileinput').value = '';
        }
        else
        {
            document.getElementById('renting_output').innerText = 'Please fill out the required fields first.';
        }

    };

    $scope.delete = () => {
    //let mid = sessionStorage.getItem('m_id');
    let url2 = `../restapi/handler.php?objAction=estatedefault&objKey=${estateID}`;
    $http.delete(url2)
        .then((response) =>
            {
                $scope.searchData = response.data;
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                //reset search items because sth could be left over
            },
            (err) => {
                console.log(err);
            });
    $scope.goToApartmentspage();
    };

    $scope.goToApartmentspage = () =>
    {
        $location.path("apartments");
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