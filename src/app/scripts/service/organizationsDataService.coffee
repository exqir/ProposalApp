(->

  organizationsDataService = (apiCallService) =>
    base = './../backend/api/organizations/'

    @getOrganizations = () =>
      apiCallService.get(base)

    @getOrganization = (id) =>
      apiCallService.get(base + id)

    @getOrganizationAliases = (id) =>
      apiCallService.get(base + id + '/alias/')

    @editOrganization = (id, organization) =>
      apiCallService.put(base + id, organization)

    @setOrganizationAsAliasOf = (id, parent) =>
      apiCallService.put(base + id + '/merge/' + parent)

    return @

  angular
  .module('proposalApp')
  .service('organizationsDataService', organizationsDataService)
)()