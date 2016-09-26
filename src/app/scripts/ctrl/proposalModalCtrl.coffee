(->
  proposalModalCtrl = ($scope,
    $q, $uibModalInstance,
    $filter, $injector,
    filterFilter, proposalsDataService,
    organizationsDataService, items) ->
    proposalModalCtrl.$inject = ['$scope',
      '$q','$uibModalInstance',
      '$filter','$injector',
      'filterFilter','proposalsDataService',
      'organizationsDataService','items']

    init = () =>

      @proposal = items.proposal

      @organizations = []
      @cultures = items.cultures
      @areas = items.areas
      @subjects = items.subjects

      @selectedOrg
      @selectedOptOrg

      @subjectsTree = []

      # Modal

      @editProposal = editProposal
      @cancel = cancel

      # Setter

      @setOrganization = setOrganization
      @setOptOrganization = setOptOrganization
      @setCulture = setCulture
      @setArea = setArea
      @setSubject = setSubject

      $q.all(pullData(@proposal.ID))
        .then () =>
          setSelection(@proposal)
          @subjectsTree = initSubjectsTree(@cultures, @areas, @subjects)

    # Util
    initSubjectsTree = (cultures, areas, subjects) =>
      initSelection(cultures, @proposal.subject_culture)
      initSelection(areas, @proposal.subject_area)
      initSelection(subjects, @proposal.subject)

      setChildren(cultures, setChildren(areas, subjects))

    initSelection = (array, id) ->
      for obj in array
        do (obj) ->
          obj.checked = if obj.ID == id
            if obj.checked? then obj.checked else true
          else false

    setChildren = (parents, children) ->
      _parents = parents
      for parent in _parents
        do (parent) ->
          fillChildren(parent, children)
      return _parents

    fillChildren = (parent, children) ->
      parent.children = []
      for child in children
        do (child) ->
          parent.children.push(child) if parent.ID == child.ParentID

    searchObjectById = (array, id) ->
      _result = {}
      for obj in array
        do (obj) ->
          _result = obj if obj.ID == id
      return _result

    checkIfSelected = (id, checked) ->
      if checked then id else 0

    # pullData

    getDescription = (id) =>
      proposalsDataService
      .getProposalDescription(id)
      .then (data) =>
        @proposal.Description = data.Description

    getOrganizations = () =>
      organizationsDataService
        .getOrganizations()
        .then (data) =>
          @organizations = data

    pullData = (id) =>
      promises = []
      promises.push(getDescription(id))
      promises.push(getOrganizations())
      return promises

    # Public

    editProposal = () =>
      console.log(@proposal)
#      proposalsDataService
#        .editProposal(proposalID, @proposal)
#        .then (result) =>
#          $uibModalInstance.dismiss() if result.status == 200

    cancel = () =>
      $uibModalInstance.dismiss()

    # Setter

    setOrganization = (organization) =>
      @proposal.OrgID = organization.ID
      @proposal.orgName = organization.Name
      @proposal.orgAbbrev = organization.Abbrev

    setOptOrganization = (organization) =>
      @proposal.OrgOptID = organization.ID

    setCulture = (cultures, culture, checked) =>
      initSelection(cultures, culture.ID)
      if @proposal.subject_culture isnt culture.ID
        @proposal.subject_area = 0
        @proposal.Area = ''
        @proposal.subject = 0
        @proposal.SubjectName = ''
      @proposal.subject_culture = checkIfSelected(culture.ID, checked)
      @proposal.Culture = if checked then culture.Name else ''

    setArea = (areas, area, checked) =>
      initSelection(areas, area.ID)
      if @proposal.subject_area isnt area.ID
        @proposal.subject = 0
        @proposal.SubjectName = ''
      @proposal.subject_area = checkIfSelected(area.ID, checked)
      @proposal.Area = if checked then area.Name else ''

    setSubject = (subjects, subject, checked) =>
      initSelection(subjects, subject.ID)
      @proposal.subject = checkIfSelected(subject.ID, checked)
      @proposal.SubjectName = if checked then subject.Name else ''


    # setSelection

    setSelection = (proposal) =>
      @selectedOrg = searchObjectById(@organizations, proposal.OrgID)
      @selectedOptOrg = searchObjectById(@organizations, proposal.OrgOptID)

    init()

    return

  angular
  .module('proposalApp')
  .controller('proposalModalCtrl', proposalModalCtrl)
)()