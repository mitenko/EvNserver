/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp', ['ngResource','ui.bootstrap']);

/**
 * Global Vars
 */
var priorityData = new Array();
priorityData[0] = {value:0, text:'Ultra', cssClass:'btn btn-danger'}
priorityData[1] = {value:1, text:'High', cssClass:'btn btn-warning'}
priorityData[2] = {value:2, text:'Medium', cssClass:'btn btn-success'}
priorityData[3] = {value:3, text:'Low', cssClass:'btn btn-primary'}
evnApp.constant('priorityData', priorityData);

/**
 * Global Functions
 */
evnApp.run(function($rootScope) {
    $rootScope.getPriorityClass = function ($eventPriority) {
        if (priorityData.length > $eventPriority && $eventPriority >= 0) {
            return priorityData[$eventPriority].cssClass;
        }
        return '';
    }
});

/**
 * Event Table Controller
 */
evnApp.controller('EvntTblCtrl', function EvntTblCtrl($scope, $http, $rootScope) {
    $scope.events = [];

    $http.get('/adminApi/getEvents')
        .then(function(response) {
            $scope.events = response.data.data;
    });

    $scope.editEvent = function(event) {
        $rootScope.$broadcast('eventSelect', event);
    };
});

/**
 * Edit Event Controller
 */
evnApp.controller('EditEvntCtrl', function EvntEvntCtrl(
        $scope, $http, $rootScope, priorityData) {
    /**
     * Calendar Picker Configurations
     */
    $scope.today = function() {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function() {
        $scope.dt = null;
    };

    $scope.inlineOptions = {
        customClass: getDayClass,
        minDate: new Date(),
        showWeeks: true
    };

    $scope.dateOptions = {
        dateDisabled: disabled,
        formatYear: 'yy',
        maxDate: new Date(2020, 5, 22),
        minDate: new Date(),
        startingDay: 1
    };

    // Disable weekend selection
    function disabled(data) {
        var date = data.date,
            mode = data.mode;
        return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.toggleMin = function() {
        $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
        $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
    };

    $scope.toggleMin();

    $scope.open1 = function() {
        console.log('open1 click');
        $scope.popup1.opened = true;
    };

    $scope.open2 = function() {
        $scope.popup2.opened = true;
    };

    $scope.setDate = function(year, month, day) {
        $scope.dt = new Date(year, month, day);
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];
    $scope.altInputFormats = ['M!/d!/yyyy'];

    $scope.popup1 = {
        opened: true
    };

    $scope.popup2 = {
        opened: false
    };

    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    var afterTomorrow = new Date();
    afterTomorrow.setDate(tomorrow.getDate() + 1);
    $scope.events = [
        {
            date: tomorrow,
            status: 'full'
        },
        {
            date: afterTomorrow,
            status: 'partially'
        }
    ];

    function getDayClass(data) {
        var date = data.date,
            mode = data.mode;
        if (mode === 'day') {
            var dayToCheck = new Date(date).setHours(0,0,0,0);

            for (var i = 0; i < $scope.events.length; i++) {
                var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

                if (dayToCheck === currentDay) {
                    return $scope.events[i].status;
                }
            }
        }

        return '';
    }

    console.log($scope.popup1.opened);

    $scope.priorityData = priorityData;
    $scope.priorityCssClass = 'btn btn-primary';

    $scope.$on('eventSelect', function(event, selectedEvent) {
        $scope.event = selectedEvent;
        $scope.priorityCssClass = $rootScope.getPriorityClass(selectedEvent.priority);
    });

    $scope.updateClass = function() {
        $scope.priorityCssClass = $rootScope.getPriorityClass($scope.event.priority);
    };
});