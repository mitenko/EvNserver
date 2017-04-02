/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp', ['ngResource','ui.bootstrap']);

/**
 * Event Table Controller
 */
evnApp.controller('RootCtrl', function RootCtrl($scope, $http) {
    /**
     * Root Vars
     */
    var priorityData = new Array();
    priorityData[0] = {value:0, text:'Ultra', cssClass:'btn btn-danger'}
    priorityData[1] = {value:1, text:'High', cssClass:'btn btn-warning'}
    priorityData[2] = {value:2, text:'Medium', cssClass:'btn btn-success'}
    priorityData[3] = {value:3, text:'Low', cssClass:'btn btn-primary'}
    $scope.priorityData = priorityData;

    /**
     * Root Functions
     */
    $scope.getPriorityClass = function ($eventPriority) {
        if ($scope.priorityData.length > $eventPriority && $eventPriority >= 0) {
            return $scope.priorityData[$eventPriority].cssClass;
        }
        return '';
    }
});

/**
 * Event Table Controller
 */
evnApp.controller('EvntTblCtrl', function EvntTblCtrl($scope, $http) {
    $scope.events = [];

    $http.get('/adminApi/getEvents')
        .then(function(response) {
            $scope.events = response.data.data;
    });

    $scope.editEvent = function(event) {
        $scope.$parent.$broadcast('eventSelect', event);
        // Make the Events tab active for return navigation
        $(".nav-tabs").find("li").removeClass("active");
    };
});

/**
 * Edit Event Controller
 */
evnApp.controller('EditEvntCtrl', function EvntEvntCtrl(
        $scope, $http, $filter) {
    /**
     * Calendar Picker
     */
    /**
     * Click event to open the start date calendar popup
     */
    $scope.openStartCal = function() {
        $scope.state.startCalOpen = true;
    };
    $scope.openEndCal = function() {
        $scope.state.endCalOpen = true;
    };

    /**
     * Called when the Start Date has changed
     */
    $scope.startDateChange = function() {
        $scope.event.unixStartTime = new Date($scope.startDate).getTime()/1000;
    };
    $scope.endDateChange = function() {
        $scope.event.unixEndTime = new Date($scope.endDate).getTime()/1000;
    };

    /**
     * Called when the Start Time has changed
     */
    $scope.startTimeChange = function() {
        $scope.event.unixStartTime = new Date($scope.startDate).getTime()/1000;
    };
    $scope.endTimeChange = function() {
        $scope.event.unixEndTime = new Date($scope.endDate).getTime()/1000;
    };

    $scope.state = {
        startCalOpen: false,
        endCalOpen: false
    };

    $scope.priorityCssClass = 'btn btn-primary';

    /**
     * Called when the Event is set
     */
    $scope.$on('eventSelect', function(event, selectedEvent) {
        console.log('Event Select Event');
        $scope.event = selectedEvent;
        $scope.startDate = selectedEvent.unixStartTime * 1000;
        $scope.endDate = selectedEvent.unixEndTime * 1000;
        $scope.priorityCssClass = $scope.$parent.getPriorityClass(selectedEvent.priority);
    });

    /**
     * Called to update the priority display class
     */
    $scope.updateClass = function() {
        $scope.priorityCssClass = $scope.$parent.getPriorityClass($scope.event.priority);
    };
});