(->
  organizationListCtrl = ($scope,
    $uibModal, $injector,
    $routeParams, organizationsDataService,
    statisticsDataService, tableFilterService) ->
    organizationListCtrl.$inject = ['$scope',
      '$uibModal','$injector',
      '$routeParams','organizationsDataService',
      'statisticsDataService','tableFilterService']

    init = () =>

      @organizations = []

      # Dropdown content
      @types = []
      @cities = []
      @states = []
      @countries = []

      # Filter
      @type = []
      @includeType = includeType
      @typeFilter = typeFilter

      @state = []
      @includeState = includeState
      @stateFilter = stateFilter

      # Search
      @search = ''
      #@.$on('$routeChangeSuccess', setSearchTerm)

      # Modal
      @open = open

      pullData()

    # Util

    setSearchTerm = () =>
      @search = JSURL.parse($routeParams.search)

    # pullData

    getOrganizations = () =>
      organizationsDataService
      .getOrganizations()
      .then (data) =>
        @organizations = data

    getOrganizationTypes = () =>
      statisticsDataService
      .getOrganizationTypes()
      .then (data) =>
        @types = data

    getStates = () =>
      statisticsDataService
      .getOrganizationStates()
      .then (data) =>
        @states = data

    # Modal

    open = (organization, types, organizations) =>
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'partials/_organizationModal.html',
        controller: 'organizationModalCtrl',
        controllerAs: 'vm',
        size: 'lg',
        resolve:
          items: () =>
            return {
              organization: organization,
              types: types,
              organizations: organizations,
            }
      })

      modalInstance.result.then () ->
        return


    # Filter

    includeType = (Abbrev) =>
      tableFilterService.filterInclude(@type, Abbrev)

    typeFilter = (organization) =>
      tableFilterService.filterRepeat(@type,'Abbrev', organization)

    includeState = (state) =>
      tableFilterService.filterInclude(@state, state)

    stateFilter = (organization) =>
      tableFilterService.filterRepeat(@state,'State', organization)

    pullData = () =>
      getOrganizations()
      getOrganizationTypes()
      getStates()

    init()

    return

  angular
  .module('proposalApp')
  .controller('organizationListCtrl', organizationListCtrl)
)()