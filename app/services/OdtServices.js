app.factory('OdtServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/Odt',
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  return {
    action: action
  }
});