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
        specialeffects: 0,
        iseffective: 0,
        plot: 0,
        convincing: 0,
        attractive: 0,
        place_viewer: 0,
        place_stimulate: 0,
        specific_sell: 0,
        specific_clear: 0,
        specific_provide: 0,
        specific_focus: 0,
        specific_promote: 0,
        discuss: 0,
        attention: 0,
        awareness: 0,
        comment: ''
      };
    }
    vm.loading = false;
  });

  // Save evaluation
  vm.save = function () {
    var filmsave = Tourfilm.save({id: vm.id, jury: vm.jury}, vm.eval).$promise;
    filmsave.then(function () {
      $state.go('dashboard');
    }, function (res) {
      alert('Ups!! There are an error, please make a screenshot of this error and send to contact@terres.info. Error:' + res);
    });
  };
}
