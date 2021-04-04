app.controller('ReviewsController', ['$scope',  'ReviewsServices', function($scope, ReviewsServices){
  $scope.action=function(){
    ReviewsServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
  $scope.data = [];
  $scope.authorlist=new Array();
  $scope.yearlist=new Array();
  $scope.countrylist=new Array();
  $scope.grouplist=new Array();
  $scope.gr="0";
  $scope.co="0";
  $scope.au="0";
  $scope.ye="0";
  $scope.isAdmin = false;
  $scope.myOrderBy="author";

  $scope.show = function(data)
  {
    $scope.isAdmin = data.isAdmin;
    $scope.data = data.result;

    $scope.authorlist = [];
    $scope.yearlist = [];
    $scope.countrylist = [];
    $scope.grouplist = [];

    for(var i=0; i < $scope.data.length; i++)
    {
        var item = $scope.data[i];
        if($scope.authorlist.indexOf(item.author) == -1) {
            $scope.authorlist.push(item.author);
        }
        if($scope.yearlist.indexOf(item.year) == -1) {
            $scope.yearlist.push(item.year);
        }
        if($scope.countrylist.indexOf(item.country) == -1) {
            $scope.countrylist.push(item.country);
        }
        if($scope.grouplist.indexOf(item.group) == -1) {
            $scope.grouplist.push(item.group);
        }
    }
  }

  $scope.autoAssigned = false;

  $scope.autoassign = function() {
    $scope.autoAssigned = null;
    ReviewsServices.autoassign(function(response) {
      $scope.autoAssigned = response.data.assignments.length;
      }, function(response) {
      $scope.autoAssigned = 'error';
      });
  };

//myOrderBy au ye co gr
  $scope.action();
}]);
