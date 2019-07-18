// for help visit:
// https://material.angularjs.org/latest/
// https://www.w3schools.com/angular/

"use strict";

// Routes
var ROUTES = {
    page1: "renting",
    page3: "login",
    page4: "register",
    page11: "homeStart",
    // none nav item routes
    page5: "details",
    page6: "agb",
    page7: "contactAndHelp",
    page8: "aboutUs",
    page9: "impressum",
    page0: "home",
    // sideNav item routes
    // tenant/lessor
    page13: "myData",
    page2: "messages",
    page14: "manageAccount",
    // tenant
    page12: "favourites",
    page15: "savedSearches",
    // lessor
    page16: "viewingAppointments",
    page10: "apartments",
    page17: "editApartment"

};

// App Module
var studyHomeApp = angular.module('studyHomeApp', ['ngRoute', 'ngMaterial', 'ngAnimate'])
    .run(function(){
        console.log('Study Home is ready!');
    });

// App Configuration
studyHomeApp.config(['$routeProvider', '$mdThemingProvider', function($routeProvider, $mdThemingProvider){

    $mdThemingProvider.theme('default')
        .primaryPalette('indigo')
        .accentPalette('blue')
        .warnPalette('red')
        .backgroundPalette('grey');
    //$mdThemingProvider.theme('default')
    //    .dark();

    $routeProvider
        .when('/' + ROUTES.page0 , {
            templateUrl: 'Views/' + ROUTES.page0 + '.html',
            controller: 'StudyHomeCtrl'
        })
        .when('/' + ROUTES.page1, {
            templateUrl: 'Views/' + ROUTES.page1 + '.html',
            controller: 'RentingCtrl'
        })
        .when('/' + ROUTES.page2, {
            templateUrl: 'Views/' + ROUTES.page2 + '.html',
            controller: 'MessagesCtrl'
        })
        .when('/' + ROUTES.page3, {
            templateUrl: 'Views/' + ROUTES.page3 + '.html',
            controller: 'LoginCtrl'
        })
        .when('/' + ROUTES.page4, {
            templateUrl: 'Views/' + ROUTES.page4 + '.html',
            controller: 'RegisterCtrl'
        })
        .when('/' + ROUTES.page5, {
            templateUrl: 'Views/' + ROUTES.page5 + '.html',
            controller: 'DetailsCtrl'
        })
        .when('/' + ROUTES.page6, {
            templateUrl: 'Views/' + ROUTES.page6 + '.html',
            controller: 'AGBCtrl'
        })
        .when('/' + ROUTES.page7, {
            templateUrl: 'Views/' + ROUTES.page7 + '.html',
            controller: 'ContactAndHelpCtrl'
        })
        .when('/' + ROUTES.page8, {
            templateUrl: 'Views/' + ROUTES.page8 + '.html',
            controller: 'AboutUsCtrl'
        })
        .when('/' + ROUTES.page9, {
            templateUrl: 'Views/' + ROUTES.page9 + '.html',
            controller: 'ImpressumCtrl'
        })
        .when('/' + ROUTES.page10, {
            templateUrl: 'Views/' + ROUTES.page10 + '.html',
            controller: 'ApartmentsCtrl'
        })
        .when('/' + ROUTES.page11, {
            templateUrl: 'Views/' + ROUTES.page11 + '.html',
            controller: 'HomeStartCtrl'
        })
        .when('/' + ROUTES.page12, {
            templateUrl: 'Views/' + ROUTES.page12 + '.html',
            controller: 'FavouritesCtrl'
        })
        .when('/' + ROUTES.page13, {
            templateUrl: 'Views/' + ROUTES.page13 + '.html',
            controller: 'MyDataCtrl'
        })
        .when('/' + ROUTES.page14, {
            templateUrl: 'Views/' + ROUTES.page14 + '.html',
            controller: 'ManageAccountCtrl'
        })
        .when('/' + ROUTES.page15, {
            templateUrl: 'Views/' + ROUTES.page15 + '.html',
            controller: 'SavedSearchesCtrl'
        })
        .when('/' + ROUTES.page16, {
            templateUrl: 'Views/' + ROUTES.page16 + '.html',
            controller: 'ViewingAppointmentsCtrl'
        })
        .when('/' + ROUTES.page17, {
            templateUrl: 'Views/' + ROUTES.page17 + '.html',
            controller: 'EditApartmentCtrl'
        })
        .otherwise({
            redirectTo: '/' + ROUTES.page11
        });
}]);

