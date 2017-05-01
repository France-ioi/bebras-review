app.controller('TasksController', ['$scope', '$location', '$sce', 'TasksServices', function($scope, $location, $sce, TasksServices){
  $scope.getTasks=function(initial) {
    TasksServices.getData('tasks', function(response){
      $scope.loadData(response.data, initial);
    }, function(response){
      $scope.loadData("Failed", initial); // TODO :: replace with an actual message
    });
  }
  $scope.getAll=function(){
    TasksServices.getData('All', function(response){
      $scope.showAll(response.data);
    }, function(response){
      $scope.showAll("Failed");
    });
  }
  $scope.getTasklist=function(){
    TasklistServices.getData(function(response){
      $scope.list(response.data);
    }, function(response){
      $scope.list("Failed List");
    });
  }

  $scope.listMode = true;
  $scope.curView = 'general';
  $scope.sel = null;
  $scope.taskData = null;

  $scope.tasksList = [];
  $scope.groupsList = [];
  $scope.reviewsList = [];
  $scope.messagesList = [];
  $scope.flag = 0; // TODO :: ???
  $scope.isAdmin = false;

  // Filters
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

  // Files views
  $scope.toggleflag = false;
  $scope.loadTasks = false;
  $scope.autoloadTasks = false;
  $scope.httpflag = false;

  // For reviews
  $scope.hasReviews = {};

  $scope.select = function(newSel) {
    // Select a task
    $scope.reviewChanged();
    $scope.taskData = null;
    if(newSel) {
      $scope.sel = newSel;
      for(i=0; i < $scope.tasksList.length; i++) {
        if($scope.tasksList[i].folderName == newSel) {
          $scope.taskData = $scope.tasksList[i];
          for(var i=0; i<$scope.reviewsList.length; i++) {
            var curReview = $scope.reviewsList[i];
            if(curReview.isMine && $scope.taskData.folderName == curReview.folderName) {
              $scope.taskData.currentRating = curReview.currentRating;
              $scope.taskData.potentialRating = curReview.potentialRating;
              $scope.taskData.reviewId = curReview.ID;
              $scope.taskData.reviewComment = curReview.comment;
              $scope.taskData.isAssigned = curReview.isAssigned;
              $scope.taskData.isPublished = curReview.isPublished;
            }
          }
          break;
        }
      }
      if(!$scope.taskData) {
        // Task not found
        $scope.sel = null;
      }
    } else {
      $scope.sel = null;
    }

    $scope.listMode = !$scope.sel;
  }

  $scope.goView = function(newView) {
    $scope.listMode = false;
    $scope.loadTasks = $scope.autoloadTasks;
    $scope.curView = newView;
  }

  $scope.loadData = function(data, initial)
  {
    var tasksList = data.tasksList;
    $scope.tasksList = tasksList;
    $scope.groupsList = data.groupsList;
    $scope.reviewsList = data.reviewsList;
    $scope.messagesList = data.messagesList;
    $scope.autoloadTasks = data.autoloadTasks == 'true';
    $scope.isAdmin = data.isAdmin;
    var i,j;
    var length=0;

    // TODO :: better logic to make the lists...
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].year==tasksList[j].year)
          break;
      if(i==j)
        $scope.yearlist[length++]=tasksList[i].year;
    }

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].country==tasksList[j].country)
          break;
      if(i==j)
        $scope.countrylist[length++]=tasksList[i].country;
    }

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].ownerName==tasksList[j].ownerName)
          break;
      if(i==j)
        $scope.ownerlist[length++]=tasksList[i].ownerName;
      // TODO :: use IDs for filtering, name for display
    }

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].Group==tasksList[j].Group)
          break;
      if(i==j)
        $scope.grouplist[length++]=tasksList[i].Group;
    }

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].status==tasksList[j].status)
          break;
      if(i==j)
        $scope.statuslist[length++]=tasksList[i].status;
    }

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(var revID in tasksList[i].reviewers) {
        if($scope.reviewerIDlist.indexOf(revID) < 0) {
          $scope.reviewerIDlist.push(revID);
          $scope.reviewerlist.push({id: revID, name: tasksList[i].reviewers[revID]});
        }
      }
    }

    $scope.httpflag = (data.localCheckoutFolder.substring(0,4) == 'http');

    $scope.hasReviews = {};
    for(var i=0; i<$scope.reviewsList.length; i++) {
      if($scope.reviewsList[i].isMine) {
        $scope.hasReviews[$scope.reviewsList[i].folderName] = true;
      }
    }

    if(initial) {
      // First load of the page
      var searchObject = $location.search();
      if(searchObject['id']) {
        $scope.select(searchObject['id']);
      } else {
        $scope.select(null);
      }
    } else {
      $scope.select($scope.sel);
    }
  }

  $scope.trust = function(url)
  {
    return $sce.trustAsResourceUrl(url);
  }

  $scope.updateSvn = function()
  {
    TasksServices.updatesvn(function(response){
    }, function(response){
    });
  }

  $scope.reviewChanged = function () {
    $('#reviewSaveBtn').text('Save').removeClass('btn-success').addClass('btn-primary');
  };

  $scope.resetReview = function(id) {
    $scope.chgReview(id, -1, -1, '');
  };

  $scope.chgReview = function(id,a,b ,comment)
  {
    var btn = $('#reviewSaveBtn');
    btn.stop(true, true);
    btn.removeClass('btn-success').addClass('btn-primary');
    btn.text('Saving...');
    TasksServices.reviewchange(id, a,b,comment, function(response) {
      btn.text('Saved!');
      btn.removeClass('btn-primary').addClass('btn-success');
      $scope.getTasks();
    }, function(response){
    });
  }

  $scope.createReview = function(folderName) {
    TasksServices.reviewcreate(folderName,
        function(response) {
          // TODO :: do not reload everything (temporary)
          $scope.getTasks();
          },
        function(response){});
  }

  $scope.messagesend = function()
  {
    var mess = $('#newMessage').val();
    TasksServices.sendmess($scope.sel, mess, function(response){
      $('#newMessage').val('');
      $scope.getTasks();
      }, function () {});
  }

  $scope.changemess = function(a,b)
  {
    TasksServices.changemess(a,b,function(response){
      $scope.getTasks();
    }, function(response){
    });
  }

  $scope.lastsave = function(data) {
    TasksServices.lastsave(data, function(response){
      $scope.loadData(response.data);
    },function(response){
    });
  }

  $scope.loadchange = function () {
    $scope.loadTasks = true;
  }

  $scope.$watch('sel', $scope.select);

  $scope.getTasks(true);
}]);

/*
    Code to check whether the task name is valid (why?)
    var year=$scope.sel.substring(0,4);
    $scope.check=$scope.check&&!isNaN(year);
    var symbol=$scope.sel.substring(4,5);
    $scope.check=$scope.check&&(symbol=='-');
    var countrycode=$scope.sel.substring(5,7);
    $scope.check=$scope.check&&(isNaN(countrycode));
    symbol=$scope.sel.substring(7,8);
    $scope.check=$scope.check&&(symbol=='-');
    var number=$scope.sel.substring(8,10);
    $scope.check=$scope.check&&(!isNaN(number));
  }
*/
