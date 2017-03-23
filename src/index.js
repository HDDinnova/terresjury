angular
  .module('app', ['ui.router', 'ui.bootstrap', 'ngResource', 'ngSanitize'])
  .constant('AUTH_EVENTS', {
    loginSuccess: 'auth-login-success',
    loginFailed: 'auth-login-failed',
    logoutSuccess: 'auth-logout-success',
    notAuthenticated: 'auth-not-authenticated'
  });
