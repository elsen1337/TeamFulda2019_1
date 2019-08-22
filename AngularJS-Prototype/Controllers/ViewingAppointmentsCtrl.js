studyHomeApp.controller('ViewingAppointmentsCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.slots = [{}];

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
            console.log('Deleted: ' + slot_id);
            $scope.viewAppointments();

        }, function myError(response) {
            console.log(response);
        });
    };

    $scope.viewAppointments = function () {
        document.getElementById('showAllButton').style.display = "none";
        $scope.slots = [{}];
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
                            $scope.putSucc = response.data;
                            timeslot = $scope.putSucc;

                            if (timeslot.length > 0) {
                                obj = {
                                    tid : timeslot[0].tid,
                                    slot: timeslot[0].slot,
                                    name: _object_wohnungen[_i]["name"],
                                    isValid: true
                                }
                                $scope.slots.push(obj);
                               // $scope.slots["name"] = _object_wohnungen[_i]["name"];
                                console.log($scope.slots);
                                timeslot = null;
                            }
                        }, function myError(response) {
                            console.log(response);
                        });
                    })(i, object_wohnungen)
                }

            }, function myError(response) {
                console.log(response);
            });
    };
}]);