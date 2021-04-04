app.directive('starRating', function () {
    return {
        restrict: 'A',
        template: '<ul class="rating">' +
            '<li ng-repeat="star in stars" ng-class="star" ng-click="toggle($index)">' +
            '\u2605' +
            '</li>' +
            '</ul>',
        scope: {
            ratingValue: '=',
            ratingReadonly: '='
        },
        link: function (scope, elem, attrs) {

            var updateStars = function () {
                scope.stars = [];
                for (var i = 0; i < 6; i++) {
                    scope.stars.push({
                        filled: i < scope.ratingValue
                    });
                }
            };

            scope.toggle = function (index) {
                if(!scope.ratingReadonly) {
                    scope.ratingValue = index + 1;
                    updateStars();
                }
            };

            scope.$watch('ratingValue', function (oldVal, newVal) {
                if (newVal) {
                    updateStars();
                }
            });

            updateStars();
        }
    }
});


app.filter('reviewsDesired', function() {
    return function(value) {
        if(value === 0) {
            return '[unset]';
        } else if(value == -1) {
            return '0';
        } else if(value == -2) {
            return '10-15';
        } else if(value == -3) {
            return '∞';
        } else {
            return value;
        }
    };
});
