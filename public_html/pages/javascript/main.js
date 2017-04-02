/**
 * Created by David on 2017-03-25.
 */
var evnApp = angular.module('evnApp', ['ngResource','ui.bootstrap']);

/**
 * Custom Filter
 */
evnApp.filter('notInArray', function($filter){
    return function(list, arrayFilter, element){
        if(arrayFilter){
            return $filter("filter")(list, function(listItem){
                return arrayFilter.indexOf(listItem[element]) == -1;
            });
        }
    };
});

/**
 * Event Table Controller
 */
evnApp.controller('RootCtrl', function RootCtrl($scope, $http) {
    /**
     * Initializations
     */
    $scope.destinations = [];
    $scope.events = [];

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
    /**
     * Returns the css class for the event priority
     * @param $eventPriority
     * @returns {*}
     */
    $scope.getPriorityClass = function ($eventPriority) {
        if ($scope.priorityData.length > $eventPriority && $eventPriority >= 0) {
            return $scope.priorityData[$eventPriority].cssClass;
        }
        return '';
    };

    /**
     * Returns the destination name given the destination id
     * @param $id
     * @returns {string}
     */
    $scope.getDestinationName = function(id) {
        console.log(id);
        for (var i=0; i < $scope.destinations.length; i++) {
            if ($scope.destinations[i].id == id) {
                return $scope.destinations[i].detail.name;
            }
        }
        return 'Unknown Destination (' + id + ')';
    };

    /**
     * HTTP calls
     */
    $http.get('/adminApi/getEvents')
        .then(function(response) {
            $scope.events = response.data.data;
        });

    $http.get('/adminApi/getDestinations')
        .then(function(response) {
            $scope.destinations = response.data.data;
        });
});

/**
 * Event Table Controller
 */
evnApp.controller('EvntTblCtrl', function EvntTblCtrl($scope, $http) {
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
        $scope, $http) {
    /**
     * Initializations
     */
    var defaultEvent = {
        detail: {
            imageURL: 'https://eventsnanaimo.com/img/placeholder.png',
        }
    };
    $scope.event = defaultEvent;
    $scope.state = {
        startCalOpen: false,
        endCalOpen: false
    };

    $scope.priorityCssClass = 'btn btn-primary';

    /**
     * Called when the Event is set
     */
    $scope.$on('eventSelect', function(event, selectedEvent) {
        $scope.event = selectedEvent;
        $scope.startDate = selectedEvent.unixStartTime * 1000;
        $scope.endDate = selectedEvent.unixEndTime * 1000;
        $scope.priorityCssClass = $scope.$parent.getPriorityClass(selectedEvent.priority);
    });

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

    /**
     * Called to update the priority display class
     */
    $scope.updateClass = function() {
        $scope.priorityCssClass = $scope.$parent.getPriorityClass($scope.event.priority);
    };

    /**
     * Event Destination Methods
     */
    /**
     * Removes the Destination
     */
    $scope.removeDestFromEvent = function(id) {
        var index = $scope.event.destinations.indexOf(id);
        if (index > -1) {
            $scope.event.destinations.splice(index,1);
        }
    };

    /**
     * Adds the Destination
     */
    $scope.addDestToEvent = function(id) {
        var index = $scope.event.destinations.indexOf(id);
        if (index == -1) {
            $scope.event.destinations.push(id);
        }
    };

    /**
     * Adds the Destination
     */
    $scope.getUnselectedDestinations = function() {
        var selectedDestinations = $scope.event.destinations;
        var allDestinations = $scope.destinations;
        var difference = [];

        jQuery.grep(allDestinations, function(el) {
            if (jQuery.inArray(el, selectedDestinations) == -1) difference.push(el);
        });
        return difference;
    };
});