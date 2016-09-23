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

      @selectedOrg

      @subjectsTree = []

      # Modal

      @editProposal = editProposal
      @cancel = cancel

      # Setter

      @setOrganization = setOrganization
      @setCulture = setCulture
      @setArea = setArea
      @setSubject = setSubject

      $q.all(pullData(proposalID))
        .then () =>
          setSelection(@proposal)

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
      console.log(@proposal)
#      proposalsDataService
#        .editProposal(proposalID, @proposal)
#        .then (result) =>
#          $uibModalInstance.dismiss() if result.status == 200

    cancel = () =>
      $uibModalInstance.dismiss()

    # Setter

    setOrganization = (id) =>
      @proposal.OrgID = id

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
  .controller('proposalModalCtrl', proposalModalCtrl)
)()