angular.module('proposalApp',['ngRoute','ngSanitize','ngAnimate','ui.bootstrap','angular.filter'])
.config(['$routeProvider',
  function($routeProvider){
    $routeProvider.
    when('/', {
      templateUrl: 'partials/dashboard.html',
      controller: 'dashboardCtrl'
    })
    .when('/dashboard', {
      templateUrl: 'partials/dashboard.html',
      controller: 'dashboardCtrl'
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
.service('restRessources', function($http){
  var route = "./../restEndpoint";
  this.getProposals = function() {
    return $http.get(route + "/proposals");
  };
  this.getProposal = function(proposalID) {
    return $http.get(route + "/proposals/" + proposalID);
  };
  this.putProposal = function(proposalID, proposal) {
    $http.put(route + "/proposals/" + proposalID, proposal);
  };
  this.getOrganizations = function() {
    return $http.get(route + "/organizations");
  };
  this.getOrganization = function(organizationID) {
    return $http.get(route + "/organizations/" + organizationID);
  };
  this.putOrganization = function(organizationID, organization) {
    $http.put(route + "/organizations/" + organizationID, organization);
  };
  this.getStatisticsOrganization = function() {
    return $http.get(route + "/statistics/organizations/");
  };
  this.getCultures = function() {
    return $http.get(route + "/subjects-lists/cultures/");
  };
  this.getAreas = function() {
    return $http.get(route + "/subjects-lists/areas/");
  };
  this.getSubjects = function() {
    return $http.get(route + "/subjects-lists/subjects/");
  };
})
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
.factory('freeSearch', function ($location) {
  var factory = {};
  factory.search = "";
  factory.setSearch = function(search) {$location.search('search', search);};
  return factory;
})
.controller('header', function($scope, freeSearch) {
  //$scope.search = "";
  $scope.goSearch = function(search) {freeSearch.setSearch(JSURL.stringify(search));};
})
.controller('proposalListCtrl', function($q, $scope, $http, $uibModal, $filter,
  $injector, $routeParams, filterFilter, freeSearch, restRessources){
  $scope.states = [];
  $scope.search = "";
  $scope.$on('$routeChangeSuccess', function() {
    $scope.search = JSURL.parse($routeParams.search);
  });
  var rest = $injector.get('restRessources');
  var promises = [];

  promises.push(rest.getProposals());
  promises.push(rest.getStatisticsOrganization());

  $q.when(promises[0])
  .then(function(result) {
    $scope.proposals = result.data;
  });
  $q.when(promises[1])
  .then(function(result) {
    $scope.organizations = result.data;
  });

  rest.getCultures()
  .then(function(result) {
    $scope.cultures = result.data;
  });
  rest.getAreas()
  .then(function(result) {
    $scope.areas = result.data;
  });
  rest.getSubjects()
  .then(function(result) {
    $scope.subjects = result.data;
  });


  $q.all(promises)
  .then(function (results) {
    var notFound = [];
    for(var i = 0; i < $scope.organizations.length; i++) {
      var found = false;
      for(var n = 0; n < $scope.proposals.length; n++) {
        if($scope.organizations[i].Name === $scope.proposals[n].orgName) {
          found = true;
          $scope.states.push($scope.organizations[i].State);
          break;
        }
      }
      if(found === false) notFound.push(i);
    }
    for (i = notFound.length - 1; i >= 0; i--) {
      $scope.organizations.splice(notFound[i],1);
    }
    $scope.states = $filter("unique")($scope.states);
  });

  $scope.org = [];
  $scope.raw = [];
  $scope.state = [];
  $scope.subjectCulture = [];
  $scope.subjectArea = [];
  $scope.subject = [];

  var filterInclude = function(array, element) {
    var i = $scope[array].indexOf(element);
    i > -1 ? $scope[array].splice(i,1) : $scope[array].push(element);
  };

  var filterRepeat = function(array, attribute, proposal) {
    if($scope[array].length > 0) {
      if($scope[array].indexOf(proposal[attribute]) < 0)
        return;
    }
    return proposal;
  };

  $scope.includeOrg = function (orgName) {filterInclude('org', orgName);};
  $scope.includeRaw = function (raw) {filterInclude('raw', raw);};
  $scope.includeState = function (state) {filterInclude('state', state);};
  $scope.includeSubjectCulture = function (culture) {filterInclude('subjectCulture', culture);};
  $scope.includeSubjectArea = function (area) {filterInclude('subjectArea', area);};
  $scope.includeSubject = function (subject) {filterInclude('subject', subject);};

  $scope.orgFilter = function (proposal) {return filterRepeat('org','orgName',proposal);};
  $scope.rawFilter = function (proposal) {return filterRepeat('raw','Raw',proposal);};
  $scope.stateFilter = function (proposal) {return filterRepeat('state','State',proposal);};
  $scope.cultureFilter = function (proposal) {return filterRepeat('subjectCulture','Culture',proposal);};
  $scope.areaFilter = function (proposal) {return filterRepeat('subjectArea','Area',proposal);};
  $scope.subjectFilter = function (proposal) {return filterRepeat('subject','Subject',proposal);};

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
.controller('proposalDetailCtrl', function($scope, $http, $routeParams, $uibModalInstance, $injector, restRessources, id){
  var proposalID = id;
  var rest = $injector.get('restRessources');
  rest.getProposal(proposalID)
    .then(function (response) {
      $scope.proposal = response.data;
    });
  $scope.editProposal = function(){
    rest.putProposal(proposalID, $scope.proposal)
    .then(function (response) {
      console.log(response);
    });
  };
  $scope.cancel = function()
  {
      $uibModalInstance.dismiss();
  };
})
.controller('organizationListCtrl', function($scope, $http, $injector, restRessources){
  var rest = $injector.get('restRessources');
  rest.getOrganizations()
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
})
.controller('dashboardCtrl', function($scope,$http, $filter, $injector, restRessources){
  var rest = $injector.get('restRessources');
  rest.getProposals()
    .then(function (response) {
      $scope.proposals = response.data;
    })
    .then(function () {
      //// TOTAL AND GERMANY TOTAL
      $scope.proposalsTotal = $scope.proposals.length;
      var proposalsGermany = $filter('filter')($scope.proposals, {Country: 'Deutschland'});
      $scope.proposalsGermanyTotal = proposalsGermany.length;
      //// STATES
      var stateNames = $filter('unique')(proposalsGermany, 'State');
      $scope.states = [];
      for (var i = 0; i < stateNames.length; i++) {
        var state = {};
        state.name = stateNames[i].State;
        if(state.name === null || state.name === '') state.name = "Unbestimmt";
        state.number = (($filter('filter')(proposalsGermany, {State: stateNames[i].State})).length);
        state.percentage = Math.round((state.number / $scope.proposalsGermanyTotal) * 100);
        $scope.states.push(state);
      }
      //// ORGANIZATIONS
      $scope.organizationsTotal = ($filter('unique')($scope.proposals, 'orgName')).length;
      var organizationTypes = $filter('unique')($scope.proposals, 'orgAbbrev');
      $scope.orgas = [];
      for (var n = 0; n < organizationTypes.length; n++) {
        var org = {};
        //TODO null handling
        org.type = organizationTypes[n].orgAbbrev;
        if(org.type === null) org.type = "Sonstige";
        org.number = (($filter('filter')($scope.proposals, {orgAbbrev: organizationTypes[n].orgAbbrev}, true)).length);
        org.percentage = Math.round((org.number / $scope.proposalsTotal) * 100);
        $scope.orgas.push(org);
      }
      //// BESOLDUNG
      $scope.besoldung = [];
      $scope.besoldung.push({
        type: 'W1',
        number: (($filter('filter')($scope.proposals, {W1: 1})).length),
        percentage: function() {return Math.round((this.number/$scope.proposalsTotal)*100);}
      });
      $scope.besoldung.push({
        type: 'W2',
        number: (($filter('filter')($scope.proposals, {W2: 1})).length),
        percentage: function() {return Math.round((this.number/$scope.proposalsTotal)*100);}
      });
      $scope.besoldung.push({
        type: 'W3',
        number: (($filter('filter')($scope.proposals, {W3: 1})).length),
        percentage: function() {return Math.round((this.number/$scope.proposalsTotal)*100);}
      });
    });
});
