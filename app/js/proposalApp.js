angular.module('proposalApp',['ngRoute','ngSanitize','ngAnimate','ui.bootstrap','angular.filter'])
.config(['$routeProvider',
  function($routeProvider){
    $routeProvider.
    when('/', {
      redirectTo: '/'
    })
    .when('/proposals', {
      templateUrl: 'partials/proposal_table.html',
      controller: 'proposalListCtrl'
    })
    .when('/proposals/:id', {
      templateUrl: 'partials/proposal_detail.html',
      controller: 'proposalDetailCtrl'
    })
    .otherwise({redirectTo: '/'});
  }
])
.filter('convertSQLdate', function () {
     return function (dateString) {
      var t = dateString.split(/[- :]/);
      var dateObject = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
      return dateObject;
     };
})
.filter('convertTenure', function() {
  return function (int) {
    if(int === "1") return "mit Tenure";
    else if (int === "-1") return "ohne Tenure";
    else return "";
  };
})
.filter('convertToStatus', function() {
  return function (int) {
    if(int === "1") return "<span class='label label-warning'>Raw</span>";
    else if(int === "2") return "<span class='label label-info'>Edited</span>";
    else if(int === "3") return "<span class='label label-danger'>Imported</span>";
    else return "<span class='label label-success'>Manual</span>";
  };
})
.filter('convertToName', function() {
  return function (int,name) {
    if(int === "1") return name;
    else return "";
  };
})
.factory('searchTerm', function () {
    return "";
})
.controller('header', function($scope, searchTerm) {
  $scope.search = searchTerm;
  $scope.goSearch = function() {
    searchTerm = $scope.search;
  };
})
.controller('proposalListCtrl', function($scope, $http, searchTerm){
  $http.get("./../restEndpoint/proposals")
    .then(function (response) {
      $scope.proposals = response.data;
    });
  $scope.search = searchTerm;
})
.controller('proposalDetailCtrl', function($scope, $http, $routeParams){
  var proposalID = $routeParams.id;
  $http.get("./../restEndpoint/proposals/" + proposalID)
    .then(function (response) {
      $scope.proposal = response.data;
    });
  $scope.editProposal = function(){
    $http.put("./../restEndpoint/proposals/" + proposalID, $scope.proposal)
    .then(function (response) {
      console.log(response);
    });
  };
});
