studyHomeApp.controller('RegisterCtrl', ['$scope', '$http', function($scope, $http){

    $scope.user = [{}];

    $scope.register = function(){
        var prename = document.getElementById("prename").value;
        var lastname = document.getElementById("lastname").value;
        var password = document.getElementById("password").value;
        var repeatPassword = document.getElementById("repeatPassword").value;
        var email = document.getElementById("email").value;
        var birthDate = document.getElementById("birthDate").value;
        console.log('Prename = ' + prename
            + ', Lastname = ' + lastname
            + ', Passwort = ' + password
            + ', Repeat Passwort = ' + repeatPassword
            + ', E-Mail = ' + email
            + ', Birth Date = ' + birthDate);

        $scope.user = {

            "prename" : prename,
            "lastname" : lastname,
            "password" : password,
            "repeatPassword" : repeatPassword,
            "email" : email,
            "birthDate" : birthDate,
        };

        console.log($scope.user);
        let submitData = convertRegisterFormData($scope.user);
        console.log(submitData);
        $http({
            url : "../restapi/handler.php?objAction=lessoraccount",
            method: "POST",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data : submitData
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);
        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
    };

    $scope.submitSearchForm = function() {
        console.log($scope.user);
        let submitData = convertRegisterFormData($scope.user);
        console.log(submitData);
        $http({
            url : "../restapi/handler.php?objAction=lessoraccount",
            method: "POST",
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data : submitData
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);
        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
    };

}]);

function convertRegisterFormData(formData){
    let result = {
        "registry[prename]" : formData.prename,
        "registry[lastname]" : formData.lastname,
        "registry[password]" : formData.password,
        "registry[repeatPassword]" : formData.repeatPassword,
        "registry[email]" : formData.email,
        "registry[birthDate]" : formData.birthDate
    };
    // let result = [];
    // result["appsearch[fulltext]"] = formData.fulltext;
    // result["appsearch[distmeter][Min]"] = formData.distmeterMin;
    // result["appsearch[distmeter][Max]"] = formData.distmeterMax;
    // result["appsearch[distopnv][Min]"] = formData.distopnvMin;
    // result["appsearch[distopnv][Max]"] = formData.distopnvMax;
    // result["appsearch[price][Min]"] = formData.priceMin;
    // result["appsearch[price][Max]"] = formData.priceMax;
    return result;
}

function triggerSubmit() {
    if(document.getElementById("regForm")) {
        document.getElementById("regForm").triggerHandler('submit');
        console.log("sdsafdsfdsfdsf");
    }
};