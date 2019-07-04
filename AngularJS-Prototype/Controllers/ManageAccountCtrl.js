angular.module('dialogDemo1', ['ngMaterial'])
studyHomeApp.controller('ManageAccountCtrl', ['$scope', '$http', '$mdDialog', function($scope, $http, $mdDialog) {


    $scope.showPrompt = function(ev) {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.prompt()
            .title('Do you really want to delete your account?')
            .textContent('Type in your password to delete your account.')
            .ariaLabel('Password')
            .targetEvent(ev)
            .required(true)
            .ok('Delete my account')
            .cancel('I keep the account');
        input_3

        $mdDialog.show(confirm).then(function(result) {
               // console.log(result);
            var urlVar = "";

            if (sessionStorage.getItem("role") === "Lessor") {
                urlVar = "../restapi/handler.php?objAction=lessorlogin";
            } else if (sessionStorage.getItem("role") === "Tenant") {
                urlVar = "../restapi/handler.php?objAction=tenantlogin";
            }

            let user = {
                "email": sessionStorage.getItem("email"),
                "pwort": result
            }

            console.log(user);

            $http({
                url: urlVar,
                method: "POST",
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                data: user
            }).then(function mySuccess(response) {
                $scope.putSucc = response.data;

                console.log(sessionStorage.getItem("m_id"));

                if (response.data === null) {
                    console.log("No results!");
                    alert("Your entered password is wrong!");
                }
                else {
                    if (sessionStorage.getItem("vm_id") === response.data["vm_id"] &&
                        sessionStorage.getItem("vm_id") !== "undefined")
                    {
                        deleteAccount(sessionStorage.getItem("role"), sessionStorage.getItem("vm_id"));
                    }
                    else if (sessionStorage.getItem("m_id") === response.data["m_id"] &&
                             sessionStorage.getItem("m_id") !== "undefined")
                    {
                        deleteAccount(sessionStorage.getItem("role"), sessionStorage.getItem("m_id"));
                    }
                    else
                    {
                        console.log("ID is wrong");
                    }
                }
                // $location.path("index.html#!/homeStart");
            }, function myError(response) {
                $scope.error = response.statusText;
                console.error($scope.error);
            });

        }, function() {
        });
    };

    $scope.rolle = sessionStorage.getItem("role");

    // Test which navbar should be displayed according to the logged in person's role.
    if ($scope.rolle === "Tenant")
    {
        console.log('hallo');
        let element = document.getElementById("lessorSidenavCont");
        element.parentNode.removeChild(element);
    }
    else
    {
        let element = document.getElementById("tenantSidenavCont");
        element.parentNode.removeChild(element);
    }

    function deleteAccount(role, id)
    {
        var path = "";

        if (role === "Lessor") {
            path = `../restapi/handler.php?objAction=lessoraccount&objKey=${id}`;
        } else if (role === "Tenant") {
            path = `../restapi/handler.php?objAction=tenantaccount&objKey=${id}`;
        }

        objkey = parseInt(id);

        $http({

            url: path,
            method: "DELETE",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            //data: id
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log($scope.putSucc);
            sessionStorage.clear();
            window.location.href = "./index.html#!/homeStart";
            window.location.reload();
        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
    }

}]);