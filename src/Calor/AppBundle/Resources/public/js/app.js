'use strict';

var token = '';
var API_URL = 'http://calormeter.dev.wmoll.info/app_dev.php/api/';

var calorApp = angular.module('CalorMeterApp', ['ngMdIcons','mgcrea.ngStrap','ngRoute', 'ngCookies', 'angularLocalStorage']);

calorApp.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider.
            when('/login', {
                templateUrl: '/bundles/calorapp/views/login.html',
                controller: 'loginController'
            }).
            when('/signup', {
                templateUrl: '/bundles/calorapp/views/create-account.html',
                controller: 'signupController'
            }).
            when('/reset', {
                templateUrl: '/bundles/calorapp/views/reset.html',
                controller: 'resetController'
            }).
            when('/dashboard', {
                templateUrl: '/bundles/calorapp/views/dashboard.html',
                controller: 'dashboardController'
            }).
            when('/reports', {
                templateUrl: '/bundles/calorapp/views/reports.html',
                controller: 'reportsController'
            }).
            otherwise({
                templateUrl: '/bundles/calorapp/views/index.html',
                controller: 'indexController'
            });
    }])
    .config(function($timepickerProvider) {
        angular.extend($timepickerProvider.defaults, {
            timeFormat: 'HH:mm:ss',
            length: 9
        })
    })
    .config(function($datepickerProvider) {
        angular.extend($datepickerProvider.defaults, {
            dateFormat: 'yyyy-MM-dd',
            startWeek: 1
        });
    });


calorApp.directive("compareTo", function() {
    return {
        require: "ngModel",
        scope: {
            otherModelValue: "=compareTo"
        },
        link: function(scope, element, attributes, ngModel) {

            ngModel.$validators.compareTo = function(modelValue) {
                return modelValue == scope.otherModelValue;
            };

            scope.$watch("otherModelValue", function() {
                ngModel.$validate();
            });
        }
    };
});

calorApp.controller('indexController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {


}]);

calorApp.controller('signupController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {

    $scope.create = function (user) {

        $scope.inProcess = true;
        $scope.createErrors = [];
        $scope.errorCreate = '';
        var form = this;

        if ($scope.createAccountForm.$valid) {
            $http.post(API_URL + "v1/user", user, {
                headers: {'Content-Type': 'application/json charset=UTF-8'}
            })
                .success(function (response) {
                    if(response.status==='error'){
                        var errorsList = [];
                        for (var i = 0; i<response.errors.length; i++){
                            errorsList[response.errors[i].property_path] = response.errors[i].message;
                        }
                        $scope.createErrors = errorsList;
                    }else{
                        token = response.token;
                        changeRoute($scope, '#/dashboard');

                        $scope.guest = [];
                        $scope.confirm = '';
                        $scope.createAccountForm = [];
                    }
                    $scope.successMessage = "ok";
                    $scope.inProcess = false;
                })
                .error(function () {
                    $scope.errorCreate = "error";
                    $scope.inProcess = false;
                });
        } else {
            $scope.errorCreate = "Form invalid, please check and fix it";
            $scope.inProcess = false;
        }
    };

}]);

calorApp.controller('loginController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {

    $scope.login = function (user) {

        $scope.inProcess = true;
        $scope.errorLogin = '';

        if ($scope.loginForm.$valid) {
            $http.post(API_URL + "v1/session", user, {
                headers: {'Content-Type': 'application/json charset=UTF-8'}
            })
                .success(function (response) {
                    if(response.status==='error'){
                        var errorsList = [];
                        for (var i = 0; i<response.errors.length; i++){
                            errorsList[response.errors[i].property_path] = response.errors[i].message;
                        }
                        $scope.loginErrors = errorsList;
                    }else{
                        token = response.token;
                        changeRoute($scope, '#/dashboard');
                    }

                    $scope.inProcess = false;
                })
                .error(function () {
                    $scope.errorLogin = "error";
                    $scope.inProcess = false;
                });
        } else {
            $scope.errorLogin = "Form invalid, please check and fix it";
            $scope.inProcess = false;
        }
    };

}]);

calorApp.controller('resetController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {


}]);

