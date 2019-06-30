studyHomeApp.controller('RegisterCtrl', ['$scope', '$http', function($scope, $http){

    $scope.reg_roles = [
        "Lessor",
        "Tenant"
    ];

    $scope.salutations = [
        "Ms.",
        "Mr."
    ];

    $scope.user = [{}];


    $scope.register = function(){




        //$scope.user.account = $scope.rolle;
        //$scope.user.vname = document.getElementById("prename").value;
        //$scope.user.nname = document.getElementById("lastname").value;
        //$scope.user.pwort = document.getElementById("password").value;
        //$scope.user.email = document.getElementById("email").value;


        var urlVar = "";
        var sx = ""

        if ($scope.registryForm.$valid)
        {
            $scope.mytext = "";

            if ($scope.reg_rep_pwd === $scope.reg_pwd)
            {
                $scope.mytext = "";

                if ($scope.salutation === "Mr.")
                {
                    sx = "M";
                }
                else if ($scope.salutation === "Ms.")
                {
                    sx = "F";
                }

                $scope.user = {

                    "account"   : $scope.reg_role,
                    "anrede"    : sx,
                    "vname"     : $scope.reg_firstname,
                    "nname"     : $scope.reg_lastname,
                    "pwort"     : $scope.reg_pwd,
                    "email"     : $scope.reg_email.toLowerCase(),
                    "profil"    : ""
                };
                console.log($scope.user);

                if ($scope.user.account == "Lessor")
                {
                    urlVar = "../restapi/handler.php?objAction=lessoraccount"
                }
                else if ($scope.user.account == "Tenant")
                {
                    urlVar = "../restapi/handler.php?objAction=tenantaccount"
                }

                $http({
                    url: urlVar,
                    method: "POST",
                    // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                    data: $scope.user
                }).then(function mySuccess(response) {
                    $scope.putSucc = response.data;
                    console.log(response.data.sqlError);
                    console.log("status: " + response.status);
                    console.log("statusText: " + response.statusText);

                    if (response.data.sqlError === "Duplicate entry 'sunny.baudelaire@informatik.hs-fulda.de' for key 'email'")
                    {
                        $scope.mytext = "The user with the email " + $scope.reg_email + " is already assigned.";
                        $scope.reg_role = null;
                        $scope.salutation = null;
                        $scope.reg_firstname = null;
                        $scope.reg_lastname = null;
                        $scope.reg_pwd = null;
                        $scope.reg_rep_pwd = null;
                        $scope.reg_email = null;
                        $scope.reg_birthDate = null;
                    }
                    else if (response.data.sqlError === "")
                    {
                        window.location.href = "./Views/confirmation.html";
                    }
                    else
                    {
                        $scope.mytext = "";
                    }

                }, function myError(response) {
                    $scope.error = response.statusText;
                    console.error($scope.error);
                });
            } else {
                $scope.mytext = "The passwords are unequal! Please make sure you type in the same password twice.";
            }
        }
        else
        {
            $scope.mytext = "Please, fill out the entire form!";
        }
    };
}]);

