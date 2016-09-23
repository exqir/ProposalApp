( ->

  config = ($routeProvider) ->
    config.$inject['$routeProvider']

    $routeProvider
    .when('/', {
      templateUrl: 'partials/_dashboard.html',
      controller: 'dashboardCtrl',
      controllerAs: 'dash'
    })
    .when('/dashboard', {
      templateUrl: 'partials/_dashboard.html',
      controller: 'dashboardCtrl',
      controllerAs: 'dash'
    })
    .when('/proposals', {
      templateUrl: 'partials/_proposalList.html',
      controller: 'proposalListCtrl',
      controllerAs: 'pTable'
    })
    .when('/organizations', {
      templateUrl: 'partials/_organizationList.html',
      controller: 'organizationListCtrl',
      controllerAs: 'vm'
    })
    .otherwise({redirectTo: '/'})

  angular
  .module('proposalApp',['ngRoute','ngSanitize','ngAnimate','ui.bootstrap','angular.filter'])
  .config(['$routeProvider', ($routeProvider) ->
    $routeProvider
    .when('/', {
      templateUrl: 'partials/_dashboard.html',
      controller: 'dashboardCtrl',
      controllerAs: 'dash'
    })
    .when('/dashboard', {
      templateUrl: 'partials/_dashboard.html',
      controller: 'dashboardCtrl',
      controllerAs: 'dash'
    })
    .when('/proposals', {
      templateUrl: 'partials/_proposalList.html',
      controller: 'proposalListCtrl',
      controllerAs: 'pTable'
    })
    .when('/organizations', {
      templateUrl: 'partials/_organizationList.html',
      controller: 'organizationListCtrl',
      controllerAs: 'vm'
    })
    .otherwise({redirectTo: '/'})
  ])
)()