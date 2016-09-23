(->

  convertSQLdate = () ->
    (dateString) ->
      t = dateString.split(/[- :]/)
      new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5])

  angular
  .module('proposalApp')
  .filter('convertSQLdate', convertSQLdate)
)()