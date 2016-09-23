(->

  convertToName = () ->
    (int, name) ->
      if int == '1' then return name
      else return ''

  angular
  .module('proposalApp')
  .filter('convertToName', convertToName)
)()