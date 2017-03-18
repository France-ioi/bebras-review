app.factory('TasksServices', function($http) {
  function getData(endpoint, onSuccess, onError){
    $http.post(baseurl+'templates/'+endpoint,
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function updatesvn(onSuccess, onError){
    $http.post(baseurl+'templates/updatesvn',
    {
      data:'action'
    }).
    then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function reviewcreate(folderName, onSuccess, onError) {
    var dat = $.param({folderName: folderName});
    var config = {
        headers:{
          'Content-Type': 'application/x-www-form-urlencoded;'
        }
    };
    $http.post(baseurl+'templates/reviewcreate', dat, config)
        .then(onSuccess, onError);
  }

  function reviewchange(id, a,b,comment, onSuccess, onError){
     var dat = $.param({
                id, a,b,comment
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

  function autosave(onSuccess, onError){
    $http.post(baseurl+'templates/autosave')
    .then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function sendmess(taskID, mess, onSuccess, onError){
    var dat = $.param({
                taskID, mess
            });
    var config = {
        headers:{
          'Content-Type': 'application/x-www-form-urlencoded;'
        }
    };
    $http.post(baseurl+'templates/discussionsend',dat,config)
    .then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function changemess(ID, mess, onSuccess, onError){
    var dat = $.param({
                ID, mess
            });
    var config = {
        headers:{
          'Content-Type': 'application/x-www-form-urlencoded;'
        }
    };
    $http.post(baseurl+'templates/discussionchange',dat,config)
    .then(function(response) {
      onSuccess(response);
    }, function(response) {
      onError(response);
    });
  }

  function lastsave(data,onSuccess,onError){
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
    autosave: autosave,
    getData: getData,
    reviewchange: reviewchange,
    reviewcreate: reviewcreate,
    sendmess: sendmess,
    changemess: changemess,
    lastsave: lastsave,
    grlist: grlist,
    updatesvn: updatesvn
  }
});