// Navigation Controller
studyHomeApp.controller('NavCtrl', ['$scope', '$location', function($scope, $location){
    $scope.routes = ROUTES;
    $scope.lastNavItem = ROUTES.page0;

    $scope.getCurrentNavItem = function(path) {
        switch(path) {
            case '/' + ROUTES.page0:
                return ROUTES.page0;
            case '/' + ROUTES.page1:
                return ROUTES.page1;
            case '/' + ROUTES.page2:
                return ROUTES.page2;
            case '/' + ROUTES.page3:
                return ROUTES.page3;
            case '/' + ROUTES.page4:
                return ROUTES.page4;
            /*case '/' + ROUTES.page5:
                return ROUTES.page5;*/
            case '/' + ROUTES.page6:
                return ROUTES.page6;
            case '/' + ROUTES.page7:
                return ROUTES.page7;
            case '/' + ROUTES.page8:
                return ROUTES.page8;
            case '/' + ROUTES.page9:
                return ROUTES.page9;
            case '/' + ROUTES.page10:
                return ROUTES.page10;
            case '/' + ROUTES.page11:
                return ROUTES.page11;
            case '/' + ROUTES.page12:
                return ROUTES.page12;
            case '/' + ROUTES.page13:
                return ROUTES.page13;
            case '/' + ROUTES.page14:
                return ROUTES.page14;
            case '/' + ROUTES.page15:
                return ROUTES.page15;
            case '/' + ROUTES.page16:
                return ROUTES.page16;
            case '/' + ROUTES.page17:
                return ROUTES.page17;
            default:
                return $scope.lastNavItem;
        }
    }

    $scope.currentNavItem = $scope.getCurrentNavItem($location.$$path);

    //console.log($location.$$path);
    $scope.printStatus = function(page) {
        $scope.status = "Goto " + page;
    };
    $scope.goto = function ( path ) {
        $location.path( path );
        showExternalNavContent();
    };
    $scope.$on('$routeChangeSuccess', function(){
        $scope.currentNavItem = $scope.getCurrentNavItem($location.$$path);
        $scope.lastNavItem = $scope.currentNavItem;
        updateNavBar($scope.currentNavItem);
    });

    //addArrow();
}]);

function updateNavBar(currentNavItem) {
    if(document.getElementById("nav-refresh")) {
        document.getElementById("nav-refresh").setAttribute("md-selected-nav-item", currentNavItem);
    }

}

// some helper functions for hiding/showing external nav content
function toggleExternalNavContent() {
    if(document.getElementById("disable-ext-cnt")) {
        document.getElementById("disable-ext-cnt").setAttribute("id", "show-ext-cnt");
        spaceMainContentDownwards();
    }
    else {
        document.getElementById("show-ext-cnt").setAttribute("id", "disable-ext-cnt");
        spaceMainContentUpwards();
    }
}

function showExternalNavContent() {
    if(document.getElementById("disable-ext-cnt")) {
        document.getElementById("disable-ext-cnt").setAttribute("id", "show-ext-cnt");
        spaceMainContentDownwards();
    }
}

function hideExternalNavContent() {
    if(document.getElementById("show-ext-cnt")) {
        document.getElementById("show-ext-cnt").setAttribute("id", "disable-ext-cnt");
        spaceMainContentUpwards();
    }

}

// helper functions to make main content fitting in when external navbar content disappears
function spaceMainContent() {
    if(document.getElementById("main-ext-nav-cnt-show")) {
        document.getElementById("main-ext-nav-cnt-show").setAttribute("id", "main-ext-nav-cnt-hidden");
    }
    else {
        document.getElementById("main-ext-nav-cnt-hidden").setAttribute("id", "main-ext-nav-cnt-show");
    }
}

function spaceMainContentDownwards() {
    if(document.getElementById("main-ext-nav-cnt-hidden")) {
        document.getElementById("main-ext-nav-cnt-hidden").setAttribute("id", "main-ext-nav-cnt-show");
    }
}

function spaceMainContentUpwards() {
    if(document.getElementById("main-ext-nav-cnt-show")) {
        document.getElementById("main-ext-nav-cnt-show").setAttribute("id", "main-ext-nav-cnt-hidden");
    }
}

//arrow soll ein pfeil sein um den external content zu togglen, kann danna für das ein und ausblenden der suchkriterien
//genutzt werden
//arrow image werde ich noch adden! die function kann ich ja schon mal lassen, mein image ist einfach noch zu schlecht
//helper function for arrow
function addArrow(){
    let node = document.createElement("img");
    node.setAttribute("src", "arrow.png");
    node.setAttribute("alt", "arrow");
    node.setAttribute("id", "arrow");
    node.addEventListener("click", function(){
        toggleExternalNavContent();
    });
    let container = document.getElementById("md-content-ins-b4");
    container.insertBefore(node, container.childNodes[6]);
}
