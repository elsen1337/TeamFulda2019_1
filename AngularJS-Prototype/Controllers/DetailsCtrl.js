/*
angular.module('material.components.dialog', [
  'material.core',
  'material.components.backdrop'
]);
*/

angular.module('selectDemoOptionsAsync', ['ngMaterial'])

studyHomeApp.controller('DetailsCtrl', ['$scope', '$http',  '$routeParams', '$location', '$timeout', function($scope, $http, $routeParams, $location, $timeout){

    var routeLink=$location.$$path;
    var estateID=routeLink.substr( routeLink.indexOf('=')+1 );


    $scope.favAdd = function () {

        var mID = sessionStorage.getItem('m_id');
		

		if (mID < 1) {

			alert('Nicht als Mieter eingeloggt !');

		} else {

			$http({
				method : "PUT",
				url : "../restapi/handler.php?objAction=tenantfavorit",
				data : {"wohn_id":estateID, "m_id": mID, 'score': 'null'},
				headers : {'Content-Type': 'application/json'}

			}).then(function mySuccess(asyncResp) {
				console.log(asyncResp.data);

			}, function myError(asyncResp) {
				console.error(asyncResp.statusText);
			});

		}
    };


	$scope.user = null;
	$scope.users = null;

	$scope.meetAdd = function () {
		
		console.log($scope.user);
		if (typeof($scope.user) != 'object') {
			return false;
		}
		
		var mID = sessionStorage.getItem('m_id');

				
		$http({
			method : "PUT",
			url : "../restapi/handler.php?objAction=tenantmeeting",
			data : {"tid":$scope.user.tid, "m_id": mID},
			headers : {'Content-Type': 'application/json'}

		}).then(function mySuccess(asyncResp) {
			console.log(asyncResp.data);
			
			alert("Successful Meeting Notification to Lessor !");

		}, function myError(asyncResp) {
			console.error(asyncResp.statusText);
		});
		
		
	};


	$scope.meetTimes = function() {
		
		$http({
			method : "GET",
			url : "../restapi/handler.php?objAction=estatemeeting&objKey="+estateID

		}).then(function mySuccess(asyncResp) {
			console.log(asyncResp.data);
			
			$scope.users=asyncResp.data;

		}, function myError(asyncResp) {
			console.error(asyncResp.statusText);
		});
		
		
		/*
		// Use timeout to simulate a 650ms request.
		return $timeout(function() {

		$scope.users =  $scope.users  || [
			{ id: 1, name: 'Scooby Doo' },
			{ id: 2, name: 'Shaggy Rodgers' },
			{ id: 3, name: 'Fred Jones' },
			{ id: 4, name: 'Daphne Blake' },
			{ id: 5, name: 'Velma Dinkley' }
		];

		}, 650);
		*/

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

        $scope.vid_url = $scope.default.vid_url;

        if ($scope.vid_url == null)
        {
            $scope.vid_url = "Streaming not possible at the moment!";
        }

        $scope.name = $scope.default.name;
        $scope.beschr = $scope.default.beschr;

        $scope.preis = $scope.default.preis;
        $scope.zimmer = $scope.default.zimmer;
        $scope.qm_groesse = $scope.default.qm_groesse;

        $scope.ort = $scope.default.ort;
        $scope.str = $scope.default.str;

        $scope.plz = $scope.default.plz;
        $scope.entf_meter = $scope.default.entf_meter;
        $scope.entf_min = $scope.default.entf_min;

        $scope.kaution = $scope.default.kaution;
        console.log($scope.default.garage);
        if($scope.default.garage == 1) {
            $scope.garage = 'Yes';
        } else {
            $scope.garage = 'No';
        }
        $scope.tiere = $scope.default.tiere;


        $scope.vm_id = $scope.default.vm_id;

        $scope.rightContent = [
            {
                title: "Rental Fee",
                description: $scope.preis
            },
            {
                title: "Rooms",
                description: $scope.zimmer
            },
            {
                title: "Surface",
                description: $scope.qm_groesse +"m²"
            }
        ];
        $scope.bottomContent1 = [
            {
                title: "Postcode",
                description: $scope.plz
            },
            {
                title: "City",
                description: $scope.ort
            }
        ];
        $scope.bottomContent2 = [
            {
                title: "Street",
                description: $scope.str
            },
            {
                title: "Distance (Meter)",
                description: $scope.entf_meter + " m"
            }
        ];
        $scope.bottomContent3 = [
            {
                title: "Distance (Minutes)",
                description: $scope.entf_min + " min"
            },
            {
                title: "Deposit",
                description: $scope.kaution + " €"
            }
        ];
        $scope.bottomContent4 = [
            {
                title: "Garage",
                description: $scope.garage
            },
            {
                title: "Pets",
                description: $scope.tiere
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
            $location.path("messages?id=" + $scope.vm_id + "&name=" + $scope.default.nname);
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

function GetRating(){
    var stars = document.getElementsByName('rating');
    for (i = 0;i < stars.length;i++){
        if(stars[i].checked){
            return stars[i].value;
        }
    }
    return 0;
}

