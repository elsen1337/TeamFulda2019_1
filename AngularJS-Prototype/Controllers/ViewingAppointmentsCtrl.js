studyHomeApp.controller('ViewingAppointmentsCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.slots = [{}];
    $scope.apartments = [{}];

    $scope.sendSlot = function()
    {

        if ($scope.appartmentform.$valid) {
            let user = {
                "wohn_id": $scope.apartmselection,
                "slot": $scope.datetime
            }

            $http({
                url: `../restapi/handler.php?objAction=estatemeeting&objKey`,
                //url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                method: "POST",
                data: user
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            }).then(function mySuccess(response) {
                /*
                $scope.putSucc = response.data;
                slots = $scope.putSucc;
                console.log(slots);
                $scope.slots = slots;
                $scope.slots.button = "<input type='button' value='Click me'/>"
                */
                $scope.putSucc = response.data;
                console.log($scope.putSucc);
                $scope.slots = [{}];
                $scope.apartments = [{}];
                $scope.viewAppointments();

            }, function myError(response) {
                console.log(response);
            });
        }
    };

    $scope.deleteSlot = function(slot_id)
    {
        $http({
            url: `../restapi/handler.php?objAction=estatemeeting&objKey=${slot_id}`,
            //url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
            method: "DELETE",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
        }).then(function mySuccess(response) {
            /*
            $scope.putSucc = response.data;
            slots = $scope.putSucc;
            console.log(slots);
            $scope.slots = slots;
            $scope.slots.button = "<input type='button' value='Click me'/>"
            */
            $scope.putSucc = response.data;
            $scope.slots = [{}];
            $scope.apartments = [{}];
            $scope.viewAppointments();

        }, function myError(response) {
            console.log(response);
        });
    };

    $scope.viewAppointments = function () {
        id = sessionStorage.getItem("vm_id");
        $http({
                url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                //url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                method: "GET",
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            }).then(function mySuccess(response) {
                $scope.putSucc = response.data;
                object_wohnungen = $scope.putSucc;

                for (i = 0; i < object_wohnungen.length; i++) {
                    (function(_i, _object_wohnungen) {
                        obj = {
                            wohn_id: _object_wohnungen[_i]["wohn_id"],
                            name: _object_wohnungen[_i]["name"],
                        }
                        $scope.apartments.push(obj);
                    })(i, object_wohnungen)
                }

                //console.log(object_wohnungen);

                for (i = 0; i < object_wohnungen.length; i++) {
                    (function(_i, _object_wohnungen) {
                        $http({
                            url: `../restapi/handler.php?objAction=estatemeeting&objKey=${_object_wohnungen[_i]["wohn_id"]}`,
                            //url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                            method: "GET",
                            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                        }).then(function mySuccess(response) {
                            /*
                            $scope.putSucc = response.data;
                            slots = $scope.putSucc;
                            console.log(slots);
                            $scope.slots = slots;
                            $scope.slots.button = "<input type='button' value='Click me'/>"
                            */
                            //console.log($scope.putSucc);
                            $scope.putSucc = response.data;

                            timeslot = $scope.putSucc;


                            if (timeslot.length > 0) {
                                    for (j = 0; j < timeslot.length; j++) {
                                        obj = {
                                            tid: timeslot[j].tid,
                                            slot: timeslot[j].slot,
                                            bookedBy: timeslot[j].bookedBy,
                                            name: _object_wohnungen[_i]["name"],
                                            isValid: true
                                        }

                                        $scope.slots.push(obj);
                                    }
                                   // $scope.slots["name"] = _object_wohnungen[_i]["name"];
                                    //console.log($scope.slots);
                                timeslot = null;
                                //console.log($scope.slots);
                            }
                        }, function myError(response) {
                            console.log(response);
                        });
                    })(i, object_wohnungen)
                }
            $scope.slots.splice(0);
            console.log($scope.slots);

        }, function myError(response) {
                console.log(response);
            });
    };

    $scope.viewAppointments();
}]);