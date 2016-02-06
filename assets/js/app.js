var app = angular.module('app', ['ngRoute', 'ngSanitize']);

app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {

    $routeProvider
        .when('/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Index'
        })
        .when('/:slug/', {
            templateUrl: swift.templates + '/single.html',
            controller: 'Single'
        })
        .when('/category/:category/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Category'
        })
        .when('/category/:category/page/:page/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Category'
        })
        .when('/page/:page/', {
            templateUrl: swift.templates + '/index.html',
            controller: 'Index'
        })
        .otherwise({
            templateUrl: swift.templates + '/404.html',
            controller: '404'
        });

    $locationProvider.html5Mode(true);

}]);

app.controller('Index', ['$scope', '$http', '$routeParams', '$wpService', function($scope, $http, $routeParams, $wpService) {
    $wpService.getAllCategories();
    $scope.data = $wpService;

    var currentPage = !$routeParams.page ? 1 : parseInt($routeParams.page);

    var request = swift.root + '/wp-json/wp/v2/posts?per_page=4';

    if ($routeParams.page) {
        request += '&page=' + $routeParams.page;
    }

    $http.get(request).success(function(res, status, headers) {
        $scope.posts = res;
        $scope.currentPage = currentPage;
        $scope.totalPages = headers('x-wp-totalpages');
    });
}]);

app.controller('404', function() {
    document.querySelector('title').innerHTML = 'Page not found | ' + swift.site_name;
});

app.controller('Category', ['$scope', '$http', '$routeParams', '$wpService', function($scope, $http, $routeParams, $wpService) {
    $wpService.getAllCategories();
    $scope.data = $wpService;

    $http.get(swift.root + '/wp-json/wp/v2/categories?slug=' + $routeParams.category).success(function(res) {
        $scope.current_category = res[0];
        document.querySelector('title').innerHTML = 'Category: ' + res[0].name + ' | ' + swift.site_name;

        var currentPage = !$routeParams.page ? 1 : parseInt($routeParams.page);

        var request = swift.root + '/wp-json/wp/v2/posts?per_page=4&filter[category_name]=' + res[0].slug;

        if ($routeParams.page) {
            request += '&page=' + $routeParams.page;
        }

        $http.get(request).success(function(res, status, headers) {
            $scope.posts = res;
            $scope.currentPage = currentPage;
            $scope.totalPages = headers('x-wp-totalpages');
        });
    });


}]);

app.controller('Single', ['$scope', '$http', '$routeParams', function($scope, $http, $routeParams) {
    $http.get(swift.root + '/wp-json/wp/v2/posts?filter[name]=' + $routeParams.slug).success(function(res) {
        $scope.post = res[0];
        document.querySelector('title').innerHTML = res[0].title.rendered + ' | ' + swift.site_name;
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
                $http.get(swift.root + '/wp-json/wp/v2/posts?per_page=-1&filter[s]=' + $scope.filter.s).success(function(res) {
                    $scope.posts = res;
                });
            };
        }
    };
});

app.directive('postsNavLink', function() {
    return {
        restrict: 'E',
        templateUrl: swift.templates + '/posts-nav-link.html',
        controller: ['$scope', '$element', '$routeParams', function($scope, $element, $routeParams) {
            var currentPage = !$routeParams.page ? 1 : parseInt($routeParams.page),
                linkPrefix = !$routeParams.category ? 'page/' : 'category/' + $routeParams.category + '/page/';

            $scope.postsNavLink = {
                prevLink: linkPrefix + (currentPage - 1),
                nextLink: linkPrefix + (currentPage + 1),
                sep: !$element.attr('sep')? '|' : $element.attr('sep'),
                prevLabel: !$element.attr('prev-label') ? 'Previous' : $element.attr('prev-label'),
                nextLabel: !$element.attr('next-label') ? 'Next' : $element.attr('next-label')
            };
        }]
    };
});

app.factory('$wpService', ['$http', function($http) {
    var WpService = {
        categories: []
    };

    WpService.getAllCategories = function() {
        if (WpService.categories.length) {
            return;
        }
        return $http.get(swift.root + '/wp-json/wp/v2/categories').success(function(res) {
            WpService.categories = res;
        });
    };

    return WpService;
}]);