app.controller('PDFController', ['$scope','$sce', '$location', 'PDFServices', 'TasklistServices', function($scope,$sce, $location, PDFServices, TasklistServices){
  $scope.action=function(){
    PDFServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
  $scope.tasklist=function(){
    TasklistServices.action(function(response){
      $scope.list(response.data);
    }, function(response){
      $scope.list("Failed List");
    });
  }

  $scope.tasklists="List";
  $scope.data="loading";

  $scope.httpflag="false";
  $scope.show = function(data)
  {
    $scope.data=data;
    var searchObject = $location.search();
    if(searchObject['id']==null)
      $scope.sel='0';
    else
      $scope.sel=searchObject['id'];
    $scope.httpflag=($scope.data.localCheckoutFolder.substring(0,4)=='http');
  }
  $scope.list = function(data)
  {
    $scope.tasklists=data;
  }
  $scope.tasklist();
  $scope.action();
  $scope.flag=0;
  $scope.toggleflag=true;

  $scope.trust = function(url)
  {
    return $sce.trustAsResourceUrl(url);
  }
  
}]);