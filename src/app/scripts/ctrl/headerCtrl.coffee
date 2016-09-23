(->
  headerCtrl = ($scope, freeSearch) ->
    headerCtrl.$inject = ['$scope','freeSearch']

    init = () =>

      @search = ''
      @startSearch = startSearch
      @resetSearch = resetSearch

    # Util
    startSearch = (search) ->
      freeSearch.setSearch(JSURL.stringify(search))

    resetSearch = () =>
      freeSearch.setSearch('')
      @search = ''

    init()

    return

  angular
  .module('proposalApp')
  .controller('headerCtrl', headerCtrl)
)()