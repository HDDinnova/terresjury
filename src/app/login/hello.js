angular
  .module('app')
  .controller('LoginController', LoginController);

LoginController.$inject = ['AuthService', '$rootScope', 'AUTH_EVENTS', '$state'];

function LoginController(AuthService, $rootScope, AUTH_EVENTS, $state) {
  var vm = this;
  vm.login = function () {
    AuthService.login(vm.credentials).then(function () {
      $rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
      $state.go('dashboard');
    }, function () {
      $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
    });
  };
}
