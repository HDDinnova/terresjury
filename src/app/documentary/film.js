angular
  .module('app')
  .controller('DocumentaryController', DocumentaryController);

DocumentaryController.$inject = ['Session', '$stateParams', 'Docfilm', '$window', '$state'];

function DocumentaryController(Session, $stateParams, Docfilm, $window, $state) {
  var vm = this;
  vm.id = $stateParams.id;
  vm.jury = Session.userId;
  vm.loading = true;
  $window.scrollTo(0, 0);

  // Get info from database
  var film = Docfilm.query({id: vm.id, jury: vm.jury}).$promise;

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
        specific_travel: 0,
        specific_sustain: 0,
        specific_narrative: 0,
        specific_focus: 0,
        specific_reflection: 0,
        specific_suggest: 0,
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
    var filmsave = Docfilm.save({id: vm.id, jury: vm.jury}, vm.eval).$promise;
    filmsave.then(function () {
      $state.go('dashboard');
    }), function (res) {
      alert('Ups!! There are an error, please make a screenshot of this error and send to filmsnomades@gmail.com. Error:' + res);
    };
  };
}
