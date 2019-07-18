studyHomeApp.controller('StudyHomeCtrl', ['$scope', '$http', '$location', function($scope, $http, $location){



    $scope.searchItems = [{}];

    if(window.searchItems) {
        $scope.searchItems = window.searchItems;
    }
    // alert(window.searchItems);


    $scope.searchFormData =
    {
        "appsearch[fulltext]" : '',
        "appsearch[distmeter][Min]" : '',
        "appsearch[distmeter][Max]" : '',
        "appsearch[distopnv][Min]" : '',
        "appsearch[distopnv][Max]" : '',
        "appsearch[price][Min]" : '',
        "appsearch[price][Max]" : ''
    };

    $scope.searchFormData.fulltext = window.startSearch;

    $scope.submitSearchForm = function()
    {

        console.log($scope.searchFormData);
        let formData = convertSearchFormData($scope.searchFormData);

        let submitData = new FormData();
        //console.log(formData);

        for(let key in formData)
        {
                submitData.append(key, formData[key]);
        }

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
                        img: "../images/thumb/" + $scope.searchData[i].imgpath,
                        name : $scope.searchData[i].name,
                        price : $scope.searchData[i].preis + " €",
                        entf_meter: $scope.searchData[i].entf_meter + " m",
                        entf_min: $scope.searchData[i].entf_min + " min"
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

    $scope.submitSearchForm();

    $scope.saveSearch = () => {
        let formData = convertSearchFormData($scope.searchFormData);
        let submitData = new FormData();

        let M_ID = sessionStorage.getItem('m_id');
        let url = `../restapi/handler.php?objAction=estatesearchsession&objKey=${M_ID}`;

        for(let key in formData)
        {
            submitData.append(key, formData[key]);
        }

        $http.post(url, submitData)
            .then((response) =>
                {
                    console.log(response.data);
                    console.log("status: " + response.status);
                    console.log("statusText: " + response.statusText);
                    //reset search items because sth could be left over
                },
                (err) => {
                    console.log(err);
                });
    };
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
    return result;
}

function triggerSubmit() {
    if(document.getElementById("searchForm")) {
        document.getElementById("searchForm").triggerHandler('submit');
    }
};