calorApp.controller('dashboardController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {
    $scope.token = storage.get('token');
    if($scope.token.length == 0){changeRoute($scope, '#');}

    $scope.meal = {};
    $scope.meal.date = new Date();
    $scope.meal.time = new Date();
    $scope.meal.name = '';
    $scope.meal.calories = '0';


    $scope.saveMeal = function (meal) {

        $scope.inProcess = true;
        $scope.createErrors = [];
        $scope.errorSave = '';
        var form = this;

        if ($scope.addMealForm.$valid) {
            var data = meal;
            data.date = data.date.getFullYear()+'-'+('0' + (data.date.getMonth() + 1)).slice(-2)+'-'+('0' + data.date.getDate()).slice(-2);
            data.time = ('0' + data.time.getHours()).slice(-2)+':'+('0' + data.time.getMinutes()).slice(-2)+':'+('0' + data.time.getSeconds()).slice(-2);

            $http.post(API_URL + "v1/meals/"+token, data, {
                headers: {'Content-Type': 'application/json charset=UTF-8'}
            })
                .success(function (response) {
                    if(response.status==='error'){
                        var errorsList = [];
                        for (var i = 0; i<response.errors.length; i++){
                            errorsList[response.errors[i].property_path] = response.errors[i].message;
                        }
                        $scope.createErrors = errorsList;

                        $scope.meal.date = new Date(data.date);
                        $scope.meal.time = new Date(data.time);

                    }else{
                        $scope.meal = {};
                        $scope.meal.date = new Date();
                        $scope.meal.time = new Date();
                        $scope.meal.name = '';
                        $scope.meal.calories = '0';
                        $scope.getLogItems();
                    }
                    $scope.successMessage = "ok";
                    $scope.inProcess = false;
                })
                .error(function () {
                    $scope.errorSave = "error";
                    $scope.inProcess = false;
                });
        } else {
            $scope.errorSave = "Form invalid, please check and fix it";
            $scope.inProcess = false;
        }
    };


    $scope.meals = [];

    $scope.filter = {};

    $scope.getLogItems = function(){
        var apiRequest = {};
        if(typeof $scope.filter.fromDate !='undefined'){
            apiRequest.dateFrom = $scope.filter.fromDate.getFullYear()+'-'+('0' + ($scope.filter.fromDate.getMonth() + 1)).slice(-2)+'-'+('0' + $scope.filter.fromDate.getDate()).slice(-2);
        }
        if(typeof $scope.filter.untilDate !='undefined'){
            apiRequest.dateTo = $scope.filter.untilDate.getFullYear()+'-'+('0' + ($scope.filter.untilDate.getMonth() + 1)).slice(-2)+'-'+('0' + $scope.filter.untilDate.getDate()).slice(-2);
        }

        if(typeof $scope.filter.fromTime !='undefined'){
            apiRequest.timeFrom = ('0' + $scope.filter.fromTime.getHours()).slice(-2)+':'+('0' + $scope.filter.fromTime.getMinutes()).slice(-2)+':'+('0' + $scope.filter.fromTime.getSeconds()).slice(-2);
        }
        if(typeof $scope.filter.untilTime !='undefined'){
            apiRequest.timeTo = ('0' + $scope.filter.untilTime.getHours()).slice(-2)+':'+('0' + $scope.filter.untilTime.getMinutes()).slice(-2)+':'+('0' + $scope.filter.untilTime.getSeconds()).slice(-2);
        }


        var page = 1;
        var per_page = 250;
        $scope.requestProcess = 1;

        $http.get(API_URL + "v1/meals/"+token+"/"+page+"/"+per_page+"", {
            headers: {'Content-Type': 'application/json charset=UTF-8'},
            params:apiRequest
        })
            .success(function (response) {
                if(response.status==='error'){
                    $scope.createErrors = response.message;
                }else{
                    $scope.meals = response;
                }
                $scope.requestProcess = 0;
            })
            .error(function () {
                $scope.errorMessage = "error";
                $scope.requestProcess = 0;
            });
    }

    $scope.getLogItems();

    $scope.applyFilter = function(){
        $scope.getLogItems();
    }

    $scope.resetFilter = function(){
        $scope.filter = {};
        $scope.getLogItems();
    }

    $scope.removeMeal = function(id){

        $http.delete(API_URL + "v1/meal/"+$scope.token+"/"+id, {
            headers: {'Content-Type': 'application/json charset=UTF-8'}
        })
            .success(function (response) {
                $scope.getLogItems();
            })
            .error(function () {
                $scope.errorLogin = "error";
                $scope.inProcess = false;
            });
        return false;
    }

    $scope.showEditForm = 0;
    $scope.editedMeal = {};
    $scope.editMeal = function(meal){
        $scope.showEditForm = 1;
        $scope.editedMeal.date = new Date(meal[0].date);
        $scope.editedMeal.time = meal[0].time;
        $scope.editedMeal.name = meal[0].name;
        $scope.editedMeal.calories = meal[0].calories;
        $scope.editedMeal.id = meal.id;
    }

    $scope.updateMeal = function(meal){
        $scope.inProcess = true;
        $scope.createErrors = [];
        $scope.errorSave = '';
        var form = this;

        if ($scope.editMealForm.$valid) {
            var data = meal;
            data.date = data.date.getFullYear()+'-'+('0' + (data.date.getMonth() + 1)).slice(-2)+'-'+('0' + data.date.getDate()).slice(-2);

            $http.put(API_URL + "v1/meal/"+token+"/"+data.id, data, {
                headers: {'Content-Type': 'application/json charset=UTF-8'}
            })
                .success(function (response) {
                    if(response.status==='error'){
                        var errorsList = [];
                        for (var i = 0; i<response.errors.length; i++){
                            errorsList[response.errors[i].property_path] = response.errors[i].message;
                        }
                        $scope.createErrors = errorsList;
                        $scope.meal.date = new Date(data.date);
                        $scope.meal.time = new Date(data.time);

                    }else{
                        $scope.editedMeal = {};
                        $scope.showEditForm = 0;
                        $scope.getLogItems();
                    }
                    $scope.successMessage = "ok";
                    $scope.inProcess = false;
                })
                .error(function () {
                    $scope.errorSave = "error";
                    $scope.inProcess = false;
                });
        } else {
            $scope.errorSave = "Form invalid, please check and fix it";
            $scope.inProcess = false;
        }
    }

}]);


