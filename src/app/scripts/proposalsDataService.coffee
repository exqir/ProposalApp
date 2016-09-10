(->

  proposalsDataService = (apiCallService) =>
    base = './../backend/api/proposals/'

    @getProposals = () =>
      apiCallService.get(base)

    @getProposal = (id) =>
      apiCallService.get(base + id)

    @editProposal = (id, proposal) =>
      apiCallService.put(base + id, proposal)

    return @

  angular
  .module('proposalApp')
  .service('proposalsDataService', proposalsDataService)
)()