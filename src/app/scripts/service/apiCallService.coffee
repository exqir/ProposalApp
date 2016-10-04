(->

  apiCallService = ($http) =>

    @get = (url, spec) =>
      return $http
      .get(url)
      .then (result) =>
        if spec? then return result.data[spec]
        else return result.data

    @put = (url, payload) =>
      if payload? then return $http.put(url, payload)
      else return $http.put(url)

    return @

  angular
  .module('proposalApp')
  .service('apiCallService', apiCallService)
)()