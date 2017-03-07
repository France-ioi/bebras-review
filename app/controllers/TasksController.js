app.controller('TasksController', ['$scope', '$location',  'TasksServices', function($scope,$location,  TasksServices){
  $scope.action=function(){
    TasksServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
  $scope.data="loading";
  $scope.flag=0;

  $scope.yearlist=new Array();
  $scope.ye="0";
  $scope.countrylist=new Array();
  $scope.co="0";
  $scope.ownerlist=new Array();
  $scope.ow="0";
  $scope.grouplist=new Array();
  $scope.gr="0";
  $scope.statuslist=new Array();
  $scope.st="0";
  $scope.reviewerIDlist=new Array();
  $scope.reviewerlist=new Array();
  $scope.re="0";

  $scope.show = function(data)
  {
    $scope.data=data;
    var i,j;
    var length=0;

    // TODO :: better logic to make the lists...
    for(i=0;i<$scope.data.length;i++)
    {
      for(j=0;j<i;j++)
        if(data[i].year==data[j].year)
          break;
      if(i==j)
        $scope.yearlist[length++]=data[i].year;
    }

    length=0;
    for(i=0;i<$scope.data.length;i++)
    {
      for(j=0;j<i;j++)
        if(data[i].countryCode==data[j].countryCode)
          break;
      if(i==j)
        $scope.countrylist[length++]=data[i].countryCode;
    }

    length=0;
    for(i=0;i<$scope.data.length;i++)
    {
      for(j=0;j<i;j++)
        if(data[i].ownerName==data[j].ownerName)
          break;
      if(i==j)
        $scope.ownerlist[length++]=data[i].ownerName;
      // TODO :: use IDs for filtering, name for display
    }

    length=0;
    for(i=0;i<$scope.data.length;i++)
    {
      for(j=0;j<i;j++)
        if(data[i].Group==data[j].Group)
          break;
      if(i==j)
        $scope.grouplist[length++]=data[i].Group;
    }

    length=0;
    for(i=0;i<$scope.data.length;i++)
    {
      for(j=0;j<i;j++)
        if(data[i].status==data[j].status)
          break;
      if(i==j)
        $scope.statuslist[length++]=data[i].status;
    }

    length=0;
    for(i=0;i<$scope.data.length;i++)
    {
      for(var revID in data[i].reviewers) {
        if($scope.reviewerIDlist.indexOf(revID) < 0) {
          $scope.reviewerIDlist.push(revID);
          $scope.reviewerlist.push({id: revID, name: data[i].reviewers[revID]});
        }
      }
    }
  }
  $scope.go=function(tag){
     $location.url("/Tasks/General?id="+tag);
  }

  $scope.svnbutton=function()
  {
    TasksServices.updatesvn(function(response){
    }, function(response){
    });
  }

  $scope.action();
}]);
