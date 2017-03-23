angular
  .module('app')
  .controller('TourfilmController', TourfilmController);

TourfilmController.$inject = ['Session', '$stateParams', 'Tourfilm', '$window', '$state'];

function TourfilmController(Session, $stateParams, Tourfilm, $window, $state) {
  var vm = this;
  vm.id = $stateParams.id;
  vm.jury = Session.userId;
  vm.loading = true;
  $window.scrollTo(0, 0);

  // Get info from database
  var film = Tourfilm.query({id: vm.id, jury: vm.jury}).$promise;

  // Fill information when the info is totally downloaded
  film.then(function (res) {
    vm.film = res.film;
    vm.eval = res.evaluation;
    if (!vm.eval) {
      vm.eval = {
        originalityscript: 0,
        rythm: 0,
        length: 0,
        photography: 0,
        sound: 0,
        edition: 0,
        specific1: 0,
        specific2: 0,
        sustainvalue: 0,
        stimulate: 0,
        originalitysustain: 0,
        attractiveness: 0,
        conscience: 0
      };
    }
    vm.loading = false;
  });

  // Save evaluation
  vm.save = function () {
    var filmsave = Tourfilm.save({id: vm.id, jury: vm.jury}, vm.eval).$promise;
    filmsave.then(function () {
      $state.go('dashboard');
    });
  };
}
