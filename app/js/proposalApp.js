angular.module('proposalApp',[])
.controller('testCtrl', function($scope, $http){
  $http.get("http://localhost:8888/USI_Stellenausschreibung/restEndpoint/proposals")
    .then(function (response) {
      $scope.proposals = response.data;
    });
});
