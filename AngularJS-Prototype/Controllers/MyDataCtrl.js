studyHomeApp.controller('MyDataCtrl', ['$scope', '$http', '$mdDialog', function($scope, $http, $mdDialog){

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if ($scope.rolle === "Tenant")
    {
        let element = document.getElementById("lessorSidenavCont");
        element.parentNode.removeChild(element);
    }
    else
    {
        let element = document.getElementById("tenantSidenavCont");
        element.parentNode.removeChild(element);
    }

    if($scope.rolle === 'Lessor') {
        $scope.roleid = sessionStorage.getItem('vm_id');
    } else {
        $scope.roleid = sessionStorage.getItem('m_id');
    }
    console.log($scope.roleid);

    let url = `../restapi/handler.php?objAction=${$scope.rolle.toLowerCase()}account&objKey=${$scope.roleid}`;
    console.log(url);

    $http.get(url,
        {
            transformRequest: angular.identity,
            headers: {
                withCredentials: true
            }
        })
        .then((response) =>
            {
                $scope.userData = response.data;
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                document.getElementById('tablerole').innerText = sessionStorage.getItem('role');
                document.getElementById('tablesalute').innerText = response.data.anrede;
                document.getElementById('tablevname').innerText = response.data.vname;
                document.getElementById('tablenname').innerText = response.data.nname;
                document.getElementById('tableemail').innerText = response.data.email;
                document.getElementById('tablebirthdate').innerText = '';

            },
            (err) => {
                console.log(err);
            });

    $scope.editData = (ev) => {
        document.getElementById('myDataOutput').style.display = 'block';
        document.getElementById('editDataBtn').style.display = 'none';
        document.getElementById('succesText').innerText = '';
    }

    $scope.sendData = () => {
        if($scope.newPw === $scope.repeatPw && $scope.newPw !== '') {
            var newPass = $scope.newPw;
            console.log($scope.oldPw);
            console.log("Password = " + newPass);
            document.getElementById('myDataOutput').style.display = 'none';
            document.getElementById('editDataBtn').style.display = 'block';

            let url = `../restapi/handler.php?objAction=${$scope.rolle.toLowerCase()}login&objKey=${$scope.roleid}`;

            let user = JSON.stringify({
                "email": $scope.userData.email.toLowerCase(),
                "pwort": $scope.oldPw
            });
            $http.post(url, user,
                {
                    transformRequest: angular.identity,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((response) => {
                        console.log(response.data);
                        console.log("status: " + response.status);
                        console.log("statusText: " + response.statusText);

                        url = `../restapi/handler.php?objAction=${$scope.rolle.toLowerCase()}account&objKey=${$scope.roleid}`;

                        data = JSON.stringify({'pwort': $scope.newPw});
                        $http.put(url, data,
                            {
                                transformRequest: angular.identity,
                                headers: {
                                    'Content-Type': undefined
                                }
                            })
                            .then((response) => {
                                    $scope.userData = response.data;
                                    console.log(response.data);
                                    console.log("status: " + response.status);
                                    console.log("statusText: " + response.statusText);
                                    document.getElementById('succesText').innerText = 'The password was succesfully changed!';
                                },
                                (err) => {
                                    console.log(err);
                                });
                    },
                    (err) => {
                        console.log(err);
                    });
        } else {
            document.getElementById('notEqualText').innerText = 'The passwords don\'t match.';
        }
    }
}]);