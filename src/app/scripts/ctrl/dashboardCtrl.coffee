(->
  dashboardCtrl = ($scope, $filter, $injector, statisticsDataService) ->
    dashboardCtrl.$inject = ['statisticsDataService']

    init = () =>

      @proposalNumber
      @proposalNumberByCountry
      @organizationNumber
      @statesByCountry = []
      @stateNumberByCountry = []
      @organizations = []
      @proposalTypes = []
      @organizationTypes = []

      @calculatePercentage = calculatePercentage

      pullData('Deutschland')

    setTypes = (countObj, targetArray) =>
      for type in Object.keys(countObj)
        do (type) =>
          obj = {'name': type, 'number': countObj[type]}
          targetArray.push(obj)

    getProposalNumber = () =>
      statisticsDataService
        .getProposalCount()
        .then (data) =>
          @proposalNumber = data

    getProposalNumberByCountry = (country) =>
      statisticsDataService
        .getProposalCountByCountry(country)
        .then (data) =>
          @proposalNumberByCountry = data

    getProposalTypeCount = () =>
      statisticsDataService
        .getProposalTypeCount()
        .then (data) =>
          setTypes(data, @proposalTypes)

    getOrganizationNumber = () =>
      statisticsDataService
        .getOrganizationCount()
        .then (data) =>
          @organizationNumber = data

    getOrganizationTypeCount = () =>
      statisticsDataService
        .getOrganizationTypeCount()
        .then (data) =>
          setTypes(data, @organizationTypes)

    getUsedStatesByCountry = (country) =>
      statisticsDataService
        .getUsedOrganizationStatesByCountry(country)
        .then (data) =>
          @statesByCountry = data

    getUsedStateCountByCountry = (country) =>
      statisticsDataService
        .getUsedOrganizationStateCountByCountry(country)
        .then (data) =>
          setTypes(data, @stateNumberByCountry)

    calculatePercentage = (number, baseNumber) =>
      Math.round((number / baseNumber) * 100)

    pullData = (country) =>
      getProposalNumber()
      getProposalNumberByCountry(country)
      getProposalTypeCount()
      getOrganizationNumber()
      getOrganizationTypeCount()
      getUsedStatesByCountry(country)
      getUsedStateCountByCountry(country)

    init()

    return

  angular
    .module('proposalApp')
    .controller('dashboardCtrl', dashboardCtrl)
)()