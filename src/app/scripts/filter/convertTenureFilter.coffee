(->

  convertTenure = () ->
    (int) ->
      switch int
        when '1' then return 'mit Tenure'
        when '-1' then return 'ohne Tenure'
        else return ''

  angular
  .module('proposalApp')
  .filter('convertTenure', convertTenure)
)()