angular.module('app', ['ui.router', 'ngAnimate', 'angularUtils.directives.dirPagination', 'angularSpinners', 'ngAlertify', 'ngProgress', 'LocalStorageModule', 'ngResource', 'vcRecaptcha', 'ngTouch', 'angular-cache','mwl.calendar']);


function initializeApp($rootScope, localStorageService, $http, CacheFactory ){
    //console.log('app init');
    $rootScope.posts_per_page = config.POSTS_PAGE;
    //console.log(config.POSTS_PAGE);
    //console.log(config.API_URL);

     /** Localize Categories **/
        $http.get(config.API_URL + 'wp/v2/categories/?per_page=30', { cache: true }).success(function(res){
            var cats = [];
            $rootScope.cats = res;
            localStorageService.set( 'cats', cats );
        });
        if (!CacheFactory.get('navCache')) { CacheFactory.createCache('navCache')}
          /** Localize menu **/
        $http.get(config.API_URL + 'wp-api-menus/v2/menu-locations/header_menu', { cache: CacheFactory.get('navCache') }).success(function(res){
            var nav = [];
            $rootScope.nav = res;
           localStorageService.set( 'nav', nav );

        });
          /** Localize menu **/
        $http.get(config.API_URL + 'wp-api-menus/v2/menu-locations/footer_menu', { cache: CacheFactory.get('navFooterCache') }).success(function(res){
            var navFooter = [];
            $rootScope.navFooter = res;
           localStorageService.set( 'navFooter', navFooter );

        });
           /** Localize menu **/
        $http.get(config.API_URL + 'wp-api-menus/v2/menu-locations/chapters_menu', { cache: CacheFactory.get('navChaptersCache') }).success(function(res){
            var navChapters = [];
            $rootScope.navChapters = res;
           localStorageService.set( 'navChapters', navChapters );

        });
        if (!CacheFactory.get('optionsCache')) { CacheFactory.createCache('optionsCache')}
           /** Cards **/
        $http.get(config.API_URL + 'acf/v2/options', {cache: CacheFactory.get('optionsCache')}).success(function(res){
            var acf = [];
            $rootScope.acf = res.acf;
            localStorageService.set( 'acf', acf );
        });

}

/**
 *
 * @param $stateProvider
 * @param $locationProvider
 * @param $urlRouterProvider
 * @ngInject
 */
