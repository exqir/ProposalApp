(->
  proposalListCtrl = ($scope,
    $uibModal, $filter,
    $injector, $routeParams,
    filterFilter, proposalsDataService,
    subjectsDataService, statisticsDataService,
    tableFilterService, convertSQLdateFilter,
    convertTenureFilter, convertToStatusFilter,
    convertToNameFilter) ->
    proposalListCtrl.$inject = ['$scope',
      '$uibModal','$filter',
      '$injector','$routeParams',
      'filterFilter','proposalsDataService',
      'subjectsDataService','statisticsDataService',
      'tableFilterService','convertSQLdateFilter',
      'convertTenureFilter','convertToStatusFilter',
      'convertToNameFilter']

    init = () =>

      @proposals = []

      # Dropdown content
      @organizations = []
      @states = []
      @cultures = []
      @areas = []
      @subjects = []

      # Filter
      @org = []
      @includeOrg = includeOrg
      @orgFilter = orgFilter

      @raw = []
      @includeRaw = includeRaw
      @rawFilter = rawFilter

      @state = []
      @includeState = includeState
      @stateFilter = stateFilter

      @subjectCulture = []
      @includeCulture = includeCulture
      @cultureFilter = cultureFilter

      @subjectArea = []
      @includeArea = includeArea
      @areaFilter = areaFilter

      @subject = []
      @includeSubject = includeSubject
      @subjectFilter = subjectFilter

      # Search
      @search = ''
      $scope.$on('$routeChangeSuccess', setSearchTerm)

      # Modal
      @open = open

      pullData()

    # Util

    setSearchTerm = () =>
      @search = JSURL.parse($routeParams.search)

    # pullData

    getProposals = () =>
      proposalsDataService
      .getProposals()
      .then (data) =>
        @proposals = data

    getOrganizations = () =>
      statisticsDataService
        .getUsedOrganizations()
        .then (data) =>
          @organizations = data

    getStates = () =>
      statisticsDataService
        .getUsedOrganizationStates()
        .then (data) =>
          @states = data

    getCultures = () =>
      subjectsDataService
        .getCultures()
        .then (data) =>
          @cultures = data

    getAreas = () =>
      subjectsDataService
      .getAreas()
      .then (data) =>
        @areas = data

    getSubjects = () =>
      subjectsDataService
      .getSubjects()
      .then (data) =>
        @subjects = data

    # Modal

    open = (proposal, cultures, areas, subjects) =>
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'partials/_proposalModal.html',
        controller: 'proposalModalCtrl',
        controllerAs: 'modal',
        size: 'lg',
        resolve:
          {
            items: () =>
              return {
                proposal: proposal,
                cultures: cultures,
                areas: areas,
                subjects: subjects
              }
          }
      })

      modalInstance.result.then () ->
        return


    # Filter

    includeOrg = (orgName) =>
      tableFilterService.filterInclude(@org, orgName)

    orgFilter = (proposal) =>
      tableFilterService.filterRepeat(@org,'orgName', proposal)

    includeRaw = (raw) =>
      tableFilterService.filterInclude(@raw, raw)

    rawFilter = (proposal) =>
      tableFilterService.filterRepeat(@raw,'Raw', proposal)

    includeState = (state) =>
      tableFilterService.filterInclude(@state, state)

    stateFilter = (proposal) =>
      tableFilterService.filterRepeat(@state,'State', proposal)

    includeCulture = (culture) =>
      tableFilterService.filterInclude(@subjectCulture, culture)

    cultureFilter = (proposal) =>
      tableFilterService.filterRepeat(@subjectCulture,'Culture', proposal)

    includeArea = (area) =>
      tableFilterService.filterInclude(@subjectArea, area)

    areaFilter = (proposal) =>
      tableFilterService.filterRepeat(@subjectArea,'Area', proposal)

    includeSubject = (subject) =>
      tableFilterService.filterInclude(@subject, subject)

    subjectFilter = (proposal) =>
      tableFilterService.filterRepeat(@subject,'Subject', proposal)

    pullData = () =>
      getProposals()
      getOrganizations()
      getStates()
      getCultures()
      getAreas()
      getSubjects()

    init()

    return

  angular
  .module('proposalApp')
  .controller('proposalListCtrl', proposalListCtrl)
)()