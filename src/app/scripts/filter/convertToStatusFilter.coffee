(->

  convertToStatus = () ->
    (int) ->
      switch int
        when '-1' then return "<span class='label label-warning'>Deaktiviert</span>"
        when '1' then return "<span class='label label-warning'>Raw</span>"
        when '2' then return "<span class='label label-info'>Edited</span>"
        when '3' then return "<span class='label label-danger'>Imported</span>"
        else return "<span class='label label-success'>Manual</span>"

  angular
  .module('proposalApp')
  .filter('convertToStatus', convertToStatus)
)()