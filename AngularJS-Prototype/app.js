// for help visit:
// https://material.angularjs.org/latest/
// https://www.w3schools.com/angular/

"use strict";

// Routes
var ROUTES = {
    page0: "home",
    page1: "login",
    page2: "register",
    page3: "estates",
    page4: "profile",
    page5: "messages",
    page6: "advertise"
};

function getCurrentNavItem(path) {
    switch(path) {
        case '/home':
            return ROUTES.page0;
        case '/login':
            return ROUTES.page1;
        case '/register':
            return ROUTES.page2;
        case '/estates':
            return ROUTES.page3;
        case '/profile':
            return ROUTES.page4;
        case '/messages':
            return ROUTES.page5;
        case '/advertise':
            return ROUTES.page6;
    }
}

// App Module
var studyHomeApp = angular.module('studyHomeApp', ['ngRoute', 'ngMaterial'])
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
            controller: 'LoginCtrl'
        })
        .when('/' + ROUTES.page2, {
            templateUrl: 'Views/' + ROUTES.page2 + '.html',
            controller: 'RegisterCtrl'
        })
        .when('/' + ROUTES.page3, {
            templateUrl: 'Views/' + ROUTES.page3 + '.html',
            controller: 'EstatesCtrl'
        })
        .when('/' + ROUTES.page4, {
            templateUrl: 'Views/' + ROUTES.page4 + '.html',
            controller: 'ProfileCtrl'
        })
        .when('/' + ROUTES.page5, {
            templateUrl: 'Views/' + ROUTES.page5 + '.html',
            controller: 'MessagesCtrl'
        })
        .when('/' + ROUTES.page6, {
            templateUrl: 'Views/' + ROUTES.page6 + '.html',
            controller: 'AdvertiseCtrl'
        })
        .otherwise({
            redirectTo: '/' + ROUTES.page0
        });
}]);

// Navigation Controller
studyHomeApp.controller('NavCtrl', ['$scope', '$location', function($scope, $location){
    $scope.routes = ROUTES;
    $scope.currentNavItem = getCurrentNavItem($location.$$path);

    //console.log($location.$$path);
    $scope.printStatus = function(page) {
        $scope.status = "Goto " + page;
    };
    $scope.goto = function ( path ) {
        $location.path( path );
        showExternalNavContent();
    };
    $scope.$on('$routeChangeSuccess', function(){
        $scope.currentNavItem = getCurrentNavItem($location.$$path);
        updateNavBar($scope.currentNavItem);
    });
}]);

function updateNavBar(currentNavItem) {
    document.getElementById("nav-refresh").setAttribute("md-selected-nav-item", currentNavItem);
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