studyHomeApp.controller('StudyHomeCtrl', ['$scope', '$http', '$location', function($scope, $http, $location){


    $scope.searchItems = [{}];

    if(window.searchItems) {
        $scope.searchItems = window.searchItems;
    }
    // alert(window.searchItems);
/*
    $scope.searchFormData = {

        "appsearch[fulltext]" : '',
        "appsearch[distmeter][Min]" : '',
        "appsearch[distmeter][Max]" : '',
        "appsearch[distopnv][Min]" : '',
        "appsearch[distopnv][Max]" : '',
        "appsearch[price][Min]" : '',
        "appsearch[price][Max]" : ''
    };
*/


    // $http({
    //     method : "GET",
    //     url : "../restapi/handler.php?objAction=estatesearch"
    // }).then(function mySuccess(response) {
    //     $scope.getSucc = response.data;
    //     console.log(response.data);
    //     console.log("status: " + response.status);
    //     console.log("statusText: " + response.statusText);
    // }, function myError(response) {
    //     $scope.error = response.statusText;
    //     console.error($scope.error);
    // });


    $scope.submitSearchForm = function() {
        console.log($scope.searchFormData);
        let formData = convertSearchFormData($scope.searchFormData);

        let submitData = new FormData();
        //console.log(formData);

        for(let key in formData)
        {
            if(formData[key] != null)
            {
                submitData.append(key, formData[key]);
                console.log(key);
            }
        }


        //submitData.append("appsearch[fulltext]", $scope.searchFormData.fulltext);
        /*
        submitData.append("appsearch[distmeter][Min]", $scope.searchFormData.distmeterMin);
        submitData.append("appsearch[distmeter][Max]", $scope.searchFormData.distmeterMax);
        submitData.append("appsearch[distopnv][Min]", $scope.searchFormData.distopnvMin);
        submitData.append("appsearch[distopnv][Max]", $scope.searchFormData.distopnvMax);
        submitData.append("appsearch[price][Min]", $scope.searchFormData.priceMin);
        submitData.append("appsearch[price][Max]", $scope.searchFormData.priceMax);
        */
        console.log(submitData);
        $http({
            url : "../restapi/handler.php?objAction=estatesearch",
            method: "PUT",
            headers : {'Content-Type': undefined},
            data : submitData
        }).then(function mySuccess(response) {
            $scope.putSucc = response.data;
            console.log(response.data);
            console.log("status: " + response.status);
            console.log("statusText: " + response.statusText);

            $http({
                method : "POST",
                url : "../restapi/handler.php?objAction=estatesearch"
                // headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function mySuccess(response) {
                $scope.searchData = response.data;
                console.log(response.data);
                console.log("status: " + response.status);
                console.log("statusText: " + response.statusText);
                //reset search items because sth could be left over
                window.searchItems = [{}];
                $scope.searchItems = [{}];
                for(let i = 0; i < $scope.searchData.length; i++) {
                    $scope.searchItems[i] = {
                        id : $scope.searchData[i].wohn_id,
                        alt : $scope.searchData[i].imgalt,
                        img: "images/thumb/" + $scope.searchData[i].imgpath,
                        name : $scope.searchData[i].name,
                        price : $scope.searchData[i].preis + " €",
                        surface : "24 m²"
                    };
                }
                window.searchItems = $scope.searchItems;
            }, function myError(response) {
                $scope.error = response.statusText;
                console.error($scope.error);
            });

        }, function myError(response) {
            $scope.error = response.statusText;
            console.error($scope.error);
        });
    };

    $scope.goToDetails = function(id, evt){
        $location.path("details?id=" + id);
    }

}]);

function convertSearchFormData(formData){
    let result = {
        "appsearch[fulltext]" : formData.fulltext,
        "appsearch[distmeter][Min]" : formData.distmeterMin,
        "appsearch[distmeter][Max]" : formData.distmeterMax,
        "appsearch[distopnv][Min]" : formData.distopnvMin,
        "appsearch[distopnv][Max]" : formData.distopnvMax,
        "appsearch[price][Min]" : formData.priceMin,
        "appsearch[price][Max]" : formData.priceMax
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
    if(document.getElementById("searchForm")) {
        document.getElementById("searchForm").triggerHandler('submit');
    }
};