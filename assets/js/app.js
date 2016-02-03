var app = angular.module('app', ['ngRoute']);

app.config(function($routeProvider, $locationProvider) {
    
    $routeProvider.when('/', {
        templateUrl: swift.templates + '/home.html',
        controller: 'Home'
    });

    $locationProvider.html5Mode(true);

});

app.controller('Home', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/posts').success(function(res) {
        $scope.posts = res;
    });
});