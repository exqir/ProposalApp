(->

  tableFilterService = () =>

    @filterInclude = (array, element) =>
      i = array.indexOf(element)
      if i > -1 then array.splice(i,1) else array.push(element)

    @filterRepeat = (array, attribute, element) =>
      if(array.length > 0) then if(array.indexOf(element[attribute]) < 0) then return
      return element

    return @

  angular
  .module('proposalApp')
  .service('tableFilterService', tableFilterService)
)()