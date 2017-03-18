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
  $scope.old="";
  $scope.new="";
  $scope.newagain="";
  $scope.list;
  $scope.show = function(data)
  {
    $scope.data=data;
    $scope.data.autoLoadTasks=((data.autoLoadTasks=="false")?false:true);
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
    $scope.old="";
    $scope.new="";
    $scope.newagain="";
  }
  $scope.passwordsave = function()
  {
    //if($scope.old==$scope.data.password&&$scope.new==$scope.newagain)
    if($scope.old=="")
    {
      $scope.old="";
      $scope.new="";
      $scope.newagain="";
      alert("Old password is empty.");
      return;
    }
    if($scope.new=="")
    {
      $scope.old="";
      $scope.new="";
      $scope.newagain="";
      alert("New password is empty.");
      return;
    }
    if($scope.new!=$scope.newagain)
    {
      $scope.old="";
      $scope.new="";
      $scope.newagain="";
      alert("Confirmation password mismatch.");
      return;
    }
    else
    {
      $scope.data.oldpassword = $scope.old;
      $scope.data.newpassword = $scope.new;
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
