app.controller('UsersController', ['$scope', '$location',  'UsersServices', function($scope, $location, UsersServices){
  //users
  $scope.action=function(){
    UsersServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
//tasklist
  $scope.listtasks=function(data){
    UsersServices.listtasks(data,function(response){
      $scope.listtas(data,response.data);
    }, function(response){
      $scope.listtas("Failed");
    });
  }
  $scope.list=[];
  $scope.listtas = function(a,data)
  {
    $scope.list[a]=data;
  }
  $scope.data="loading";
  $scope.councode="France";
  $scope.myOrderBy="firstName";
  $scope.ownID = -1;
  $scope.isAdmin = true;

  $scope.groupsList = [];

  $scope.countrylist=new Array();
  $scope.co="";

  //userid->tasks
  $scope.show = function(data)
  {
    var usersList = data.usersList;
    $scope.data = usersList;
    $scope.groupsList = data.groupsList;
    $scope.ownID = data.ownID;
    $scope.isAdmin = data.isAdmin;
    var i=0;
    for(i=0;i<usersList.length;i++)
    {
      $scope.listtasks(usersList[i]['ID']);
    }

    var length=0;
    for(i=0;i<usersList.length;i++)
    {
      for(j=0;j<i;j++)
        if(usersList[i].country==usersList[j].country)
          break;
      if(i==j)
        $scope.countrylist[length++]=usersList[i].country;
    }
  }

  $scope.listts = function (data)
  {
    $location.url("Tasks?id="+data);
  }

  $scope.ch = function (id,member,data)
  {
    UsersServices.update(id,member,data,function(response){
    }, function(response){
    });
  }

  $scope.action();

}]);
