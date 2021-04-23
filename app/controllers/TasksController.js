app.controller('TasksController', ['$scope', '$location', '$routeParams', '$sce', 'TasksServices', function($scope, $location, $routeParams, $sce, TasksServices){
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

  // Admin buttons
  $scope.svnInProgress = false;
  $scope.svnMessage = '';
  $scope.svnMsgClass = '';

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
  $scope.isRejected = false;

  // Files views
  $scope.toggleflag = false;
  $scope.loadTasks = false;
  $scope.autoloadTasks = false;
  $scope.httpflag = false;

  // For reviews
  $scope.hasReviews = {};

  $scope.doneReviews = [];

  $scope.select = function(newSel) {
    // Select a task
    $scope.reviewChanged();
    $scope.taskData = null;
    if(newSel) {
      $scope.sel = newSel;
      $scope.doneReviews = [];
      for(i=0; i < $scope.tasksList.length; i++) {
        if($scope.tasksList[i].folderName == newSel || $scope.tasksList[i].textID == newSel) {
          $scope.sel = $scope.tasksList[i].textID;
          $scope.taskData = $scope.tasksList[i];
          for(var i=0; i<$scope.reviewsList.length; i++) {
            var curReview = $scope.reviewsList[i];
            if($scope.taskData.folderName != curReview.folderName) { continue; }

            if(curReview.isPublished) {
              $scope.doneReviews.push(curReview);
            }

            if(curReview.isMine) {
              $scope.taskData.currentRating = curReview.currentRating;
              $scope.taskData.potentialRating = curReview.potentialRating;
              $scope.taskData.itsInformatics = curReview.itsInformatics;
              $scope.taskData.ageDifficulty = curReview.ageDifficulty;
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

    if($scope.sel) {
        $location.path('/Tasks/' + $scope.sel, false);
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

    tasksList.sort(function(a, b) { return a.textID < b.textID ? -1 : 1; });

    // TODO :: better logic to make the lists...
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].year==tasksList[j].year)
          break;
      if(i==j)
        $scope.yearlist[length++]=tasksList[i].year;
    }
    $scope.yearlist.sort();

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].country==tasksList[j].country)
          break;
      if(i==j)
        $scope.countrylist[length++]=tasksList[i].country;
    }
    $scope.countrylist.sort();

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
    $scope.ownerlist.sort();


    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].Group==tasksList[j].Group)
          break;
      if(i==j)
        $scope.grouplist[length++]=tasksList[i].Group;
    }
    $scope.grouplist.sort();

    length=0;
    for(i=0;i<tasksList.length;i++)
    {
      for(j=0;j<i;j++)
        if(tasksList[i].status==tasksList[j].status)
          break;
      if(i==j)
        $scope.statuslist[length++]=tasksList[i].status;
    }
    $scope.statuslist.sort();

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
    $scope.reviewerlist.sort();

    $scope.httpflag = (data.localCheckoutFolder.substring(0,4) == 'http');

    $scope.hasReviews = {};
    for(var i=0; i<$scope.reviewsList.length; i++) {
      if($scope.reviewsList[i].isMine) {
        $scope.hasReviews[$scope.reviewsList[i].textID] = true;
      }
    }

    $scope.isRejected = {};
    for(var i = 0; i < data.rejected.length; i++) {
        $scope.isRejected[data.rejected[i]['taskID']] = true;
    };

    if(initial) {
      // First load of the page
      if($routeParams['taskId']) {
        $scope.select($routeParams['taskId']);
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
    $scope.svnInProgress = true;
    $scope.svnMessage = 'Import from SVN in progress...'
    $scope.svnMsgClass = 'alert-info';
    TasksServices.updatesvn(
      function(data) {
        $scope.svnMessage = '<b>' + data.data + ' tasks updated successfully!</b>';
        $scope.svnMsgClass = 'alert-success';
        $scope.svnInProgress = false;
      },
      function(data) {
        $scope.svnMessage = 'Error.';
        $scope.svnMsgClass = 'alert-danger';
        $scope.svnInProgress = false;
      });
  }

  $scope.commitSvn = function()
  {
    $scope.svnInProgress = true;
    $scope.svnMessage = 'Exporting reviews to SVN...';
    $scope.svnMsgClass = 'alert-info';
    TasksServices.commitsvn(
      function(data) {
        data = data.data;
        var msg = '';
        if(data.result) {
          msg = '<b>Export to SVN successful!</b> ' + data.modified.length + ' tasks had their reviews updated.';
          $scope.svnMsgClass = 'alert-success';
        } else {
          msg = '<b>Export to SVN unsuccessful.</b> ' + data.modified.length + ' tasks had their reviews locally updated, but not committed yet.';
          $scope.svnMsgClass = 'alert-warning';
        }
        if(data.nonexist.length) {
          msg += "<br /><b>Tasks which don't exist anymore in SVN:</b> " + data.nonexist.join(', ');
        }
        msg += "<br /><b>Tasks updated:</b> " + data.modified.join(', ');
        $scope.svnMessage = $sce.trustAsHtml(msg);
        $scope.svnInProgress = false;
      },
      function(data) {
        $scope.svnMessage = 'Error.';
        $scope.svnMsgClass = 'alert-danger';
        $scope.svnInProgress = false;
      });
  }

  $scope.reviewChanged = function () {
    $('#reviewSaveBtn').text('Save').removeClass('btn-success').addClass('btn-primary');
  };

  $scope.askedReset = false;
  $scope.askReset = function() {
    $scope.askedReset = true;
  };
  $scope.cancelReset = function() {
    $scope.askedReset = false;
  };

  $scope.resetReview = function(id) {
    $scope.askedReset = false;
    $scope.chgReview(id, {
      currentRating: -1,
      potentialRating: -1,
      itsInformatics: 'none',
      ageDifficulty: 'none',
      reviewComment: ''});
  };

  $scope.saveReview = function(taskData) {
    $scope.chgReview(taskData.reviewId, taskData);
  };

  $scope.chgReview = function(id, reviewData)
  {
    var btn = $('#reviewSaveBtn');
    btn.stop(true, true);
    btn.removeClass('btn-success').addClass('btn-primary');
    btn.text('Saving...');
    TasksServices.reviewchange(id, reviewData, function(response) {
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


  $scope.askedReject = false;
  $scope.reject = function(type, rejectReason) {
    switch(type) {
      case 'ask':
        $scope.askedReject = true;
        break;
      case 'cancel':
        $scope.askedReject = false;
        break;
      case 'confirm':
        var taskID = $scope.taskData.ID;
        if(!taskID || !rejectReason) { return; }
        TasksServices.reject({taskID: taskID, reason: rejectReason}, function() {
            $scope.getTasks();
        }, function() {
            $scope.getTasks();
        });
        break;
    }
  };
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
