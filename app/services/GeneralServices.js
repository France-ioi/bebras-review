app.factory('GeneralServices', function($http) {
  function action(onSuccess, onError){
    $http.post(baseurl+'templates/general',
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function lastsave(data,onSuccess,onError){
    alert("sending"+data);
    var dat = $.param({
                data
            });
    var config = {
        headers:{
          'Content-Type': 'application/x-www-form-urlencoded;'
        }
    };
    $http.post(baseurl+'templates/lastsave',dat,config)
    .then(function(response) {
       alert("service"+JSON.stringify(response.data));
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function grlist(onSuccess, onError){
    $http.post(baseurl+'templates/group')
    .then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  return {
    action: action,
    grlist: grlist,
    lastsave: lastsave
  }
});