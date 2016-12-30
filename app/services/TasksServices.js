app.factory('TasksServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/tasks',
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