angular.module('proposalApp',['ngRoute','ngSanitize','ngAnimate','ui.bootstrap','angular.filter'])
.config(['$routeProvider',
  function($routeProvider){
    $routeProvider.
    when('/', {
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
    .otherwise({redirectTo: '/'});
  }
])
.service('restRessources', function($http){
  var route = "./../backend/api";
  this.getProposals = function() {
    return $http.get(route + "/proposals");
  };
  this.getProposal = function(proposalID) {
    return $http.get(route + "/proposals/" + proposalID);
  };
  this.putProposal = function(proposalID, proposal) {
    return $http.put(route + "/proposals/" + proposalID, proposal);
  };
  this.getOrganizations = function() {
    return $http.get(route + "/organizations");
  };
  this.getOrganization = function(organizationID) {
    return $http.get(route + "/organizations/" + organizationID);
  };
  this.putOrganization = function(organizationID, organization) {
    return $http.put(route + "/organizations/" + organizationID, organization);
  };
  this.getStatisticsOrganization = function() {
    return $http.get(route + "/statistics/organizations/");
  };
  this.getUsedOrganizations = function() {
    return $http.get(route + "/statistics/organizations/used/");
  };
  this.getUsedStates = function() {
    return $http.get(route + "/statistics/organizations/states/used/");
  };
  this.getCultures = function() {
    return $http.get(route + "/subjects/cultures/");
  };
  this.getAreas = function() {
    return $http.get(route + "/subjects/areas/");
  };
  this.getSubjects = function() {
    return $http.get(route + "/subjects/subjects/");
  };
  this.getTypeIds = function() {
    return $http.get(route + "/statistics/organization-types/");
  };
  this.getProposalCount = function() {
    return $http.get(route + "/statistics/proposals/").then(function(response) {return response.data.number});
  };
  this.getProposalCountByCountry = function(country) {
    return $http.get(route + "/statistics/proposals/" + country).then(function(response) {return response.data.number});
  };
  this.getStatesByCountry = function(country) {
    return $http.get(route + "/statistics/organizations/states/used/" + country);
  };
  this.setAlias = function(id, mainOrg) {
    return $http.put(route + "/organizations/" + id + "/merge/" + mainOrg);
  };
  this.getAliases = function(id) {
    return $http.get(route + "/organizations/" + id + "/alias/");
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
});
// .controller('header', function($scope, freeSearch) {
//   //TODO delete function to reset the search phrase and filtering
//   $scope.goSearch = function(search) {freeSearch.setSearch(JSURL.stringify(search));};
//   $scope.resetSearch = function() {
//     freeSearch.setSearch("");
//     $scope.search = "";
//   };
// })
// .controller('proposalListCtrl', function($q, $scope, $http, $uibModal, $filter,
//   $injector, $routeParams, filterFilter, freeSearch, restRessources){
//   $scope.states = [];
//   $scope.search = "";
//   $scope.$on('$routeChangeSuccess', function() {
//     $scope.search = JSURL.parse($routeParams.search);
//   });
//   var rest = $injector.get('restRessources');
//   var promises = [];
//
//   promises.push(rest.getProposals());
//   promises.push(rest.getUsedOrganizations());
//
//   $q.when(promises[0])
//   .then(function(result) {
//     $scope.proposals = result.data;
//   });
//   $q.when(promises[1])
//   .then(function(result) {
//     $scope.organizations = result.data;
//   });
//
//   rest.getUsedStates()
//   .then(function(result) {
//     $scope.states = result.data;
//   })
//   rest.getCultures()
//   .then(function(result) {
//     $scope.cultures = result.data;
//   });
//   rest.getAreas()
//   .then(function(result) {
//     $scope.areas = result.data;
//   });
//   rest.getSubjects()
//   .then(function(result) {
//     $scope.subjects = result.data;
//   });
//
//   // $q.all(promises)
//   // .then(function (results) {
//   //   var notFound = [];
//   //   for(var i = 0; i < $scope.organizations.length; i++) {
//   //     var found = false;
//   //     for(var n = 0; n < $scope.proposals.length; n++) {
//   //       if($scope.organizations[i].Name === $scope.proposals[n].orgName) {
//   //         found = true;
//   //         $scope.states.push($scope.organizations[i].State);
//   //         break;
//   //       }
//   //     }
//   //     if(found === false) notFound.push(i);
//   //   }
//   //   for (i = notFound.length - 1; i >= 0; i--) {
//   //     $scope.organizations.splice(notFound[i],1);
//   //   }
//   //   $scope.states = $filter("unique")($scope.states);
//   // });
//
//   $scope.org = [];
//   $scope.raw = [];
//   $scope.state = [];
//   $scope.subjectCulture = [];
//   $scope.subjectArea = [];
//   $scope.subject = [];
//
//   var filterInclude = function(array, element) {
//     var i = $scope[array].indexOf(element);
//     i > -1 ? $scope[array].splice(i,1) : $scope[array].push(element);
//   };
//
//   var filterRepeat = function(array, attribute, proposal) {
//     if($scope[array].length > 0) {
//       if($scope[array].indexOf(proposal[attribute]) < 0)
//         return;
//     }
//     return proposal;
//   };
//
//   $scope.includeOrg = function (orgName) {filterInclude('org', orgName);};
//   $scope.includeRaw = function (raw) {filterInclude('raw', raw);};
//   $scope.includeState = function (state) {filterInclude('state', state);};
//   $scope.includeSubjectCulture = function (culture) {filterInclude('subjectCulture', culture);};
//   $scope.includeSubjectArea = function (area) {filterInclude('subjectArea', area);};
//   $scope.includeSubject = function (subject) {filterInclude('subject', subject);};
//
//   $scope.orgFilter = function (proposal) {return filterRepeat('org','orgName',proposal);};
//   $scope.rawFilter = function (proposal) {return filterRepeat('raw','Raw',proposal);};
//   $scope.stateFilter = function (proposal) {return filterRepeat('state','State',proposal);};
//   $scope.cultureFilter = function (proposal) {return filterRepeat('subjectCulture','Culture',proposal);};
//   $scope.areaFilter = function (proposal) {return filterRepeat('subjectArea','Area',proposal);};
//   $scope.subjectFilter = function (proposal) {return filterRepeat('subject','Subject',proposal);};
//
//   $scope.getCountOrg = function(exp){
//     return filterFilter( $scope.proposals, {orgName:exp}).length;
//   };
//
//   $scope.getCountRaw = function(exp){
//     return filterFilter( $scope.proposals, {Raw:exp}).length;
//   };
//
//
//   $scope.open = function(proposalID)
//   {
//       var modalInstance = $uibModal.open
//       ({
//           animation: true,
//           templateUrl: 'partials/_proposalModal.html',
//           controller: 'proposalDetailCtrl',
//           size: 'lg',
//           resolve: { id: function() {return proposalID;}}
//       });
//
//       modalInstance.result.then(function()
//       {
//         //TODO reload or infuse changes
//       });
//   };
// })
// .controller('proposalDetailCtrl', function($scope, $http, $routeParams, $uibModalInstance, $injector, restRessources, id){
//   var proposalID = id;
//   var rest = $injector.get('restRessources');
//   var lookup = {};
//   var getLookupObject = function(array,attribute) {
//     var lookup = {};
//     for (var i = 0; i < array.length; i++) {
//       lookup[array[i][attribute]] = array[i];
//     }
//     return lookup;
//   };
//   rest.getProposal(proposalID)
//     .then(function (response) {
//       $scope.proposal = response.data;
//       rest.getOrganizations()
//       .then(function(result) {
//         $scope.organizations = result.data;
//         var orgLookup = getLookupObject($scope.organizations, "ID");
//         $scope.selectedOrg = orgLookup[$scope.proposal.OrgID];
//       });
//       rest.getCultures()
//       .then(function(result) {
//         $scope.cultures = result.data;
//         var cultureLookup = getLookupObject($scope.cultures, "ID");
//         $scope.selectedCulture = cultureLookup[$scope.proposal.subject_culture];
//       });
//       rest.getAreas()
//       .then(function(result) {
//         $scope.areas = result.data;
//         var areaLookup = getLookupObject($scope.areas, "ID");
//         $scope.selectedArea = areaLookup[$scope.proposal.subject_area];
//       });
//       rest.getSubjects()
//       .then(function(result) {
//         $scope.subjects = result.data;
//         var subjectLookup = getLookupObject($scope.subjects, "ID");
//         $scope.selectedSubject = subjectLookup[$scope.proposal.subject];
//       });
//   });
//   $scope.editProposal = function(){
//     console.log($scope.proposal);
//     rest.putProposal(proposalID, $scope.proposal)
//     .then(function (response) {
//       console.log(response);
//       if(response.status === 200) $uibModalInstance.dismiss();
//     });
//   };
//   $scope.cancel = function()
//   {
//       $uibModalInstance.dismiss();
//   };
//   $scope.setOrg = function() {
//     $scope.proposal.OrgID = $scope.selectedOrg.ID;
//   };
//   $scope.setCulture = function() {
//     $scope.proposal.subject_culture = $scope.selectedCulture.ID;
//   };
// })
// .controller('organizationListCtrl', function($scope, $http, $injector, $routeParams ,$uibModal ,restRessources){
//   var rest = $injector.get('restRessources');
//   $scope.$on('$routeChangeSuccess', function() {
//     $scope.search = JSURL.parse($routeParams.search);
//   });
//   rest.getOrganizations()
//     .then(function (response) {
//       $scope.organizations = response.data;
//     });
//     $scope.type = [];
//     $scope.includeType = function(type) {
//         var i = $scope.type.indexOf(type);
//         if (i > -1) {
//             $scope.type.splice(i, 1);
//         } else {
//             $scope.type.push(type);
//         }
//     };
//
//     $scope.typeFilter = function(organization) {
//         if ($scope.type.length > 0) {
//             if ($scope.type.indexOf(organization.Abbrev) < 0)
//                 return;
//         }
//
//         return organization;
//     };
//
//     $scope.open = function(orgID)
//     {
//         var modalInstance = $uibModal.open
//         ({
//             animation: true,
//             templateUrl: 'partials/_organizationModal.html',
//             controller: 'organizationDetailCtrl',
//             size: 'lg',
//             resolve: { id: function() {return orgID;}}
//         });
//
//         modalInstance.result.then(function()
//         {
//           //TODO reload or infuse changes
//         });
//     };
// })
// .controller('organizationDetailCtrl', function($scope, $http, $routeParams, $uibModalInstance, $injector, restRessources, id){
//   var orgID = id;
//   var rest = $injector.get('restRessources');
//   var lookup = {};
//   var getLookupObject = function(array,attribute) {
//     var lookup = {};
//     for (var i = 0; i < array.length; i++) {
//       lookup[array[i][attribute]] = array[i];
//     }
//     return lookup;
//   };
//   rest.getOrganization(orgID)
//     .then(function (response) {
//       $scope.organization = response.data;
//       rest.getTypeIds()
//       .then(function(result) {
//         $scope.types = result.data;
//         var typesLookup = getLookupObject($scope.types, "ID");
//         $scope.selectedType = typesLookup[$scope.organization.TypeID];
//       });
//       rest.getOrganizations()
//       .then(function(result) {
//         $scope.organizations = result.data;
//         //$scope.organizations = $filter("filter")($scope.organizations, {ID: orgID});
//       });
//       rest.getAliases(orgID)
//       .then(function(result) {
//         if(!Array.isArray(result.data)) {
//           $scope.aliases = [];
//           if(result.data !== null) $scope.aliases.push(result.data);
//         } else {
//           $scope.aliases = result.data;
//         }
//         //$scope.organizations = $filter("filter")($scope.organizations, {ID: orgID});
//       });
//   });
//   $scope.editOrganization = function(){
//     console.log($scope.organization);
//     rest.putOrganization(orgID, $scope.organization)
//     .then(function (response) {
//       console.log(response.data);
//       if(response.status === 200) $uibModalInstance.dismiss();
//     });
//   };
//   $scope.cancel = function()
//   {
//       $uibModalInstance.dismiss();
//   };
//   $scope.setType = function() {
//     $scope.organization.TypeID = $scope.selectedType.ID;
//     $scope.organization.Abbrev = $scope.selectedType.Abbrev;
//   };
//   $scope.setAlias = function(id, mainOrg) {
//     rest.setAlias(id, mainOrg)
//     .then(function(response) {
//       if(response.status === 200) console.log("success");
//     });
//   };
//   $scope.setAliasOrganization = function(mainOrg) {
//     $scope.mainOrg = mainOrg;
//   };
// });
