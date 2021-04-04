app.controller('PresentationController', ['$scope', '$location', 'PresentationServices', function($scope, $location, PresentationServices){
  $scope.action=function(){
    PresentationServices.action(function(response){
      $scope.show(response.data);
    }, function(response){
      $scope.show("Failed");
    });
  }
  $scope.data="loading";
  $scope.show = function(data)
  {
    $scope.data=data;
    if(data.profile.nbReviewsDesired == '0') {
        $('#desiredMissingModal').modal();
    }
  }

  $scope.action();

  $scope.goToProfile = function() {
    $location.path('/Profile');
  };
}]);
