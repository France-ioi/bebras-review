app.factory('YourServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/Your',
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function reviewchange(id, comment, onSuccess, onError){
     var dat = $.param({
                id, comment
            });
    var config = {
        headers:{
          'Content-Type': 'application/x-www-form-urlencoded;'
        }
    };
    $http.post(baseurl+'templates/reviewchange',dat,config).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  return {
    action: action,
    reviewchange:reviewchange
  }
});