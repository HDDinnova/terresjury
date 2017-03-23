angular
  .module('app')
  .config(routesConfig);

function routesConfig($stateProvider, $urlRouterProvider, $locationProvider, AUTH_EVENTS) {
  $locationProvider.html5Mode(true);
  $urlRouterProvider.otherwise('/');

  $stateProvider
    .state('login', {
      url: '/',
      templateUrl: 'app/login/hello.html',
      controller: 'LoginController',
      controllerAs: 'Login'
    })
    .state('dashboard', {
      url: '/dashboard',
      templateUrl: 'app/dashboard/index.html',
      controller: 'DashController',
      controllerAs: 'Dashboard',
      data: {
        authorize: [AUTH_EVENTS.loginSuccess]
      }
    })
    .state('tourfilm', {
      url: '/film/tourism/:id',
      templateUrl: 'app/tourfilm/film.html',
      controller: 'TourfilmController',
      controllerAs: 'Tourfilm'
    })
    .state('corpfilm', {
      url: '/film/corporate/:id',
      templateUrl: 'app/corporate/film.html',
      controller: 'CorporateController',
      controllerAs: 'Corporate'
    })
    .state('docfilm', {
      url: '/film/documentary/:id',
      templateUrl: 'app/documentary/film.html',
      controller: 'DocumentaryController',
      controllerAs: 'Documentary'
    });
}
