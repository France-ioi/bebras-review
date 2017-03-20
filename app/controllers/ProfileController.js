app.controller('ProfileController', ['$scope', '$location', 'ProfileServices', function($scope, $location, ProfileServices){
  $scope.action=function(){
    ProfileServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
  $scope.update = function (fullprofile) {
    if(fullprofile) {
      var newdata = $scope.data;
    } else {
      var newdata = {
        'oldpassword': $scope.data.oldpassword,
        'newpassword': $scope.data.newpassword};
    }
    ProfileServices.update($scope.data, function(response){
      $scope.show(response.data);
    },function(response){
      $scope.show("Failed");
    });
  }

  $scope.listtasks=function(data){
    ProfileServices.listtasks(data,function(response){
      $scope.listtas(response.data);
    }, function(response){
      $scope.listtas("Failed");
    });
  }
  $scope.listtas = function(data)
  {
    $scope.list=data;
  }
  $scope.data="loading";
  $scope.passwords = {
    oldp: '',
    newp: '',
    newpagain: ''};
  $scope.list = [];
  $scope.countryList = [];
  $scope.show = function(data)
  {
    $scope.data = data.profile;
    $scope.countryList = data.countryList;
    $scope.data.autoLoadTasks=((data.profile.autoLoadTasks=="false")?false:true);
    $scope.listtasks(data.ID);
  }

  
  $scope.save = function()
  {
    //update
    $scope.update();
  }
  $scope.cancel = function()
  {
    $scope.action(true);
  }
  $scope.passwordcancel = function()
  {
    $scope.passwords.oldp = "";
    $scope.passwords.newp = "";
    $scope.passwords.newpagain = "";
  }
  $scope.passwordsave = function()
  {
    if($scope.passwords.oldp=="")
    {
      alert("Old password is empty.");
      return;
    }
    if($scope.passwords.newp=="")
    {
      alert("New password is empty.");
      return;
    }
    if($scope.passwords.newp != $scope.passwords.newpagain)
    {
      alert("Confirmation password mismatch.");
      return;
    }
    else
    {
      $scope.data.oldpassword = $scope.passwords.oldp;
      $scope.data.newpassword = $scope.passwords.newp;
      $scope.passwords.oldp = "";
      $scope.passwords.newp = "";
      $scope.passwords.newpagain = "";
      $scope.update(false);
    }
  }

  $scope.sele = function()
  {
    $location.url("Tasks?id="+$scope.sel);
  }

  $scope.sel=0;
  $scope.action();

}]);
