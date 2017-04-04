angular
  .module('app')
  .controller('DashController', DashController)
  .controller('FilmsController', FilmsController);

DashController.$inject = ['Session', '$state'];
FilmsController.$inject = ['$log', 'Films'];

function DashController(Session, $state) {
  var vm = this;
  vm.isNavCollapsed = true;
  vm.user = Session;
  vm.logout = function () {
    Session.destroy();
    $state.go('login');
  };
}
function FilmsController($log, Films) {
  var vm = this;
  vm.films = Films.query();
}
