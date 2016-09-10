(->
  proposalListCtrl = ($scope, $uibModal, $filter,
    $injector, $routeParams, filterFilter, freeSearch, proposalsDataService, subjectsDataService, statisticsDataService) ->
    proposalListCtrl.$inject = ['$scope','$routeParams','proposalsDataService','subjectsDataService','statisticsDataService']

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

      @search = ''

      pullData()

      @.$on('$routeChangeSuccess', setSearchTerm)

    #Util

    filterInclude = (array, element) =>
      i = @[array].indexOf(element)
      if i > -1 then @[array].splice(i,1) else @[array].push(element)

    filterRepeat = (array, attribute, proposal) =>
      if(@[array].length > 0) then if(@[array].indexOf(proposal[attribute]) < 0) then return
      return proposal

    setSearchTerm = () =>
      @search = JSURL.parse($routeParams.search)

    #Public

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

    ## Modal

    @open = (proposalID) =>
      modalInstance = $uibModal.open({
        animation: true,
        templateUrl: 'partials/_proposalModal.html',
        controller: 'proposalModalCtrl',
        controllerAs: 'modal',
        size: 'lg',
        resolve: {id: () => proposalID}
      })

      modalInstance.result.then () ->
        return


    ## Filter

    includeOrg = (orgName) =>
      filterInclude('org', orgName)

    orgFilter = (proposal) =>
      filterRepeat('org','orgName', proposal)

    includeRaw = (raw) =>
      filterInclude('raw', raw)

    rawFilter = (proposal) =>
      filterRepeat('raw','Raw', proposal)

    includeState = (state) =>
      filterInclude('state', state)

    stateFilter = (proposal) =>
      filterRepeat('state','State', proposal)

    includeCulture = (culture) =>
      filterInclude('subjectCulture', culture)

    cultureFilter = (proposal) =>
      filterRepeat('subjectCulture','Culture', proposal)

    includeArea = (area) =>
      filterInclude('subjectArea', area)

    areaFilter = (proposal) =>
      filterRepeat('subjectArea','Area', proposal)

    includeSubject = (subject) =>
      filterInclude('subject', subject)

    subjectFilter = (proposal) =>
      filterRepeat('subject','Subject', proposal)

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