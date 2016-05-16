angular.module('proposalApp',['ngRoute','ngSanitize','ngAnimate'])
.config(['$routeProvider',
  function($routeProvider){
    $routeProvider.
    when('/', {
      templateUrl: 'partials/proposal_table.html',
      controller: 'proposalCtrl'
    }).
    otherwise({redirectTo: '/'});
  }
])
.filter('convertSQLdate', function () {
     return function (dateString) {
      var t = dateString.split(/[- :]/);
      var dateObject = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
      return dateObject;
     };
})
.filter('convertToYesOrNo', function(){
  return function (int) {
    if(int === "1") return "Ja";
    else return "Nein";
  };
})
.controller('proposalCtrl', function($scope, $http){
  $http.get("http://localhost:8888/ProposalApp/restEndpoint/proposals")
    .then(function (response) {
      $scope.proposals = response.data;
    });
});
