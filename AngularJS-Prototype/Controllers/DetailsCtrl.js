studyHomeApp.controller('DetailsCtrl', ['$scope', '$http', '$routeParams', '$location', function($scope, $http, $routeParams, $location){

	var routeLink=$location.$$path;
	var estateID=routeLink.substr( routeLink.indexOf('=')+1 );
     
	
	$scope.favAdd = function () {
		
   
		// console.log(estateID);
		
		/*
		let dialogRef = dialog.open(UserProfileComponent, {
			height: '400px',
			width: '600px',
		});
		*/
		
		var mID = sessionStorage.getItem('m_id');
		
		if (mID < 1) {
			
			alert('Nicht als Mieter eingeloggt !');
			
		} else {

			$http({
				method : "PUT",
				url : "../restapi/handler.php?objAction=tenantfavorit",
				headers : {'Content-Type': 'application/json'},
				data : {"wohn_id":estateID, "m_id": mID, 'score': 'null'}
			}).then(function mySuccess(response) {
				
				

			}, function myError(response) {
				$scope.error = response.statusText;
				console.error($scope.error);
			});
		
		}
    };
	
    // console.log(getEstateID($location.$$path));

    $scope.detailsID = getEstateID($location.$$path);
	

	/*
	$http({
		method : "GET",
		url : "../restapi/handler.php?objAction=estateimages&objKey="+estateID,
	}).then(function mySuccess(response) {
		
		console.log(response);

	}, function myError(response) {
		$scope.error = response.statusText;
		console.error($scope.error);
	});
*/

    $http({
        method : "GET",
        url : "../restapi/handler.php" + getDetailsQueryString("default", $scope.detailsID)
    }).then(function mySuccess(response) {
		
        $scope.vm_id = null;
        $scope.default = response.data;
		
        console.log(response.data);

        $scope.name = $scope.default.name;
        $scope.desc = $scope.default.beschr;

        $scope.rent = $scope.default.preis;

        $scope.rooms = $scope.default.zimmer;
        $scope.surface = $scope.default.qm_groesse;

        $scope.city = $scope.default.ort;
        $scope.street = $scope.default.str;
        $scope.number = "123"; // PregMatach

        $scope.Postcode = $scope.default.plz;
        $scope.distance = $scope.default.entf_meter;
        $scope.time = $scope.default.entf_min;

        $scope.deposit = $scope.default.deposit;
        $scope.garage = $scope.default.garage;
        $scope.pets = $scope.default.tiere;

        $scope.free = $scope.default.frei;

        $scope.vm_id = $scope.default.vm_id;

        $scope.rightContent = [
            {
                title: "Rental Fee",
                description: $scope.rent
            },
            {
                title: "Rooms",
                description: $scope.rooms
            },
            {
                title: "Surface",
                description: response.data.qm_groesse+"m²"
            }
        ];
        $scope.bottomContent1 = [
            {
                title: "City",
                description: $scope.city
            },
            {
                title: "Street",
                description: $scope.street
            },
            {
                title: "Number",
                description: $scope.number
            }
        ];
        $scope.bottomContent2 = [
            {
                title: "Postcode",
                description: $scope.Postcode
            },
            {
                title: "Distance (Meter)",
                description: $scope.distance + " m"
            },
            {
                title: "Distance (Minutes)",
                description: $scope.time + " min"
            }
        ];
        $scope.bottomContent3 = [
            {
                title: "Deposit",
                description: $scope.deposit + " €"
            },
            {
                title: "Garage",
                description: $scope.garage
            },
            {
                title: "Free On",
                description: $scope.free
            }
        ];
        $scope.bottomContent4 = [
            {
                title: "Pets",
                description: $scope.pets
            }
        ];
    }, function myError(response) {
        $scope.error = response.statusText;
        console.error($scope.error);
    });

    $scope.contactLessor = function(){
        if(sessionStorage.getItem("isLoggedIn") !== "yes") {
            alert("Please login to use this feature!");
            return;
        }
        if(sessionStorage.getItem("role") === "Lessor") {
            alert("Please login to your tenant-account!");
            return;
        }
        if($scope.vm_id) {
            $location.path("messages?id=" + $scope.vm_id);
        }
    }

    // $http.get("../restapi/handler.php?objAction=estatedefault&objKey=2")
    //     .then(function(response){
    //         console.log(response.data);
    //     });

    // $scope.bottomContent = [{
    //
    // }];
}]);

function getDetailsQueryString(objAction, objKey) {
    return "?objAction=estate" + objAction + "&objKey=" + objKey;
}

// this doesn't really work
// function insertRightContent() {
//     document.getElementById("right-content");
//     let li = document.createElement("md-list-item");
//     li.setAttribute("class", "md-2-line");
//     li.setAttribute("ng-repeat", "item in rightContent");
//     let div = document.createElement("div");
//     div.setAttribute("class", "md-list-item-text");
//     li.append(div);
//     let h1 = document.createElement("h1");
//     h1.innerHTML = "{{item.title}}";
//     div.append(h1);
//     let p = document.createElement("p");
//     p.innerHTML = "{{item.description}}";
//     div.append(p);
// }

function getEstateID(path) {
    return path.match("[0-9]+");
}
