angular
  .module('app')
  .service('Session', Session);
//
// Session.$inject = ['$window'];

function Session() {
  this.create = function (sessionId, userId, email, name) {
    this.id = sessionId;
    this.userId = userId;
    this.email = email;
    this.name = name;
    // $window.sessionStorage.user = angular.toJson(this);
  };
  this.destroy = function () {
    this.id = null;
    this.userId = null;
  };
}
