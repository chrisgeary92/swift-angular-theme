var app = angular.module('app', ['ngRoute', 'ngSanitize']);

app.config(function($routeProvider, $locationProvider) {

    $routeProvider
        .when('/', {
            templateUrl: swift.templates + '/home.html',
            controller: 'Home'
        })
        .when('/:slug/', {
            templateUrl: swift.templates + '/single.html',
            controller: 'Single'
        });

    $locationProvider.html5Mode(true);

});

app.controller('Home', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/posts').success(function(res) {
        $scope.posts = res;
        document.querySelector('title').innerHTML = 'Home | ' + swift.site_name;
    });
});

app.controller('Single', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/posts?filter[name]=' + $routeParams.slug).success(function(res) {
        $scope.post = res[0];
        document.querySelector('title').innerHTML = res[0].title.rendered + ' | ' + swift.site_name;
    });
});

app.filter('toTrusted', ['$sce', function($sce) {
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]);

app.directive('swiftSearchForm', function() {
    return {
        restrict: 'E', // E = element, A = attribute, C = classname
        templateUrl: swift.templates + '/search-form.html',
        controller: function($scope, $http) {
            $scope.filter = {
                s: ''
            };
            $scope.search = function() {
                $http.get(swift.root + '/wp-json/wp/v2/posts?filter[s]=' + $scope.filter.s).success(function(res) {
                    $scope.posts = res;
                });
            };
        }
    };
});