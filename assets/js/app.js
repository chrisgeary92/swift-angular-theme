var app = angular.module('app', ['ngRoute', 'ngSanitize']);

app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {

    $routeProvider
        .when('/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Home'
        })
        .when('/:slug/', {
            templateUrl: swift.templates + '/single.html',
            controller: 'Single'
        })
        .when('/category/:category/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Category'
        })
        .otherwise({
            templateUrl: swift.templates + '/404.html'
        });

    $locationProvider.html5Mode(true);

}]);

app.controller('Home', ['$scope', '$http', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/categories').success(function(res) {
        $scope.categories = res;
    });
    $http.get(swift.root + '/wp-json/wp/v2/posts').success(function(res) {
        $scope.posts = res;
        document.querySelector('title').innerHTML = 'Home | ' + swift.site_name;
    });
}]);

app.controller('Single', ['$scope', '$http', '$routeParams', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/posts?filter[name]=' + $routeParams.slug).success(function(res) {
        $scope.post = res[0];
        document.querySelector('title').innerHTML = res[0].title.rendered + ' | ' + swift.site_name;
    });
}]);

app.controller('Category', ['$scope', '$http', '$routeParams', function($scope, $http, $routeParams) {
    
    $http.get(swift.root + '/wp-json/wp/v2/categories').success(function(res) {
        $scope.categories = res;
    });

    $http.get(swift.root + '/wp-json/wp/v2/categories?slug=' + $routeParams.category).success(function(res) {
        $scope.current_category = res[0];
        document.querySelector('title').innerHTML = 'Category: ' + res[0].name + ' | ' + swift.site_name;
        $http.get(swift.root + '/wp-json/wp/v2/posts?filter[category_name]=' + res[0].slug).success(function(res) {
            $scope.posts = res;
        });
    });

}]);

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