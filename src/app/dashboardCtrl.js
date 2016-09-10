angular
    .module('proposalApp')
    .controller('dashboardCtrl', dashboardCtrl);

dashboardCtrl.$inject = ['restRessources'];

function dashboardCtrl($scope,$http, $filter, $injector, restRessources) {
    /* jshint validthis: true */
    var vm = this;

    var vm.proposals = [];
    var vm.proposalsGermany = [];
    var vm.states = [];
    var vm.organizations = [];
    var vm.besoldung = [];

    init();

    function init() {
        getProposals().then(function() {
            console.info('Proposals loaded');
        });
        getProposalsForCountry('Deutschland').then(function() {
            console.info('German Proposals loaded');
        });
    }

    function getProposals() {
        return restRessources.getProposals().then(function(data) {
            vm.proposals = data;
        })
    }

    function getProposalsByCountry(country) {
        return restRessources.getProposalsByCountry(country).then(function(data) {
            vm.proposalsGermany = data;
        })
    }

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
}
