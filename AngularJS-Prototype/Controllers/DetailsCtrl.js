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
