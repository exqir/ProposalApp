(->
  proposalModalCtrl = ($scope, $q, $uibModalInstance, $filter,
    $injector, filterFilter,
    proposalsDataService, organizationsDataService,
    items) ->
    proposalModalCtrl.$inject = ['$scope','$q','$uibModalInstance','$filter',
      '$injector','filterFilter','proposalsDataService','organizationsDataService','items']

    proposalID = items.id

    init = () =>

      @proposal

      @organizations = []
      @cultures = items.cultures
      @areas = items.areas
      @subjects = items.subjects

      # Modal

      @editProposal = editProposal
      @cancel = cancel

      # Setter

      @setOrganization = setOrganization
      @setCulture = setCulture
      @setArea = setArea
      @setSubject = setSubject

      # Lookup
      @orgLookup = {}
      @cultureLookup = createLookup(@cultures, 'ID')
      @areaLookup = createLookup(@areas, 'ID')
      @subjectLookup = createLookup(@subjects, 'ID')

      $q.all(pullData(proposalID))
        .then () =>
          @orgLookup = createLookup(@organizations, 'ID')
          setSelection(@proposal)

    # Util

    createLookup = (array, attribute) ->
      obj = {}
      pushElement = (element) =>
        obj[element[attribute]] = element

      pushElement element for element in array
      return obj

    # pullData

    getProposal = (proposalID) =>
      proposalsDataService
        .getProposal(proposalID)
        .then (data) =>
          @proposal = data

    getOrganizations = () =>
      organizationsDataService
        .getOrganizations()
        .then (data) =>
          @organizations = data

    pullData = (proposalID) =>
      promises = []
      promises.push(getProposal(proposalID))
      promises.push(getOrganizations())
      return promises


    # Public

    editProposal = () =>
      proposalsDataService
        .editProposal(proposalID, @proposal)
        .then (result) =>
          $uibModalInstance.dismiss() if result.status == 200

    cancel = () =>
      $uibModalInstance.dismiss()

    setOrganization = (id) =>
      @proposal.OrgID = id

    setCulture = (id) =>
      @proposal.subject_culture = id

    setArea = (id) =>
      @proposal.subject_area = id

    setSubject = (id) =>
      @proposal.subject = id

    # setSelection
    setSelection = (proposal) =>
      @selectedOrg = @orgLookup[proposal.OrgID]
      @selectedCulture = @cultureLookup[proposal.subject_culture]
      @selectedArea = @areaLookup[proposal.subject_area]
      @selectedSubject = @subjectLookup[proposal.subject]

    init()

    return

  angular
  .module('proposalApp')
  .controller('proposalModalCtrl', proposalModalCtrl)
)()