angular
  .module('app')
  .controller('DashController', DashController)
  .controller('FilmsController', FilmsController);

DashController.$inject = ['Session'];
FilmsController.$inject = ['$log', 'Films'];

function DashController(Session) {
  var vm = this;
  vm.isNavCollapsed = true;
  vm.user = Session;
}
function FilmsController($log, Films) {
  var vm = this;
  vm.films = Films.query();
}
