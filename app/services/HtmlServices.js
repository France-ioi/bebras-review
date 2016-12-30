app.factory('HtmlServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/Html',
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