calorApp.controller('reportsController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {
    $scope.token = storage.get('token');
    if($scope.token.length == 0){changeRoute($scope, '#');}
    var apiRequest = {};

    $scope.report = [];



    $scope.mode = 'daily';


    $http.get(API_URL + "v1/reports/"+token+"/"+$scope.mode, {
        headers: {'Content-Type': 'application/json charset=UTF-8'},
        params:apiRequest
    })
        .success(function (response) {
            if(response.status==='error'){
                $scope.createErrors = response.message;
            }else{
                $scope.report = response.report;
            }
            $scope.requestProcess = 0;
        })
        .error(function () {
            $scope.errorMessage = "error";
            $scope.requestProcess = 0;
        });


    $scope.user = {};
    $http.get(API_URL + "v1/user/"+token, {
        headers: {'Content-Type': 'application/json charset=UTF-8'},
        params:apiRequest
    })
        .success(function (response) {
            if(response.status==='error'){
                $scope.createErrors = response.message;
            }else{
                $scope.user = response.user;
            }
            $scope.requestProcess = 0;
        })
        .error(function () {
            $scope.errorMessage = "error";
            $scope.requestProcess = 0;
        });


    $scope.saveUser = function(){
        $scope.inProcess = true;
        $scope.createErrors = [];
        $scope.errorSave = '';
        var form = this;


        $http.put(API_URL + "v1/user/"+token, $scope.user, {
            headers: {'Content-Type': 'application/json charset=UTF-8'}
        })
            .success(function (response) {
                $scope.successMessage = "ok";
                $scope.inProcess = false;
            })
            .error(function () {
                $scope.errorSave = "error";
                $scope.inProcess = false;
            });

    }
}]);


calorApp.controller('HeaderController', ['$scope', '$http', 'storage', function ($scope, $http, storage) {
    storage.bind($scope, 'token', '');
    if ($scope.token.length > 0) {
        token = $scope.token;
    }

    $scope.$watch(function () {
        return token
    }, function (value) {
        $scope.token = value;
        if($scope.token.length != 0) {
            $scope.logined = true;
        }else{
            $scope.logined = false;
        }
    });

    $scope.logout = function(){
        $http.delete(API_URL + "v1/session/"+$scope.token, {
            headers: {'Content-Type': 'application/json charset=UTF-8'}
        })
            .success(function (response) {
                token = '';
                storage.clearAll();
                changeRoute($scope, '#');
            })
            .error(function () {
                $scope.errorLogin = "error";
                $scope.inProcess = false;
            });
        return false;
    }
}]);






var changeRoute = function($scope, url, forceReload) {
    $scope = $scope || angular.element(document).scope();
    if(forceReload || $scope.$$phase) { // that's right TWO dollar signs: $$phase
        window.location = url;
    } else {
        $location.path(url);
        $scope.$apply();
    }
};