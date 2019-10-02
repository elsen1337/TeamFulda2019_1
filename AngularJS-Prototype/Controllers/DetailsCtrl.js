/*
angular.module('material.components.dialog', [
  'material.core',
  'material.components.backdrop'
]);
*/

angular.module('selectDemoOptionsAsync', ['ngMaterial'])

studyHomeApp.controller('DetailsCtrl', ['$scope', '$http',  '$routeParams', '$location', function($scope, $http, $routeParams, $location){

    let url;
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

	// Creating function to send a event to the handler.php.
	// The handler processes the event and sends it to the Raspberry Pi
	$scope.sendEventRaspberryPi = function(event)
    {
		data = {
			"event": event,
			"host": '172.18.96.49'
		};
		
        $http({
            method : "PUT",
            url : "../restapi/handler.php?objAction=estatestream",
            data: data

        }).then(function mySuccess(asyncResp) {
            console.log(asyncResp.data);

        }, function myError(asyncResp) {
            console.error(asyncResp.statusText);
        });
    }


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

	};

    $scope.detailsID = getEstateID($location.$$path);

	
    $http({
        method : "GET",
        url : "../restapi/handler.php" + getDetailsQueryString("default", $scope.detailsID)
    }).then(function mySuccess(response) {
        $scope.vm_id = null;
        $scope.default = response.data;

        console.log(response.data);
		
		// Check, if apartment has stream url
		if ($scope.default.vid_url !== null)
		{
			$scope.vid_url = $scope.default.vid_url;
			$scope.ipaddress = $scope.vid_url.replace('http://', '');
			$scope.ipaddress = $scope.ipaddress.substring(0, $scope.ipaddress.indexOf(":"));
		}
		else
		{
			console.log("No Vid_url found!");
		}
		
        if ($scope.vid_url !== null)
        {
            $scope.stream_available = true;
        }
        else
        {
            $scope.stream_available = false;
        }
		
		// Check if url is available

		if ($scope.stream_available) {
			console.log($scope.ipaddress);
			$http({
				method: "GET",
				url: `../restapi/handler.php?objAction=estatestream&objKey=${$scope.ipaddress}`,
				headers: {'Content-Type': 'application/json'}

			}).then(function mySuccess(response) {
				$scope.putSucc = response.data;

				$scope.urlPing = $scope.putSucc;

				console.log($scope.urlPing)

			}, function myError(response) {
				console.error(response.statusText);
			});
		}
		
		// URL finishes here
		
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
        $scope.getRating();
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


    $scope.getRating = () => {
        url = `../restapi/handler.php?objAction=tenantrating&objKey=${$scope.vm_id}`;
        $http.get(url,
            {
                transformRequest: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            })
            .then((response) =>
                {
                    $scope.ratingData = response.data;
                    console.log(response.data);
                    console.log("status: " + response.status);
                    console.log("statusText: " + response.statusText);

                    $scope.bewertungenItems = [{}];
                    if($scope.ratingData.length === 0) {
                        document.getElementById("bewertungenListe").style.display="none";
                    } else {
                        document.getElementById("bewertungenListe").style.display="block";
                    }

                    document.getElementById("bewertungenListe").innerHTML = "";
                    for(let i = 0; i < $scope.ratingData.length; i++) {
                        let stars = "";
                        let starGold = "<input type=\"radio\"/><label style='color: gold'></label>\n";
                        let starGrey = "<input type=\"radio\"/><label></label>\n";
                        switch($scope.ratingData[i].stars.toString()){
                            case "1": stars = "<fieldset class=\"sterne\">\n" +
                                starGrey + starGrey + starGrey + starGrey + starGold +
                                "                    </fieldset>\n";
                                break;
                            case "2": stars = "<fieldset class=\"sterne\">\n" +
                                starGrey + starGrey + starGrey + starGold + starGold +
                                "                    </fieldset>\n";
                                break;
                            case "3": stars = "<fieldset class=\"sterne\">\n" +
                                starGrey + starGrey + starGold + starGold + starGold +
                                "                    </fieldset>\n";
                                break;
                            case "4": stars = "<fieldset class=\"sterne\">\n" +
                                starGrey + starGold + starGold + starGold + starGold +
                                "                    </fieldset>\n";
                                break;
                            case "5": stars = "<fieldset class=\"sterne\">\n" +
                                starGold + starGold + starGold + starGold + starGold +
                                "                    </fieldset>\n";
                                break;
                            default: stars = "<fieldset class=\"sterne\">\n" +
                                starGrey + starGrey + starGrey + starGrey + starGrey +
                                "                    </fieldset>\n";
                                break;
                        }

                        document.getElementById("bewertungenListe").innerHTML += "" +
                            "<md-list-item>\n" +
                            stars +
                            "                    <p id=\"kommentar-Liste\">" + $scope.ratingData[i].cmt + "</p>\n" +
                            "                </md-list-item>";
                    }
                },
                (err) => {
                    console.log(err);
                });
    }

    $scope.postRating = () => {
        var mID = sessionStorage.getItem('m_id');
        url = `../restapi/handler.php?objAction=tenantrating&objKey=${mID}`;
        if (mID < 1) {
            alert('Not logged in as a tenant!');
        }
        else if($scope.kommentar === '' || $scope.kommentar === undefined){
            alert('Please write a comment!')
        } else {
            data = JSON.stringify({'vm_id': $scope.vm_id, 'm_id': mID, 'stars': getStars(), 'cmt': $scope.kommentar});

            $http.post(url, data,
                {
                    transformRequest: angular.identity,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((response) => {
                        $scope.userData = response.data;
                        console.log(response.data);
                        console.log("status: " + response.status);
                        console.log("statusText: " + response.statusText);
                        $scope.getRating();
                        $scope.kommentar='';
                    },
                    (err) => {
                        console.log(err);
                    });
        }
    }
}]);

function getDetailsQueryString(objAction, objKey) {
    return "?objAction=estate" + objAction + "&objKey=" + objKey;
}

function getEstateID(path) {
    return path.match("[0-9]+");
}

function getStars(){
    var stars = document.getElementsByName('rating');
    for (i = 0;i < stars.length;i++){
        if(stars[i].checked){
            return stars[i].value;
        }
    }
    return 0;
}
