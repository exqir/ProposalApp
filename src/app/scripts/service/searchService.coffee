(->

  search = ($location, $injector) ->
    search.$inject = ['$location','$injector']

    @setSearch = (search) ->
      $location.search('search', search)

    return @

  angular
  .module('proposalApp')
  .service('search', search)
)()