(->
  organizationModalCtrl = ($scope, $q, $uibModalInstance,
    $injector, proposalsDataService, organizationsDataService,
    items) ->
    organizationModalCtrl.$inject = ['$scope','$q','$uibModalInstance',
      '$injector','proposalsDataService','organizationsDataService','items']

    init = () =>

      @organization = items.organization

      @organizations = items.organizations
      @types = items.types

      @aliases = []

      @selectedType = searchObjectById(@types, @organization.TypeID)

      # Modal

      @editOrganization = editOrganization
      @cancel = cancel

      # Setter

      @setType = setType
      @setAlias = setAlias
      @setAliasOrganization = setAliasOrganization

      pullData(@organization.ID)

    # Util
    initSelection = (array, id) ->
      for obj in array
        do (obj) ->
          obj.checked = if obj.ID == id
            if obj.checked? then obj.checked else true
          else false

    searchObjectById = (array, id) ->
      _result = {}
      for obj in array
        do (obj) ->
          _result = obj if obj.ID == id
      return _result

    # pullData

    getProposal = (proposalID) =>
      proposalsDataService
      .getProposal(proposalID)
      .then (data) =>
        @proposal = data

    getOrganizationAliases = (organizationID) =>
      organizationsDataService
      .getOrganizationAliases(organizationID)
      .then (data) =>
        @aliases = data

    pullData = (organizationID) =>
      getOrganizationAliases(organizationID)


    # Public

    editOrganization = () =>
      console.log(@organization)
    #      proposalsDataService
    #        .editProposal(proposalID, @proposal)
    #        .then (result) =>
    #          $uibModalInstance.dismiss() if result.status == 200

    cancel = () =>
      $uibModalInstance.dismiss()

    # Setter

    setType = (id) =>
      @organization.TypeID = id

    setCulture = (cultures, id, checked) =>
      initSelection(cultures, id)
      if @proposal.subject_culture isnt id
        @proposal.subject_area = 0
        @proposal.subject = 0
      @proposal.subject_culture = checkIfSelected(id, checked)

    setArea = (areas, id, checked) =>
      initSelection(areas, id)
      if @proposal.subject_area isnt id
        @proposal.subject = 0
      @proposal.subject_area = checkIfSelected(id, checked)

    setSubject = (subjects, id, checked) =>
      initSelection(subjects, id)
      @proposal.subject = checkIfSelected(id, checked)

    # setSelection

    setSelection = (proposal) =>
      @selectedOrg = searchObjectById(@organizations, proposal.OrgID)
      @subjectsTree = initSubjectsTree(@cultures, @areas, @subjects)

    init()

    return

  angular
  .module('proposalApp')
  .controller('organizationModalCtrl', organizationModalCtrl)
)()