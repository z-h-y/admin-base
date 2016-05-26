(function() {
  'use strict';

  angular
    .module('adminApp')
    .factory('Actions', Actions);

  Actions.$inject = ['$http'];

  function Actions($http) {

    return {
      updateUserProfile: function(data, cb) {
        if (!data || !cb) {
          return;
        }
        $http.post('/admin/api/updateUserProfile', data).then(function(response) {
          if (response && response.data) {
            var result = response.data;
            if (result && result.error) {
              cb(result.error);
            } else {
              cb();
            }
          }
        }, function(response) {
          if (cb) {
            cb(response.data);
          }
        });
      }

      /********* Add your app's actions *************/


      /********* Add your app's actions *************/
    };

  }

})();
