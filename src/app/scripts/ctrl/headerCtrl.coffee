(->
  headerCtrl = ($scope, search) ->
    headerCtrl.$inject = ['$scope','search']

    init = () =>

      @searchTerm = ''
      @startSearch = startSearch
      @resetSearch = resetSearch

    # Util
    startSearch = (searchTerm) ->
      search.setSearch(JSURL.stringify(searchTerm))

    resetSearch = () =>
      search.setSearch('')
      @searchTerm = ''

    init()

    return

  angular
  .module('proposalApp')
  .controller('headerCtrl', headerCtrl)
)()