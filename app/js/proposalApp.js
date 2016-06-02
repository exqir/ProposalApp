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
    .when('/organizations', {
      templateUrl: 'partials/organizations_table.html',
      controller: 'organizationListCtrl'
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
.controller('proposalListCtrl', function($scope, $http, $uibModal, filterFilter,searchTerm){
  $http.get("./../restEndpoint/proposals")
    .then(function (response) {
      $scope.proposals = response.data;
    });
  $scope.search = searchTerm;
  $scope.org = [];
  $scope.raw = [];

  $scope.includeOrg = function(orgName) {
      var i = $scope.org.indexOf(orgName);
      if (i > -1) {
          $scope.org.splice(i, 1);
      } else {
          $scope.org.push(orgName);
      }
  };

  $scope.orgFilter = function(proposal) {
      if ($scope.org.length > 0) {
          if ($scope.org.indexOf(proposal.orgName) < 0)
              return;
      }

      return proposal;
  };

  $scope.includeRaw = function(raw) {
      var i = $scope.raw.indexOf(raw);
      if (i > -1) {
          $scope.raw.splice(i, 1);
      } else {
          $scope.raw.push(raw);
      }
  };

  $scope.rawFilter = function(proposal) {
      if ($scope.raw.length > 0) {
          if ($scope.raw.indexOf(proposal.Raw) < 0)
              return;
      }

      return proposal;
  };

  $scope.getCountOrg = function(exp){
    return filterFilter( $scope.proposals, {orgName:exp}).length;
  };

  $scope.getCountRaw = function(exp){
    return filterFilter( $scope.proposals, {Raw:exp}).length;
  };

  $scope.open = function(proposalID)
  {
      var modalInstance = $uibModal.open
      ({
          animation: true,
          templateUrl: 'partials/proposalModal.html',
          controller: 'proposalDetailCtrl',
          size: 'lg',
          resolve: { id: function() {return proposalID;}}
      });

      // modalInstance.result.then(function(resultV)
      // {
      //     $scope.chartTypes = resultV.chartTypes;
      //     $scope.routeSelection = resultV.route;
      //     $scope.chartType = $scope.chartTypes[0];
      //     $scope.goTo(true);
      // });
  };
})
.controller('proposalDetailCtrl', function($scope, $http, $routeParams, $uibModalInstance, id){
  var proposalID = id;
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
  $scope.cancel = function()
  {
      $uibModalInstance.dismiss();
  };
})
.controller('organizationListCtrl', function($scope, $http){
  $http.get("./../restEndpoint/organizations")
    .then(function (response) {
      $scope.organizations = response.data;
    });

    $scope.type = [];
    $scope.includeType = function(type) {
        var i = $scope.type.indexOf(type);
        if (i > -1) {
            $scope.type.splice(i, 1);
        } else {
            $scope.type.push(type);
        }
    };

    $scope.typeFilter = function(organization) {
        if ($scope.type.length > 0) {
            if ($scope.type.indexOf(organization.Abbrev) < 0)
                return;
        }

        return organization;
    };
});
