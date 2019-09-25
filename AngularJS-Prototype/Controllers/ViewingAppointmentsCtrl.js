studyHomeApp.controller('ViewingAppointmentsCtrl', ['$scope', '$http', function($scope, $http) {

	// Stores Apartments and slots.
    $scope.slots = [{}];
    $scope.apartments = [{}];

	// Send
	
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
	
	// Deletes meeting slot

    $scope.deleteSlot = function(slot_id)
    {
        $http({
            url: `../restapi/handler.php?objAction=estatemeeting&objKey=${slot_id}`,
            method: "DELETE",
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            $scope.slots = [{}];
            $scope.apartments = [{}];
            $scope.viewAppointments();

        }, function myError(response) {
            console.log(response);
        });
    };
	
	// View meeting slots

    $scope.viewAppointments = function () {
		// First of all: You have to get all apartments from the lessor
        id = sessionStorage.getItem("vm_id");
        $http({
                url: `../restapi/handler.php?objAction=lessorestate&objKey=${id}`,
                method: "GET",
            }).then(function mySuccess(response) {
                $scope.putSucc = response.data;
                object_wohnungen = $scope.putSucc;

				// Storing every apartment object in an array
                for (i = 0; i < object_wohnungen.length; i++) {
                    (function(_i, _object_wohnungen) {
                        obj = {
                            wohn_id: _object_wohnungen[_i]["wohn_id"],
                            name: _object_wohnungen[_i]["name"],
                        }
                        $scope.apartments.push(obj);
                    })(i, object_wohnungen)
                }

				// Check, which apartments have a timeslot from a potential tenant
                for (i = 0; i < object_wohnungen.length; i++) {
                    (function(_i, _object_wohnungen) {
                        $http({
                            url: `../restapi/handler.php?objAction=estatemeeting&objKey=${_object_wohnungen[_i]["wohn_id"]}`,
                            method: "GET",
                        }).then(function mySuccess(response) {
                  
                            $scope.putSucc = response.data;

                            timeslot = $scope.putSucc;

							// If the timeslot object has at least one object, the timeslot will be iterated for storing all valid entries into the slots array
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
								// null, if timeslot is empty
                                timeslot = null;
                            }
                        }, function myError(response) {
                            console.log(response);
                        });
                    })(i, object_wohnungen)
                }
			// Delete unneccessary header value 
            $scope.slots.splice(0);
            console.log($scope.slots);

        }, function myError(response) {
                console.log(response);
            });
    };

    $scope.viewAppointments();
}]);