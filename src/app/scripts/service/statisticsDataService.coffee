(->

  statisticsDataService = (apiCallService) =>
    base = './../backend/api/statistics/'
    organization = base + 'organizations/'
    proposal = base + 'proposals/'

    @getOrganizationCount = () =>
      apiCallService.get(organization, 'number')

    @getOrganizationTypes = () =>
      apiCallService.get(organization + 'types/')

    @getOrganizationTypeCount = () =>
      apiCallService.get(organization + 'types/count/')

    @getUsedOrganizations = () =>
      apiCallService.get(organization + 'used/')

    @getOrganizationStates = () =>
      apiCallService.get(organization + 'states/')

    @getUsedOrganizationStates = () =>
      apiCallService.get(organization + 'states/used/')

    @getUsedOrganizationStatesByCountry = (country) =>
      apiCallService.get(organization + 'states/used/' + country)

    @getUsedOrganizationStateCountByCountry = (country) =>
      apiCallService.get(organization + 'states/used/count/' + country)

    @getProposalCount = () =>
      apiCallService.get(proposal, 'number')

    @getProposalCountByCountry = (country) =>
      apiCallService.get(proposal + 'country/' + country, 'number')

    @getProposalTypeCount = () =>
      apiCallService.get(proposal + 'types/')

    return @

  angular
    .module('proposalApp')
    .service('statisticsDataService', statisticsDataService)
)()