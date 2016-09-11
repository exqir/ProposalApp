(->

  subjectsDataService = (apiCallService) =>
    base = './../backend/api/subjects/'

    @getCultures = () =>
      apiCallService.get(base + 'cultures/')

    @getAreas = () =>
      apiCallService.get(base + 'areas/')

    @getSubjects = () =>
      apiCallService.get(base + 'subjects/')

    return @

  angular
  .module('proposalApp')
  .service('subjectsDataService', subjectsDataService)
)()