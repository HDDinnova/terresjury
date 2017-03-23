angular
  .module('app')
  .factory('Films', Films)
  .factory('Tourfilm', Tourfilm)
  .factory('Corpfilm', Corpfilm)
  .factory('Docfilm', Docfilm)
  .factory('AuthService', AuthService);

function AuthService($http, Session) {
  var authService = {};

  authService.login = function (credentials) {
    return $http.post('api/login', credentials)
      .then(function (res) {
        Session.create(res.data.id, res.data.user.id, res.data.user.email, res.data.user.name);
        return res.data.user;
      });
  };

  authService.isAuthenticated = function () {
    return Boolean(Session.userId);
  };

  return authService;
}

function Films($resource) {
  return $resource('api/films', {}, {
    query: {method: 'GET'}
  });
}

function Tourfilm($resource) {
  return $resource('api/tourfilm/:id/:jury', {id: '@id', jury: '@jury'},
    {
      query: {method: 'GET'},
      save: {method: 'POST'}
    });
}

function Corpfilm($resource) {
  return $resource('api/corpfilm/:id/:jury', {id: '@id', jury: '@jury'},
    {
      query: {method: 'GET'},
      save: {method: 'POST'}
    });
}

function Docfilm($resource) {
  return $resource('api/docfilm/:id/:jury', {id: '@id', jury: '@jury'},
    {
      query: {method: 'GET'},
      save: {method: 'POST'}
    });
}