function routesConfig($stateProvider, $locationProvider, paginationTemplateProvider, $urlRouterProvider, localStorageServiceProvider, CacheFactoryProvider) {
    
    var wow;
    wow = new WOW({ boxClass:     'js-wow',      // default
                    animateClass: 'is-animated', // default
                    mobile:       false  // default
                })
    wow.init();


    angular.extend(CacheFactoryProvider.defaults, { maxAge: 1440 * 60 * 1000, deleteOnExpire: 'aggressive' });

    localStorageServiceProvider.setPrefix('wp');
    paginationTemplateProvider.setPath('common/directives/pagination/dirPagination.tpl.html');

    $urlRouterProvider.otherwise('/');
    $stateProvider
        .state('root', {
                url: '',
                abstract: true,
                views: {
                    'header': {
                        templateUrl: 'partials/layout/head.tpl.html',
                        controller: 'HeaderCtrl'
                    },
                    'footer': {
                        templateUrl: 'partials/layout/footer.tpl.html',
                        controller: 'HeaderCtrl'
                    }
                }
            })
            .state('root.home', {
                url: '/',
                views: {
                    'container@': {
                        templateUrl: 'partials/pages/home-page.tpl.html',
                        controller: 'HomeCtrl',
                        controllerAs: 'vm'
                    }
                }

            })
            .state('root.news', {
            url: "/news",
            views: {
                    'container@': {
                        templateUrl: 'partials/posts/news.tpl.html',
                        controller: 'NewsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.social', {
            url: "/connect",
            views: {
                    'container@': {
                        templateUrl: 'partials/pages/connect.tpl.html',
                        controller: 'ConnectCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.events', {
            url: "/events",
            views: {
                    'container@': {
                        templateUrl: 'partials/pages/events.tpl.html',
                        controller: 'EventsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.eventsCalendar', {
            url: "/events-calendar",
            views: {
                    'container@': {
                        templateUrl: 'partials/pages/events-calendar.tpl.html',
                        controller: 'EventsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.documents', {
            url: "/documents",
            views: {
                    'container@': {
                        templateUrl: 'partials/posts/documents.tpl.html',
                        controller: 'DocumentCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.documentsS', {
            url: "/documents/",
            views: {
                    'container@': {
                        templateUrl: 'partials/posts/documents.tpl.html',
                        controller: 'DocumentCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.documentPost',{
                url: '/documents/:title',
                views: {
                    'container@': {
                        templateUrl: 'partials/posts/document-post.tpl.html',
                        controller: 'DocumentCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.documentPostS',{
                url: '/documents/:title/',
                views: {
                    'container@': {
                        templateUrl: 'partials/posts/document-post.tpl.html',
                        controller: 'DocumentCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.contact', {
            url: "/contact",
            views: {
                    'container@': {
                        templateUrl: 'partials/pages/contact.tpl.html',
                        controller: 'ContactCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.newsBySearch', {
                url: "/news/search/:searchTerm",
                views: {
                 'container@': {
                        templateUrl: 'partials/posts/news.tpl.html',
                        controller: 'NewsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
             .state('root.tags',{
                url:'/tag/:term',
                views: {
                 'container@': {
                        templateUrl: 'partials/posts/category.tpl.html',
                        controller: 'TagsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.tagsS',{
                url:'/tag/:term/',
                views: {
                 'container@': {
                        templateUrl: 'partials/posts/category.tpl.html',
                        controller: 'TagsCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.category',{
                url:'/category/:term',
                views: {
                 'container@': {
                        templateUrl: 'partials/posts/category.tpl.html',
                        controller: 'CategoryCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.categoryS',{
                url:'/category/:term/',
                views: {
                 'container@': {
                        templateUrl: 'partials/posts/category.tpl.html',
                        controller: 'CategoryCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.search', {
                url: '/search',
                views: {
                    'container@': {
                        templateUrl: 'partials/pages/search.tpl.html', // Make Dynamic
                        controller: 'SearchCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.searchTerm', {
                url: '/search/:searchTerm',
                views: {
                    'container@': {
                        templateUrl: 'partials/pages/search.tpl.html', // Make Dynamic
                        controller: 'SearchCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.searchCat', {
                url: '/search/:searchCat/:searchTerm',
                views: {
                    'container@': {
                        templateUrl: 'partials/pages/search.tpl.html', // Make Dynamic
                        controller: 'SearchCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
            .state('root.404', {
                url: '/404',
                views: {
                    'container@': {
                        templateUrl: 'partials/pages/404.tpl.html' // Make Dynamic
                    }
                }
            })
            .state('root.newsPost',{
                url: '/{id:[0-9a-fA-F]}/:title',
                views: {
                    'container@': {
                        templateUrl: 'partials/posts/single-post.tpl.html',
                        controller: 'PostCtrl',
                        controllerAs: 'vm'
                    }
                }
            })
             .state('external',{
                url: '/wp/{path:.*}',
                external: true,
            })
             .state('external_wp',{
                url: '/wp-content/{path:.*}',
                external: true,
            })
            .state('root.single', {
                url: '*path',
                views: {
                    'container@': {
                        controller: 'PageCtrl',
                        controllerAs: 'vm',
                        template: '<div ng-include="pageTemplate()"></div>' // Make Dynamic
                    }
                }
            });
       
    $locationProvider.html5Mode(true).hashPrefix('!');

    $urlRouterProvider.rule(function ($injector, $location) {
        var slashHashRegex,
            matches,
            path = $location.url();

        // check to see if the path already has a slash where it should be
        if (path[path.length - 1] === '/' || path.indexOf('/?') > -1) {
            return path.substring(0, path.length - 1);
        }

        // if there is a trailing slash *and* there is a hash, remove the last slash so the route will correctly fire
        slashHashRegex = /\/(#[^\/]*)$/;
        matches = path.match(slashHashRegex);
        if (1 < matches.length) {
            return path.replace(matches[0], matches[1]);
        }
    });
}

var config = {
    // global constant config values live here
    ROOT_URL: '%%ROOT_URL%%',
    API_URL: '%%API_URL%%',
    POSTS_PAGE: '%%POSTS_PAGE%%'
};

function AppController($rootScope, $window, $location, $timeout, MetadataService) {
    var vm = this;

     $rootScope.$on('$stateChangeStart',
    function(event, toState, toParams, fromState, fromParams) {
      if (toState.external) {
          event.preventDefault();
          $window.open($location.url(), '_self');
      }
    });

    $rootScope.$watchCollection( function() {
        return MetadataService.getMetadata();
    }, function (meta) {
        if (typeof meta.title !== 'undefined') {
            $rootScope.meta = meta;
            $timeout(function () {
                // push event to google analytics. This is done in a $timeout
                // so the current $digest loop has a chance to actually update the
                // HTML with the correct page title etc. Check for localhost to prevent
                // dev sessions from being recorded in analytics.
                if ($location.host() !== 'localhost') {
                    $window.ga('send', 'pageview', {page: $location.path()});
                }
            });
        }
    });
}

angular
    .module('app')
    .run(initializeApp)
    .config(routesConfig)
    .constant('config', config)
    .controller('AppController', AppController);