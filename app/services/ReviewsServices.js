app.factory('ReviewsServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/reviews',
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function autoassign(onSuccess, onError){
    $http.post(baseurl+'templates/autoassign', {}).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  return {
    action: action,
    autoassign: autoassign
  }
});
