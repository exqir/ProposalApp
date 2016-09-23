(->
  organizationModalCtrl = ($scope,
    $q, $uibModalInstance,
    $injector, proposalsDataService,
    organizationsDataService, items) ->
    organizationModalCtrl.$inject = ['$scope',
      '$q','$uibModalInstance',
      '$injector','proposalsDataService',
      'organizationsDataService','items']

    init = () =>

      @organization = items.organization

      @organizations = items.organizations
      @types = items.types

      @aliases = []
      @aliasOrganization = {}

      @selectedType

      # Modal

      @editOrganization = editOrganization
      @cancel = cancel

      @saveAlias = saveAlias

      # Setter

      @setType = setType
      @setAliasOrganization = setAliasOrganization

      setSelection(@organization)
      pullData(@organization.ID)

    # Util

    searchObjectById = (array, id) ->
      _result = {}
      for obj in array
        do (obj) ->
          _result = obj if obj.ID == id
      return _result

    # pullData

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
    #      organizationsDataService
    #        .editOrganization(@organization.ID, @organization)
    #        .then (result) =>
    #          $uibModalInstance.dismiss() if result.status == 200

    cancel = () =>
      $uibModalInstance.dismiss()

    # Setter

    setType = (id) =>
      @organization.TypeID = id

    setAliasOrganization = (organization) =>
      @aliasOrganization = organization

    saveAlias = (orgID, parentID) =>
      organizationsDataService
      .setOrganizationAsAliasOf(orgID, parentID)
      .then (result) =>
        if result.status == 200 then console.log('success')

    # setSelection

    setSelection = (organization) =>
      @selectedType = searchObjectById(@types, organization.TypeID)

    init()

    return

  angular
  .module('proposalApp')
  .controller('organizationModalCtrl', organizationModalCtrl)
)()